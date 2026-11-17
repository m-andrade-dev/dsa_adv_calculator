<?php
/**
 * api.php - PHP bridge between the web UI and the Java calculation engine.
 * Receives JSON {expr, vars}, validates it, runs the compiled Java class
 * (formula sent through stdin, so there is no shell-injection or encoding issue),
 * and returns the engine's JSON answer.
 */
header('Content-Type: application/json; charset=utf-8');

const JAVA_BIN  = 'java';                       // e.g. 'C:\\Program Files\\Java\\jdk-21\\bin\\java.exe' if not on PATH
const JAVAC_BIN = 'javac';
const CLASS_DIR = __DIR__ . '/java/bin';
const TIMEOUT_S = 10;

function fail(string $msg): never {
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

// Auto-compile AdvancedCalculator.java if not yet compiled
$classDir = is_dir(CLASS_DIR) ? CLASS_DIR : __DIR__;
if (!file_exists($classDir . '/AdvancedCalculator.class') && !file_exists(__DIR__ . '/AdvancedCalculator.class')) {
    $srcFile = file_exists(__DIR__ . '/AdvancedCalculator.java')
        ? __DIR__ . '/AdvancedCalculator.java'
        : __DIR__ . '/java/src/AdvancedCalculator.java';
    if (file_exists($srcFile)) {
        if (!is_dir(CLASS_DIR)) @mkdir(CLASS_DIR, 0777, true);
        $targetDir = is_dir(CLASS_DIR) ? CLASS_DIR : __DIR__;
        $compileCmd = [JAVAC_BIN, '-encoding', 'UTF-8', '-d', $targetDir, $srcFile];
        $cProc = @proc_open($compileCmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $cPipes);
        if (is_resource($cProc)) {
            fclose($cPipes[0]); fclose($cPipes[1]); fclose($cPipes[2]);
            proc_close($cProc);
        }
    }
}

$activeClassDir = file_exists(CLASS_DIR . '/AdvancedCalculator.class') ? CLASS_DIR : __DIR__;

$in = json_decode(file_get_contents('php://input'), true);
if (!is_array($in) || !isset($in['expr'])) fail('Invalid request.');

$expr = trim(str_replace(["\r", "\n"], ' ', (string)$in['expr']));
if ($expr === '') fail('Please enter a formula.');
if (mb_strlen($expr) > 300) fail('Formula is too long (max 300 characters).');

$lines = [$expr];
foreach (($in['vars'] ?? []) as $name => $value) {
    $name = (string)$name; $value = trim((string)$value);
    if ($value === '') continue;
    if (!preg_match('/^[A-Za-z]$/', $name)) fail('Variable names must be a single letter.');
    if (!preg_match('/^-?\d+(\.\d+)?$/', $value)) fail("Value of $name must be a number.");
    $lines[] = "$name=$value";
}

$cmd  = [JAVA_BIN, '-Dfile.encoding=UTF-8', '-cp', $activeClassDir, 'AdvancedCalculator'];
$proc = @proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
if (!is_resource($proc)) fail('Could not start Java. Check JAVA_BIN in api.php.');

fwrite($pipes[0], implode("\n", $lines) . "\n");
fclose($pipes[0]);
stream_set_blocking($pipes[1], false);
stream_set_blocking($pipes[2], false);

$out = ''; $err = ''; $start = microtime(true);
while (true) {
    $out .= stream_get_contents($pipes[1]);
    $err .= stream_get_contents($pipes[2]);
    if (!proc_get_status($proc)['running']) break;
    if (microtime(true) - $start > TIMEOUT_S) {
        proc_terminate($proc);
        fail('Calculation timed out.');
    }
    usleep(5000);
}
$out .= stream_get_contents($pipes[1]);
$err .= stream_get_contents($pipes[2]);
fclose($pipes[1]); fclose($pipes[2]); proc_close($proc);

$decoded = json_decode(trim($out), true);
if (!is_array($decoded)) {
    fail('Java engine error. Did you compile it? See README. ' . substr(trim($err), 0, 200));
}
echo json_encode($decoded);
