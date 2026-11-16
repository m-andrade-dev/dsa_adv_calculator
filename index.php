<?php
// Button definitions: [label shown, text inserted into the formula]
$fnKeys = [
    ['⌊x⌋', 'floor('], ['⌈x⌉', 'ceil('], ['%', '%'], ['//', '//'], ['xʸ', '^'],
    ['√x', '√('], ['∛x', '∛('], ['log x', 'log('], ['log₂ x', 'log2('], ['ln x', 'ln('],
    ['n!', '!'], ['a!+b!', '5!+3!'], ['a!/b!', '6!/4!'], ['to', ' to '], ['=', '='],
    ['Σ', 'Σ(i=1 to 5) i'], ['ΣΣ', 'ΣΣ(i=1 to 3, j=1 to 3) i*j'],
    ['Π', 'Π(i=1 to 4) i'], ['ΠΠ', 'ΠΠ(i=1 to 3, j=1 to 3) i*j'], [',', ','],
];
$padKeys = [
    ['7','7',null], ['8','8',null], ['9','9',null], ['⌫',null,'back'], ['AC',null,'clr'],
    ['4','4',null], ['5','5',null], ['6','6',null], ['×','×',null],    ['÷','÷',null],
    ['1','1',null], ['2','2',null], ['3','3',null], ['+','+',null],    ['−','-',null],
    ['0','0',null], ['.','.',null], ['(','(',null], [')',')',null],    ['=',null,'eq'],
];
$variables = ['A', 'B', 'C', 'D', 'x', 'y', 'c'];
$h = fn($s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DSA Advanced Calculator</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=JetBrains+Mono:wght@500;600;700&family=Orbitron:wght@600;700&display=swap" rel="stylesheet">
<style>
  /* ================= LIGHT MODE: CUTESY Y2K K-POP IDOL (Gehlee) ================= */
  :root {
    --bg: #ffd6ec;
    --bg-pattern:
      radial-gradient(circle at 15% 15%, rgba(255, 255, 255, 0.85) 0%, transparent 32%),
      radial-gradient(circle at 85% 20%, rgba(212, 187, 255, 0.65) 0%, transparent 38%),
      radial-gradient(circle at 50% 85%, rgba(255, 179, 217, 0.7) 0%, transparent 45%),
      linear-gradient(135deg, #ffe6f2 0%, #ffc2e2 48%, #f3c6ff 100%);
    --bg-img: url('Assets/Gehlee.png');
    --card: rgba(255, 246, 251, 0.92);
    --card-border: #ff85c0;
    --card-outer-glow: 0 18px 45px rgba(255, 77, 166, 0.25), 0 0 0 4px #ffffff, 0 0 0 7px #ffb3d9;
    --text: #5c1638;
    --muted: #b5487f;
    --line: #ffadd2;
    --btn: linear-gradient(180deg, #ffffff 0%, #ffe0f0 55%, #ffc2e0 100%);
    --btn-fn: linear-gradient(180deg, #ffffff 0%, #f4e8ff 55%, #e2c7ff 100%);
    --btn-shadow: 0 5px 0 #f062a6, 0 7px 14px rgba(255, 77, 166, 0.24), inset 0 2px 1px #ffffff;
    --btn-active-shadow: 0 1px 0 #f062a6, 0 2px 6px rgba(255, 77, 166, 0.2);
    --btn-radius: 999px;
    --accent: linear-gradient(135deg, #ff70b8 0%, #ff3399 55%, #c44dff 100%);
    --accent-solid: #ff3399;
    --accent-shadow: 0 5px 0 #b80062, 0 8px 18px rgba(255, 51, 153, 0.4), inset 0 2px 2px rgba(255,255,255,0.6);
    --accent2: linear-gradient(135deg, #ff85a2 0%, #ff477e 100%);
    --on-accent: #ffffff;
    --err: #d81b60;
    --font-main: 'Fredoka', system-ui, Segoe UI, sans-serif;
    --font-title: 'Fredoka', system-ui, sans-serif;
    --input-bg: #ffffff;
    --ans-bg: linear-gradient(135deg, #fff0f7 0%, #f7e8ff 100%);
    --badge-bg: linear-gradient(90deg, #ff66b3, #b366ff);
    --badge-text: #ffffff;
  }

  /* ================= DARK MODE: COOL SLEEK DARK (Jeo Ong) ================= */
  [data-theme="dark"] {
    --bg: #0b111a;
    --bg-pattern: radial-gradient(circle at 80% 20%, #16263b 0%, #091019 60%, #05080d 100%);
    --bg-img: url('Assets/Jeo.png');
    --card: rgba(10, 22, 36, 0.94);
    --card-border: #00e5ff;
    --card-outer-glow: 0 16px 45px rgba(0, 0, 0, 0.65), 0 0 20px rgba(0, 229, 255, 0.18), 0 0 0 1px #00e5ff;
    --text: #e2f1ff;
    --muted: #6892b8;
    --line: #1e3a56;
    --btn: linear-gradient(180deg, #162a40 0%, #0d1a29 100%);
    --btn-fn: linear-gradient(180deg, #1b3552 0%, #102133 100%);
    --btn-shadow: 0 4px 0 #060d14, 0 0 10px rgba(0, 229, 255, 0.14);
    --btn-active-shadow: 0 1px 0 #060d14, 0 0 16px rgba(0, 229, 255, 0.45);
    --btn-radius: 8px;
    --accent: linear-gradient(180deg, #00e5ff 0%, #0099cc 100%);
    --accent-solid: #00e5ff;
    --accent-shadow: 0 4px 0 #005f80, 0 0 16px rgba(0, 229, 255, 0.5);
    --accent2: linear-gradient(180deg, #3a7bd5 0%, #1e4d91 100%);
    --on-accent: #04101c;
    --err: #ff5252;
    --font-main: 'JetBrains Mono', Consolas, monospace;
    --font-title: 'Orbitron', 'JetBrains Mono', sans-serif;
    --input-bg: #07121e;
    --ans-bg: #0c1d30;
    --badge-bg: linear-gradient(90deg, #0088cc, #00e5ff);
    --badge-text: #04101c;
  }

  * { box-sizing: border-box; }
  body {
    margin: 0;
    background-color: var(--bg);
    background-image: var(--bg-pattern);
    color: var(--text);
    font-family: var(--font-main);
    min-height: 100vh;
    padding: 20px;
    transition: background 0.35s ease, color 0.3s ease;
    overflow-x: hidden;
  }

  body::before {
    content: "✦ ★ ♡ UNIVERSE PHOTOCARD ★ ✦";
    position: fixed;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 0.72rem;
    letter-spacing: 0.28em;
    color: var(--muted);
    opacity: 0.65;
    pointer-events: none;
    white-space: nowrap;
    font-weight: 700;
  }
  [data-theme="dark"] body::before {
    content: "";
  }

  .layout { display: flex; justify-content: center; align-items: flex-start; position: relative; z-index: 1; margin-top: 12px; }
  .mascot {
    position: fixed;
    bottom: 0;
    left: 12px;
    width: min(370px, calc((100vw - 640px) / 2 - 10px));
    aspect-ratio: 1 / 1.1;
    background: var(--bg-img) center bottom / contain no-repeat;
    pointer-events: none;
    filter: drop-shadow(0 10px 24px rgba(255, 51, 153, 0.32));
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease, filter 0.35s ease;
  }
  [data-theme="dark"] .mascot {
    left: auto;
    right: 12px;
    filter: drop-shadow(0 0 22px rgba(0, 229, 255, 0.25));
  }
  .mascot.pop {
    transform: scale(1.07) translateY(-8px) rotate(-2deg);
  }

  .card {
    background: var(--card);
    backdrop-filter: blur(12px);
    width: 100%;
    max-width: 640px;
    min-width: 0;
    border-radius: 32px;
    padding: 26px;
    box-shadow: var(--card-outer-glow);
    border: 3px solid var(--card-border);
    position: relative;
    transition: all 0.35s ease;
  }
  .card::after {
    content: "✨💖✨";
    position: absolute;
    top: -15px;
    right: 26px;
    background: #ffffff;
    border: 2px solid #ff85c0;
    border-radius: 999px;
    padding: 2px 12px;
    font-size: 0.82rem;
    box-shadow: 0 4px 10px rgba(255, 77, 166, 0.25);
    pointer-events: none;
  }
  [data-theme="dark"] .card {
    border-radius: 14px;
    border: 1px solid var(--card-border);
    border-top: 6px solid var(--accent-solid);
  }
  [data-theme="dark"] .card::after {
    display: none;
  }

  header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; gap: 8px; flex-wrap: wrap; }
  .title-wrap { display: flex; flex-direction: column; gap: 2px; }
  .mode-badge {
    display: inline-block;
    width: fit-content;
    background: var(--badge-bg);
    color: var(--badge-text);
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    padding: 2px 9px;
    border-radius: 999px;
  }
  h1 {
    font-family: var(--font-title);
    font-size: 1.38rem;
    margin: 0;
    letter-spacing: 0.03em;
    display: flex;
    align-items: center;
    gap: 8px;
    text-shadow: 0 2px 0 rgba(255, 255, 255, 0.8);
  }
  [data-theme="dark"] h1 {
    font-size: 1.25rem;
    text-shadow: none;
  }

  .header-controls {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }
  .header-controls button {
    padding: 7px 12px;
    font-size: 0.8rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .label {
    font-size: .74rem;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--muted);
    margin: 12px 0 5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  #formula {
    width: 100%;
    font: 1.15rem 'JetBrains Mono', ui-monospace, Consolas, monospace;
    padding: 13px 16px;
    border-radius: 20px;
    border: 2.5px solid var(--line);
    background: var(--input-bg);
    color: var(--text);
    box-shadow: inset 0 2px 6px rgba(255, 105, 180, 0.12);
    transition: border-color 0.2s, box-shadow 0.2s, border-radius 0.3s;
  }
  [data-theme="dark"] #formula {
    border-radius: 8px;
    box-shadow: none;
  }
  #formula:focus {
    outline: none;
    border-color: var(--accent-solid);
    box-shadow: 0 0 0 4px rgba(255, 51, 153, 0.22);
  }
  [data-theme="dark"] #formula:focus {
    box-shadow: 0 0 12px rgba(0, 229, 255, 0.35);
  }

  #answer {
    min-height: 54px;
    padding: 13px 16px;
    border-radius: 20px;
    background: var(--ans-bg);
    border: 2.5px solid var(--line);
    font: 1.35rem 'JetBrains Mono', ui-monospace, Consolas, monospace;
    font-weight: 700;
    word-break: break-all;
    max-height: 160px;
    overflow: auto;
    transition: border-radius 0.3s, background 0.3s;
  }
  [data-theme="dark"] #answer { border-radius: 8px; }
  #answer.err { color: var(--err); font-size: 1rem; }

  button {
    font-family: var(--font-main);
    font-weight: 700;
    border: 2px solid var(--line);
    background: var(--btn);
    color: var(--text);
    border-radius: var(--btn-radius);
    padding: 10px 6px;
    cursor: pointer;
    min-width: 0;
    box-shadow: var(--btn-shadow);
    position: relative;
    transition: transform 0.12s cubic-bezier(0.34, 1.56, 0.64, 1),
                box-shadow 0.12s ease,
                border-color 0.18s ease,
                border-radius 0.25s ease,
                background 0.25s ease;
    user-select: none;
  }
  button:hover {
    border-color: var(--accent-solid);
    transform: translateY(-3px) scale(1.03);
  }
  [data-theme="dark"] button:hover {
    box-shadow: 0 4px 0 #060d14, 0 0 15px rgba(0, 229, 255, 0.45);
    color: #00e5ff;
  }
  button:active, button.pressed {
    transform: translateY(3px) scale(0.96);
    box-shadow: var(--btn-active-shadow);
  }

  .answer-row { display: flex; gap: 8px; align-items: stretch; }
  .answer-row #answer { flex: 1; min-width: 0; }
  .answer-row button { font-weight: 700; padding: 0 20px; }

  .keys { display: grid; grid-template-columns: repeat(5, 1fr); gap: 9px; margin-top: 16px; }
  .keys.fn button {
    font-size: .86rem;
    padding: 10px 4px;
    white-space: nowrap;
    background: var(--btn-fn);
  }
  .keys.pad button {
    font-size: 1.24rem;
    font-weight: 700;
    padding: 14px 6px;
  }

  #eq {
    background: var(--accent);
    color: var(--on-accent);
    border-color: var(--accent-solid);
    box-shadow: var(--accent-shadow);
  }
  #eq:hover { color: var(--on-accent); filter: brightness(1.08); }

  #clr, #back {
    background: var(--accent2);
    color: #fff;
    border-color: transparent;
  }
  [data-theme="dark"] #clr, [data-theme="dark"] #back {
    border-color: #3a7bd5;
    color: #e2f1ff;
  }

  .group h2 {
    font-size: .82rem;
    color: var(--muted);
    margin: 18px 0 8px;
    font-weight: 700;
    letter-spacing: 0.04em;
  }

  .vars { display: grid; grid-template-columns: repeat(auto-fill, minmax(125px, 1fr)); gap: 9px; }
  .vars label { display: flex; align-items: center; gap: 6px; font-family: 'JetBrains Mono', ui-monospace, monospace; }
  .vars label button { width: 42px; flex: 0 0 42px; padding: 8px 0; }
  .vars input {
    flex: 1;
    min-width: 0;
    padding: 8px 10px;
    border-radius: 999px;
    border: 2px solid var(--line);
    background: var(--input-bg);
    color: var(--text);
    font-family: 'JetBrains Mono', monospace;
    font-weight: 600;
    text-align: center;
    transition: border-radius 0.3s, border-color 0.2s;
  }
  [data-theme="dark"] .vars input { border-radius: 8px; }
  .vars input:focus { outline: none; border-color: var(--accent-solid); }

  .hint {
    font-size: .76rem;
    color: var(--muted);
    margin-top: 18px;
    line-height: 1.55;
    background: rgba(255, 255, 255, 0.55);
    border: 1.5px dashed #ff9ec7;
    padding: 10px 14px;
    border-radius: 18px;
  }
  [data-theme="dark"] .hint {
    background: rgba(0, 229, 255, 0.05);
    border: none;
    border-left: 3px solid var(--accent-solid);
    border-radius: 8px;
  }

  .audio-toast {
    position: fixed;
    bottom: 18px;
    right: 18px;
    background: var(--card);
    color: var(--text);
    border: 2px solid var(--card-border);
    padding: 9px 16px;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 700;
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    opacity: 0;
    transform: translateY(10px);
    pointer-events: none;
    transition: opacity 0.25s ease, transform 0.25s ease;
    z-index: 10;
  }
  .audio-toast.show {
    opacity: 1;
    transform: translateY(0);
  }

  @media (max-width: 1000px) {
    .mascot { display: none; }
  }
  @media (max-width: 480px) {
    body { padding: 10px; }
    .card { padding: 14px; }
    .keys.fn button { font-size: .75rem; }
    .keys.pad button { padding: 12px 4px; }
    .vars { grid-template-columns: repeat(2, 1fr); }
  }
</style>
</head>
<body>
<div class="layout">
  <div class="mascot" id="mascot" aria-hidden="true"></div>

  <div class="card">
    <header>
      <div class="title-wrap">
        <span class="mode-badge" id="mode-badge">💖 K-POP CENTER CONCEPT • GEHLEE</span>
        <h1 id="app-title">🎀 DSA Idol Calculator ✨</h1>
      </div>
      <div class="header-controls">
        <button id="bgm-btn" type="button" title="Toggle Background Music">🎵 BGM: ON</button>
        <button id="next-bgm-btn" type="button" title="Skip to Next Song (Track 1/3)">⏭️ Track 1/3</button>
        <button id="sfx-btn" type="button" title="Toggle Sound Effects">🔊 SFX: ON</button>
        <button id="theme" type="button" title="Toggle Gehlee (Light) / Jeo Ong (Dark) mode">💖 Gehlee</button>
      </div>
    </header>

    <div class="label" id="formula-label">✨ Formula Input</div>
    <input id="formula" type="text" autocomplete="off" spellcheck="false" placeholder="e.g. Σ(i=1 to 5) i" autofocus>

    <div class="label" id="answer-label">💖 Stage Result</div>
    <div class="answer-row">
      <div id="answer">&nbsp;</div>
      <button id="copy" type="button">Copy</button>
    </div>

    <div class="keys fn">
      <?php foreach ($fnKeys as [$label, $insert]): ?>
        <button type="button" class="calc-key fn-key" data-ins="<?= $h($insert) ?>"><?= $h($label) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="group">
      <h2 id="vars-title">🌸 Variables (assign a number, then use the letter in your formula)</h2>
      <div class="vars">
        <?php foreach ($variables as $v): ?>
          <label><button type="button" class="calc-key var-key" data-ins="<?= $h($v) ?>"><?= $h($v) ?></button>
            <input type="text" inputmode="decimal" data-var="<?= $h($v) ?>" placeholder="0"></label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="keys pad">
      <?php foreach ($padKeys as [$label, $insert, $id]): ?>
        <button type="button" class="calc-key pad-key"<?= $id ? ' id="' . $h($id) . '"' : '' ?><?= $insert !== null ? ' data-ins="' . $h($insert) . '"' : '' ?>><?= $h($label) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="hint">
      Use <b>to</b> for ranges: <code>Σ(i=1 to 5) i</code>. Implicit multiplication works: <code>xy</code>, <code>2x</code>, <code>Cx</code>.
      Use parentheses for longer bodies: <code>Σ(i=1 to 4) (i^2+1)</code>. Press <b>Enter</b> to calculate.
    </div>
  </div>
</div>

<div id="audio-toast" class="audio-toast"></div>

<!-- ================= AUDIO ASSETS (Organized in Assets/Audio/ Subfolders) ================= -->
<!-- Gehlee (Light Mode - K-Pop) 3-Song Playlist -->
<audio id="bgm-gehlee-1" src="Assets/Audio/Gehlee/song1.mp3" preload="auto"></audio>
<audio id="bgm-gehlee-2" src="Assets/Audio/Gehlee/song2.mp3" preload="auto"></audio>
<audio id="bgm-gehlee-3" src="Assets/Audio/Gehlee/song3.mp3" preload="auto"></audio>

<!-- Jeo Ong (Dark Mode) 3-Song Playlist -->
<audio id="bgm-jeo-1" src="Assets/Audio/Jeo/song1.mp3" preload="auto"></audio>
<audio id="bgm-jeo-2" src="Assets/Audio/Jeo/song2.mp3" preload="auto"></audio>
<audio id="bgm-jeo-3" src="Assets/Audio/Jeo/song3.mp3" preload="auto"></audio>

<!-- Button Click Sound -->
<audio id="click-sound" src="Assets/Audio/SFX/click.mp3" preload="auto"></audio>

<!-- Character Voice Lines (Supports 1 main file OR up to 3 rotating voice clips!) -->
<audio id="gehlee-voice-1" src="Assets/Audio/Gehlee/voice1.mp3" preload="auto"></audio>
<audio id="gehlee-voice-2" src="Assets/Audio/Gehlee/voice2.mp3" preload="auto"></audio>
<audio id="gehlee-voice-3" src="Assets/Audio/Gehlee/voice3.mp3" preload="auto"></audio>

<audio id="jeo-voice-1" src="Assets/Audio/Jeo/voice1.mp3" preload="auto"></audio>
<audio id="jeo-voice-2" src="Assets/Audio/Jeo/voice2.mp3" preload="auto"></audio>
<audio id="jeo-voice-3" src="Assets/Audio/Jeo/voice3.mp3" preload="auto"></audio>

<script>
const f = document.getElementById('formula'), ans = document.getElementById('answer');
const mascot = document.getElementById('mascot');
const appTitle = document.getElementById('app-title');
const modeBadge = document.getElementById('mode-badge');
const formulaLabel = document.getElementById('formula-label');
const answerLabel = document.getElementById('answer-label');
const varsTitle = document.getElementById('vars-title');
const themeBtn = document.getElementById('theme');
const bgmBtn = document.getElementById('bgm-btn');
const nextBgmBtn = document.getElementById('next-bgm-btn');
const sfxBtn = document.getElementById('sfx-btn');
const toastEl = document.getElementById('audio-toast');

/* ================= MULTI-SONG PLAYLIST & AUDIO ENGINE ================= */
let audioCtx = null;
let bgmEnabled = true;
let sfxEnabled = true;
let bgmInterval = null;
let bgmStep = 0;

const playlists = {
  light: ['bgm-gehlee-1', 'bgm-gehlee-2', 'bgm-gehlee-3'],
  dark:  ['bgm-jeo-1', 'bgm-jeo-2', 'bgm-jeo-3']
};
let trackIndex = { light: 0, dark: 0 };
let voiceIndex = { light: 0, dark: 0 };

const synthSongs = {
  light: [
    { notes: [523.25, 659.25, 783.99, 1046.50, 783.99, 659.25, 880.00, 783.99], tempo: 240, wave: 'sine' },
    { notes: [587.33, 739.99, 880.00, 1174.66, 987.77, 880.00, 739.99, 880.00], tempo: 220, wave: 'sine' },
    { notes: [659.25, 783.99, 987.77, 1318.51, 987.77, 783.99, 880.00, 1046.50], tempo: 250, wave: 'sine' }
  ],
  dark: [
    { notes: [220.00, 261.63, 329.63, 392.00, 196.00, 246.94, 293.66, 329.63], tempo: 370, wave: 'triangle' },
    { notes: [164.81, 196.00, 246.94, 329.63, 293.66, 246.94, 196.00, 220.00], tempo: 320, wave: 'triangle' },
    { notes: [130.81, 196.00, 261.63, 311.13, 293.66, 261.63, 196.00, 174.61], tempo: 400, wave: 'triangle' }
  ]
};

function getAudioCtx() {
  if (!audioCtx) {
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    if (AudioContext) audioCtx = new AudioContext();
  }
  if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume();
  return audioCtx;
}

function showToast(msg) {
  toastEl.textContent = msg;
  toastEl.classList.add('show');
  clearTimeout(showToast._t);
  showToast._t = setTimeout(() => toastEl.classList.remove('show'), 2300);
}

function playAudioOrFallback(audioEl, fallbackFn, volume = 0.7) {
  if (!sfxEnabled) return;
  if (audioEl && audioEl.getAttribute('src')) {
    audioEl.currentTime = 0;
    audioEl.volume = volume;
    const playPromise = audioEl.play();
    if (playPromise !== undefined) {
      playPromise.catch(() => fallbackFn());
      return;
    }
  }
  fallbackFn();
}

function playClickSound(btnType = 'normal') {
  if (!sfxEnabled) return;
  const clickAudio = document.getElementById('click-sound');
  const isDark = document.documentElement.dataset.theme === 'dark';

  playAudioOrFallback(clickAudio, () => {
    const ctx = getAudioCtx();
    if (!ctx) return;
    const now = ctx.currentTime;
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.connect(gain);
    gain.connect(ctx.destination);

    if (!isDark) {
      osc.type = 'sine';
      const baseFreq = btnType === 'eq' ? 740 : (btnType === 'clr' ? 480 : 587.33);
      osc.frequency.setValueAtTime(baseFreq, now);
      osc.frequency.exponentialRampToValueAtTime(baseFreq * 1.75, now + 0.065);
      gain.gain.setValueAtTime(0.18, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.075);
      osc.start(now);
      osc.stop(now + 0.08);
    } else {
      osc.type = 'triangle';
      const baseFreq = btnType === 'eq' ? 320 : (btnType === 'clr' ? 180 : 240);
      osc.frequency.setValueAtTime(baseFreq * 1.8, now);
      osc.frequency.exponentialRampToValueAtTime(baseFreq * 0.5, now + 0.045);
      gain.gain.setValueAtTime(0.20, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.05);
      osc.start(now);
      osc.stop(now + 0.055);
    }
  }, 0.55);
}

function playThemeCharacterSound(targetTheme) {
  if (!sfxEnabled) return;
  const voiceIds = targetTheme === 'light'
    ? ['gehlee-voice-1', 'gehlee-voice-2', 'gehlee-voice-3']
    : ['jeo-voice-1', 'jeo-voice-2', 'jeo-voice-3'];

  const idx = voiceIndex[targetTheme] % 3;
  voiceIndex[targetTheme]++;

  const primaryVoice = document.getElementById(voiceIds[idx]);
  const fallbackVoice1 = document.getElementById(voiceIds[0]);

  const synthVoiceFallback = () => {
    const ctx = getAudioCtx();
    if (!ctx) return;
    if (targetTheme === 'light') {
      const notes = [587.33, 739.99, 880.00, 1174.66, 1479.98];
      notes.forEach((freq, i) => {
        const now = ctx.currentTime + i * 0.055;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, now);
        gain.gain.setValueAtTime(0.16, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.22);
        osc.connect(gain); gain.connect(ctx.destination);
        osc.start(now); osc.stop(now + 0.23);
      });
    } else {
      const notes = [220.00, 164.81, 130.81, 261.63];
      notes.forEach((freq, i) => {
        const now = ctx.currentTime + i * 0.075;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(freq, now);
        gain.gain.setValueAtTime(0.12, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);
        osc.connect(gain); gain.connect(ctx.destination);
        osc.start(now); osc.stop(now + 0.26);
      });
    }
  };

  playAudioOrFallback(primaryVoice, () => {
    if (primaryVoice !== fallbackVoice1) {
      playAudioOrFallback(fallbackVoice1, synthVoiceFallback, 0.85);
    } else {
      synthVoiceFallback();
    }
  }, 0.85);
}

function stopAllBgm() {
  if (bgmInterval) { clearInterval(bgmInterval); bgmInterval = null; }
  [...playlists.light, ...playlists.dark].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.pause();
      el.currentTime = 0;
    }
  });
}

function updateTrackButtonLabel() {
  const mode = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
  const num = (trackIndex[mode] % 3) + 1;
  nextBgmBtn.textContent = `⏭️ Track ${num}/3`;
}

[...playlists.light, ...playlists.dark].forEach(id => {
  const el = document.getElementById(id);
  if (el) {
    el.addEventListener('ended', () => {
      const mode = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
      trackIndex[mode] = (trackIndex[mode] + 1) % 3;
      updateTrackButtonLabel();
      startBgm();
    });
  }
});

function startBgm() {
  stopAllBgm();
  updateTrackButtonLabel();
  if (!bgmEnabled) return;

  const mode = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
  const currentIdx = trackIndex[mode] % 3;
  const trackIds = playlists[mode];

  const tryPlayTrack = (idxToTry, fallbackFn) => {
    const el = document.getElementById(trackIds[idxToTry]);
    if (!el) return fallbackFn();
    el.volume = 0.28;
    const p = el.play();
    if (p !== undefined) {
      p.catch(() => fallbackFn());
    } else {
      fallbackFn();
    }
  };

  tryPlayTrack(currentIdx, () => {
    if (currentIdx !== 0) {
      tryPlayTrack(0, () => startSynthBgm(mode, currentIdx));
    } else {
      startSynthBgm(mode, currentIdx);
    }
  });
}

function startSynthBgm(mode, songIdx) {
  const ctx = getAudioCtx();
  if (!ctx) return;
  const song = synthSongs[mode][songIdx % 3];
  bgmStep = 0;

  bgmInterval = setInterval(() => {
    if (!bgmEnabled || document.hidden) return;
    const now = ctx.currentTime;
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.type = song.wave;
    const freq = song.notes[bgmStep % song.notes.length];
    bgmStep++;
    osc.frequency.setValueAtTime(freq, now);
    gain.gain.setValueAtTime(0.035, now);
    gain.gain.exponentialRampToValueAtTime(0.0008, now + 0.3);
    osc.connect(gain);
    gain.connect(ctx.destination);
    osc.start(now);
    osc.stop(now + 0.32);
  }, song.tempo);
}

let audioInitialized = false;
function initAudioOnFirstGesture() {
  if (audioInitialized) return;
  audioInitialized = true;
  getAudioCtx();
  if (bgmEnabled) startBgm();
}
document.addEventListener('pointerdown', initAudioOnFirstGesture, { once: true });
document.addEventListener('keydown', initAudioOnFirstGesture, { once: true });

bgmBtn.addEventListener('click', () => {
  bgmEnabled = !bgmEnabled;
  bgmBtn.textContent = bgmEnabled ? '🎵 BGM: ON' : '🔇 BGM: OFF';
  playClickSound();
  if (bgmEnabled) startBgm(); else stopAllBgm();
});

nextBgmBtn.addEventListener('click', () => {
  const mode = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
  trackIndex[mode] = (trackIndex[mode] + 1) % 3;
  playClickSound();
  updateTrackButtonLabel();
  const who = mode === 'dark' ? 'Jeo Ong' : 'Gehlee';
  showToast(`🎵 ${who} — Track ${(trackIndex[mode] % 3) + 1}/3`);
  if (bgmEnabled) startBgm();
});

sfxBtn.addEventListener('click', () => {
  sfxEnabled = !sfxEnabled;
  sfxBtn.textContent = sfxEnabled ? '🔊 SFX: ON' : '🔈 SFX: OFF';
  if (sfxEnabled) playClickSound();
});

function insert(t) {
  f.setRangeText(t, f.selectionStart, f.selectionEnd, 'end');
  f.focus();
}
document.querySelectorAll('[data-ins]').forEach(b => b.addEventListener('click', () => insert(b.dataset.ins)));

document.querySelectorAll('.calc-key, #copy').forEach(btn => {
  btn.addEventListener('click', () => {
    const type = btn.id === 'eq' ? 'eq' : (btn.id === 'clr' || btn.id === 'back' ? 'clr' : 'normal');
    playClickSound(type);
  });
});

async function calculate() {
  const vars = {};
  document.querySelectorAll('[data-var]').forEach(i => { if (i.value.trim() !== '') vars[i.dataset.var] = i.value.trim(); });
  ans.className = ''; ans.textContent = '…';
  try {
    const res = await fetch('api.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ expr: f.value, vars })
    });
    const data = await res.json();
    if (data.ok) ans.textContent = data.result;
    else { ans.className = 'err'; ans.textContent = data.error; }
  } catch (e) {
    ans.className = 'err'; ans.textContent = 'Could not reach the server.';
  }
}

document.getElementById('eq').addEventListener('click', calculate);
document.getElementById('clr').addEventListener('click', () => { f.value = ''; ans.className = ''; ans.innerHTML = '&nbsp;'; f.focus(); });
document.getElementById('back').addEventListener('click', () => {
  const s = f.selectionStart, e = f.selectionEnd;
  if (s !== e) f.setRangeText('', s, e, 'end'); else if (s > 0) f.setRangeText('', s - 1, s, 'end');
  f.focus();
});
document.getElementById('copy').addEventListener('click', () => {
  if (!ans.classList.contains('err')) {
    navigator.clipboard?.writeText(ans.textContent);
    showToast('📋 Answer copied!');
  }
});

themeBtn.addEventListener('click', () => {
  const r = document.documentElement;
  const nextTheme = r.dataset.theme === 'dark' ? 'light' : 'dark';
  r.dataset.theme = nextTheme;

  const currentSongNum = (trackIndex[nextTheme] % 3) + 1;

  if (nextTheme === 'dark') {
    modeBadge.textContent = '⚡ DARK MODE • JEO ONG';
    appTitle.textContent = '⚡ DSA Advanced Calculator';
    formulaLabel.textContent = 'Formula';
    answerLabel.textContent = 'Answer';
    varsTitle.textContent = 'Variables (assign a number, then use the letter in your formula)';
    themeBtn.textContent = '⚡ Jeo Ong';
    showToast(`⚡ Jeo Ong Mode • Track ${currentSongNum}/3`);
  } else {
    modeBadge.textContent = '💖 K-POP CENTER CONCEPT • GEHLEE';
    appTitle.textContent = '🎀 DSA Idol Calculator ✨';
    formulaLabel.textContent = '✨ Formula Input';
    answerLabel.textContent = '💖 Stage Result';
    varsTitle.textContent = '🌸 Variables (assign a number, then use the letter in your formula)';
    themeBtn.textContent = '💖 Gehlee';
    showToast(`💖 Gehlee K-Pop Idol Mode • Track ${currentSongNum}/3`);
  }

  mascot.classList.remove('pop');
  void mascot.offsetWidth;
  mascot.classList.add('pop');

  playThemeCharacterSound(nextTheme);
  if (bgmEnabled) startBgm();
  else updateTrackButtonLabel();

  trackIndex[nextTheme] = (trackIndex[nextTheme] + 1) % 3;
});

f.addEventListener('keydown', e => {
  if (e.key === 'Enter') {
    playClickSound('eq');
    calculate();
  }
});
</script>
</body>
</html>
