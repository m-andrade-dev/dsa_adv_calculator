<?php
// ---------- Content definitions: [label shown, text inserted into the formula] ----------

// Main keypad (4 columns). Third value = style (op / ctl), fourth = element id.
$pad = [
    ['AC', null, 'ctl', 'clr'], ['⌫', null, 'ctl', 'back'], ['(', '('], [')', ')'],
    ['7', '7'], ['8', '8'], ['9', '9'], ['÷', '÷', 'op'],
    ['4', '4'], ['5', '5'], ['6', '6'], ['×', '×', 'op'],
    ['1', '1'], ['2', '2'], ['3', '3'], ['−', '-', 'op'],
    ['0', '0'], ['.', '.'], [',', ','], ['+', '+', 'op'],
];

// Tabs beside the keypad: id => [tab title, help text, buttons]
$tabs = [
    'basic' => ['Basic', 'Remainder, integer division, floor and ceiling.', [
        ['%', '%'], ['//', '//'], ['⌊x⌋', 'floor('], ['⌈x⌉', 'ceil('],
    ]],
    'powers' => ['Powers & logs', 'Each button opens a bracket. Type the number, then close it with ).', [
        ['xʸ', '^'], ['√x', '√('], ['∛x', '∛('], ['log x', 'log('], ['log₂ x', 'log2('], ['ln x', 'ln('],
    ]],
    'fact' => ['Factorials', 'n! adds a factorial sign. The other two insert examples you can edit.', [
        ['n!', '!'], ['a! + b!', '5!+3!'], ['a! ÷ b!', '6!/4!'],
    ]],
    'sigma' => ['Σ & Π', 'Tap a template, then change the numbers. Σ adds terms, Π multiplies them. Double signs use two counters.', [
        ['Σ', 'Σ(i=1 to 5) i'], ['ΣΣ', 'ΣΣ(i=1 to 3, j=1 to 3) i*j'],
        ['Π', 'Π(i=1 to 4) i'], ['ΠΠ', 'ΠΠ(i=1 to 3, j=1 to 3) i*j'],
        ['to', ' to '], ['=', '='],
    ]],
];
$varsHint  = 'Type a number for a letter, then tap the letter to add it to your formula.';
$variables = ['A', 'B', 'C', 'D', 'x', 'y', 'c'];

// Mascot stickers per theme (light = Gehlee, dark = Jeo)
$art = [
    'light' => ['name' => 'Gehlee', 'idle' => 'Assets/Gehlee.png', 'hi' => 'Assets/Gehlee_hi.png',
                'jump' => 'Assets/Gehlee_jump.png', 'nom' => 'Assets/Gehlee_nom.png'],
    'dark'  => ['name' => 'Jeo Ong', 'idle' => 'Assets/Jeo.png',    'hi' => 'Assets/Jeo_hi.png',
                'jump' => 'Assets/Jeo_jump.png',    'nom' => 'Assets/Jeo_nom.png'],
];

$hints = ['vars' => $varsHint];
foreach ($tabs as $id => $t) $hints[$id] = $t[1];

$h = fn($s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DSA Advanced Calculator</title>
<script>
  // Apply the saved (or system) theme before first paint to avoid a flash.
  try {
    document.documentElement.dataset.theme = localStorage.getItem('theme') ||
      (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  } catch (e) {}
</script>
<style>
  /* Light = Gehlee: K-pop / Y2K photocard.  Dark = Jeo: rockstar stage. */
  :root {
    --font: ui-rounded, "Nunito", "Trebuchet MS", system-ui, sans-serif;
    --head: ui-rounded, "Baloo 2", "Trebuchet MS", system-ui, sans-serif;
    --upper: none; --ital: normal;
    --bg1: #ffd3e8; --bg2: #e6c8ff;
    --card-bg: linear-gradient(160deg, #fff, #ffeef8 55%, #f5e9ff);
    --ring-bg: linear-gradient(135deg, #ff8cc6, #c79bff, #ffb3dc, #ff8cc6);
    --text: #5a1740; --muted: #a2577f; --line: #ffc1de;
    --accent: #ff3d95; --accent-ink: #fff; --accent-soft: #ffe1f0; --glow: rgba(255, 61, 149, .4);
    --display: #fff7fd; --display-bg: linear-gradient(180deg, #f7ecff, #fde8f4); --panel: #fff0f8;
    --key: linear-gradient(180deg, #fff, #efe0ff); --num: linear-gradient(180deg, #fff, #ffc9e1); --op: linear-gradient(180deg, #ffe4f2, #ffadd2);
    --ctl-bg: linear-gradient(180deg, #ff94bc, #ff4f88); --ctl: #fff;
    --eq-bg: linear-gradient(135deg, #ff4fa0, #b259ff);
    --kb: #ff93c6; --ke: #ff7ab4; --kshine: rgba(255, 255, 255, .9);
    --r: 999px; --rc: 28px;
    --tag-bg: linear-gradient(90deg, #ff5fa2, #b86bff);
    --err: #d6215b;
    --shadow: 0 18px 50px rgba(220, 70, 150, .28);
    --stage: radial-gradient(2px 2px at 12% 18%, #fff, #0000), radial-gradient(3px 3px at 82% 12%, #fff, #0000), radial-gradient(2px 2px at 28% 72%, #fff, #0000), radial-gradient(3px 3px at 90% 64%, #fff, #0000), radial-gradient(2px 2px at 60% 88%, #fff, #0000), radial-gradient(2px 2px at 45% 8%, #fff, #0000), radial-gradient(3px 3px at 6% 52%, #fff, #0000);
    --corners: none;
  }
  [data-theme="dark"] {
    --font: "Bahnschrift", "Segoe UI", system-ui, sans-serif;
    --head: Impact, "Haettenschweiler", "Arial Narrow Bold", "Arial Black", sans-serif;
    --upper: uppercase; --ital: italic;
    --bg1: #06080c; --bg2: #121a28;
    --card-bg: repeating-linear-gradient(45deg, #151a22 0 5px, #181e27 5px 10px);
    --ring-bg: linear-gradient(135deg, #c9d1dc, #394150 35%, #aeb7c4 55%, #2f3643 80%, #c9d1dc);
    --text: #e9eef7; --muted: #8c97ab; --line: #2b3343;
    --accent: #6cb4ff; --accent-ink: #06101c; --accent-soft: #1b2a41; --glow: rgba(108, 180, 255, .55);
    --display: #0b0e13;
    --display-bg: repeating-linear-gradient(0deg, rgba(255, 255, 255, .035) 0 1px, #0000 1px 3px), linear-gradient(180deg, #07090d, #101620);
    --panel: #11151c;
    --key: linear-gradient(180deg, #3d4554, #232935); --num: linear-gradient(180deg, #2d3441, #1b2029); --op: linear-gradient(180deg, #36557f, #1f3558);
    --ctl-bg: linear-gradient(180deg, #2c3a3a, #192222); --ctl: #6fcfc3;
    --eq-bg: linear-gradient(180deg, #ff6a5e, #c4161c);
    --kb: #4b566a; --ke: #05070a; --kshine: rgba(255, 255, 255, .14);
    --r: 10px; --rc: 14px;
    --tag-bg: linear-gradient(90deg, #c4161c, #ff5a4f);
    --err: #ff8f86;
    --shadow: 0 20px 60px rgba(0, 0, 0, .7), 0 0 40px rgba(108, 180, 255, .12);
    --stage: linear-gradient(104deg, #0000 38%, rgba(108, 180, 255, .14) 48%, #0000 58%), linear-gradient(76deg, #0000 42%, rgba(255, 90, 79, .10) 52%, #0000 62%), radial-gradient(ellipse at 50% 0%, rgba(108, 180, 255, .12), #0000 60%);
    --corners: radial-gradient(circle, #9aa4b2 0 2.5px, #0000 3px) left top/10px 10px no-repeat, radial-gradient(circle, #9aa4b2 0 2.5px, #0000 3px) right top/10px 10px no-repeat, radial-gradient(circle, #9aa4b2 0 2.5px, #0000 3px) left bottom/10px 10px no-repeat, radial-gradient(circle, #9aa4b2 0 2.5px, #0000 3px) right bottom/10px 10px no-repeat;
  }

  * { box-sizing: border-box; }
  html { color-scheme: light; }
  [data-theme="dark"] { color-scheme: dark; }
  body {
    margin: 0; min-height: 100vh; display: flex; justify-content: center; align-items: flex-start;
    padding: 46px 16px 28px; color: var(--text); font-family: var(--font);
    background: linear-gradient(160deg, var(--bg1), var(--bg2)) fixed;
  }
  body::before { content: ""; position: fixed; inset: -5%; background: var(--stage); animation: stage 9s ease-in-out infinite; pointer-events: none; }
  @keyframes stage { 0%, 100% { opacity: .65; transform: translateX(-2%); } 50% { opacity: 1; transform: translateX(2%); } }

  /* ---------- Card ---------- */
  .card {
    position: relative; z-index: 2; width: 100%; max-width: 880px; padding: 22px;
    border: 4px solid transparent; border-radius: var(--rc);
    background: var(--card-bg) padding-box, var(--ring-bg) border-box; box-shadow: var(--shadow);
  }
  .card::before { content: ""; position: absolute; inset: 6px; pointer-events: none; background: var(--corners); }
  .cap { position: absolute; top: -30px; left: 0; right: 0; text-align: center; font-size: .62rem; font-weight: 800; letter-spacing: .28em; color: var(--muted); }
  .corner { position: absolute; top: -15px; right: 22px; padding: 2px 12px; border-radius: 999px; font-size: .78rem; font-weight: 800; background: var(--display); color: var(--accent); border: 2px solid var(--kb); }

  header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
  header .title { flex: 1; min-width: 0; }
  .tag { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: .62rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #fff; background: var(--tag-bg); }
  h1 { margin: 6px 0 0; font-family: var(--head); font-weight: 800; font-size: 1.35rem; line-height: 1.15; font-style: var(--ital); text-transform: var(--upper); }
  [data-theme="dark"] h1 { letter-spacing: .04em; text-shadow: 2px 2px 0 #c4161c; }
  header p { margin: 4px 0 0; font-size: .78rem; color: var(--muted); }

  /* ---------- Buttons (glossy "gummy" in light, steel "pedals" in dark) ---------- */
  button {
    font: inherit; cursor: pointer; color: var(--text); background: var(--key);
    border: 2px solid var(--kb); border-radius: var(--r);
    box-shadow: 0 4px 0 var(--ke), inset 0 2px 0 var(--kshine);
    transition: transform .06s, box-shadow .06s, filter .15s;
  }
  button:hover { filter: brightness(1.07); }
  button:active { transform: translateY(3px); box-shadow: 0 1px 0 var(--ke), inset 0 2px 0 var(--kshine); }
  button:focus-visible, .var input:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }

  .chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
  .chips button { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; font-size: .72rem; font-weight: 800; }
  .eq { display: inline-flex; gap: 2px; align-items: flex-end; height: 14px; }
  .eq i { width: 3px; height: 25%; border-radius: 2px; background: var(--accent); }
  .playing .eq i { animation: bar .55s ease-in-out infinite alternate; }
  .eq i:nth-child(2) { animation-delay: .12s; } .eq i:nth-child(3) { animation-delay: .24s; } .eq i:nth-child(4) { animation-delay: .06s; }
  @keyframes bar { to { height: 100%; } }

  /* ---------- Display ---------- */
  .display { background: var(--display-bg); border: 2px solid var(--line); border-radius: calc(var(--rc) - 8px); padding: 12px 14px 14px; }
  .lbl { display: block; font-size: .66rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); margin-bottom: 4px; }
  #formula {
    width: 100%; background: var(--display); color: var(--text); border: 2px solid var(--kb); border-radius: 14px;
    font: 1.1rem/1.4 "JetBrains Mono", ui-monospace, Consolas, monospace; padding: 10px 12px;
  }
  #formula::placeholder { color: var(--muted); opacity: .7; }
  #formula:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--glow); }
  .out { display: flex; align-items: flex-end; gap: 10px; margin-top: 12px; }
  .out > div:first-child { flex: 1; min-width: 0; }
  #answer {
    min-height: 48px; max-height: 150px; overflow: auto; text-align: right; word-break: break-all;
    font: 700 1.9rem/1.25 "JetBrains Mono", ui-monospace, Consolas, monospace; color: var(--accent); text-shadow: 0 0 12px var(--glow);
  }
  #answer.err { font: 600 .95rem/1.4 var(--font); color: var(--err); text-align: left; text-shadow: none; }
  #copy { flex: none; padding: 6px 14px; font-size: .8rem; font-weight: 800; }

  /* ---------- Layout ---------- */
  .main { display: grid; grid-template-columns: minmax(250px, 330px) 1fr; gap: 20px; margin-top: 16px; align-items: start; }
  @media (max-width: 720px) { .main { grid-template-columns: 1fr; } }
  .pad { display: grid; grid-template-columns: repeat(4, 1fr); gap: 9px; }
  .pad button { height: 54px; font-family: var(--head); font-size: 1.2rem; font-weight: 800; background: var(--num); }
  .pad button.op { background: var(--op); }
  .pad button.ctl { background: var(--ctl-bg); color: var(--ctl); border-color: transparent; }
  #eq {
    grid-column: 1 / -1; height: 58px; font-size: 1.5rem; color: #fff; background: var(--eq-bg); border-color: transparent;
    box-shadow: 0 4px 0 var(--ke), 0 0 18px var(--glow), inset 0 2px 0 var(--kshine);
  }

  /* ---------- Tools (tabs) ---------- */
  .tools { background: var(--panel); border: 2px solid var(--line); border-radius: calc(var(--rc) - 8px); padding: 12px; }
  .tabs { display: flex; flex-wrap: wrap; gap: 6px; }
  .tabs button { padding: 6px 12px; font-size: .8rem; font-weight: 800; background: transparent; color: var(--muted); box-shadow: none; border-color: transparent; }
  .tabs button:hover { color: var(--text); border-color: var(--line); }
  .tabs button[aria-selected="true"] { background: var(--accent); color: var(--accent-ink); border-color: var(--accent); box-shadow: 0 0 12px var(--glow); }
  .badge { display: inline-block; min-width: 18px; margin-left: 4px; padding: 0 5px; border-radius: 9px; background: var(--accent-ink); color: var(--accent); font-size: .72rem; line-height: 18px; }
  .badge[hidden] { display: none; }
  .peek { margin: 12px 2px; min-height: 2.6em; font-size: .82rem; line-height: 1.5; color: var(--muted); overflow-wrap: anywhere; }
  .peek b { color: var(--accent); font-family: ui-monospace, Consolas, monospace; font-weight: 600; }
  .row { display: grid; grid-template-columns: repeat(auto-fill, minmax(84px, 1fr)); gap: 9px; }
  .row button { height: 46px; font-size: 1rem; font-weight: 700; }
  .vars { display: grid; grid-template-columns: repeat(auto-fill, minmax(118px, 1fr)); gap: 8px; }
  .var { display: flex; align-items: center; gap: 6px; background: var(--display); border: 2px solid var(--line); border-radius: 14px; padding: 4px; }
  .var button { min-width: 36px; height: 34px; padding: 0; font-family: ui-monospace, monospace; font-weight: 800; background: var(--op); }
  .var input { width: 100%; min-width: 0; border: 0; background: transparent; color: var(--text); font: 1rem ui-monospace, Consolas, monospace; padding: 6px 4px; }
  .var input:focus { outline: none; }
  .var:focus-within { border-color: var(--accent); }
  .hint { font-size: .78rem; color: var(--muted); margin-top: 16px; line-height: 1.6; padding: 10px 12px; border: 2px dashed var(--line); border-radius: 14px; }
  code { background: var(--accent-soft); color: var(--accent); padding: 1px 5px; border-radius: 5px; font-size: .95em; }

  /* ---------- Mascot ---------- */
  .mascot-box { position: relative; }
  .mascot {
    display: block; width: 100%; padding: 0; background: none; border: 0; box-shadow: none; border-radius: 16px;
    animation: bob 3.6s ease-in-out infinite;
  }
  .mascot:active { transform: none; box-shadow: none; }
  .mascot img { display: block; width: 100%; height: auto; transition: transform .2s; }
  .mascot:hover img { transform: scale(1.04) rotate(-1.5deg); }
  .mascot img:not([src]) { visibility: hidden; }

  /* Big sticker beside the card (wide screens) */
  .side {
    display: none; position: fixed; bottom: 6px; z-index: 1;
    width: clamp(190px, calc((100vw - 880px) / 2 + 70px), 340px);
  }
  .side img { filter: drop-shadow(0 8px 14px rgba(0, 0, 0, .22)); }
  [data-theme="light"] .side { left: 8px; }
  [data-theme="dark"] .side { right: 8px; }
  @media (min-width: 1180px) { .side { display: block; } .head { display: none; } }

  /* Small sticker in the header (narrow screens) */
  .head { width: 68px; flex: none; }

  .bubble {
    position: absolute; z-index: 5; width: max-content; max-width: 210px; pointer-events: none;
    padding: 8px 12px; border-radius: 14px; font-size: .85rem; line-height: 1.35; text-align: center;
    background: var(--display); color: var(--text); border: 1px solid var(--line); box-shadow: 0 6px 18px rgba(0,0,0,.14);
    opacity: 0; transition: opacity .2s, transform .2s;
  }
  .bubble::after {
    content: ""; position: absolute; width: 10px; height: 10px; background: var(--display);
    border: 1px solid var(--line); transform: rotate(45deg);
  }
  .side .bubble { left: 50%; bottom: 100%; transform: translate(-50%, 8px) scale(.9); }
  .side .bubble.show { opacity: 1; transform: translate(-50%, -4px) scale(1); }
  .side .bubble::after { bottom: -6px; left: calc(50% - 5px); border-width: 0 1px 1px 0; }
  .head .bubble { left: 0; top: 100%; transform: translateY(-6px) scale(.9); transform-origin: top left; }
  .head .bubble.show { opacity: 1; transform: translateY(8px) scale(1); }
  .head .bubble::after { top: -6px; left: 24px; border-width: 1px 0 0 1px; }

  .hearts { position: absolute; inset: 0; pointer-events: none; }
  .hearts span { position: absolute; bottom: 45%; color: var(--accent); font-size: 1.2rem; opacity: 0; animation: floatup 1.2s ease-out forwards; }

  .pop   { animation: pop .45s ease-out; }
  .hop   { animation: hop .7s ease-out; }
  .wobble{ animation: wobble .5s ease-in-out; }
  @keyframes bob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
  @keyframes pop { 0% { transform: scale(.82); } 60% { transform: scale(1.08); } 100% { transform: scale(1); } }
  @keyframes hop { 0%, 100% { transform: translateY(0); } 30% { transform: translateY(-34px) rotate(-3deg); } 60% { transform: translateY(0); } 78% { transform: translateY(-10px); } }
  @keyframes wobble { 0%, 100% { transform: rotate(0); } 25% { transform: rotate(-5deg); } 75% { transform: rotate(5deg); } }
  @keyframes floatup {
    0% { opacity: 0; transform: translate(0, 0) scale(.6); }
    20% { opacity: 1; }
    100% { opacity: 0; transform: translate(var(--dx), -130px) scale(1.2); }
  }
  @media (prefers-reduced-motion: reduce) { * { animation: none !important; transition: none !important; } }
  .audio-toast {
    position: fixed; bottom: 18px; right: 18px; z-index: 10; padding: 9px 16px; border-radius: 999px;
    background: var(--display); color: var(--text); border: 2px solid var(--kb);
    font-size: .8rem; font-weight: 700; box-shadow: 0 6px 20px rgba(0, 0, 0, .25);
    opacity: 0; transform: translateY(10px); pointer-events: none; transition: opacity .25s, transform .25s;
  }
  .audio-toast.show { opacity: 1; transform: none; }
</style>
</head>
<body>

<!-- Big sticker, wide screens -->
<div class="mascot-box side">
  <div class="bubble" role="status" aria-live="polite"></div>
  <button class="mascot" type="button" title="Click me!"><img alt=""></button>
  <div class="hearts"></div>
</div>

<div class="card">
  <div class="cap" data-l="✦ ★ ♡ UNIVERSE PHOTOCARD ♡ ★ ✦" data-d="⚡ ALL ACCESS · WORLD TOUR ⚡">✦ ★ ♡ UNIVERSE PHOTOCARD ♡ ★ ✦</div>
  <div class="corner" data-l="✨💗✨" data-d="🤘 LIVE">✨💗✨</div>

  <header>
    <!-- Small sticker, narrow screens -->
    <div class="mascot-box head">
      <button class="mascot" type="button" title="Click me!"><img alt=""></button>
      <div class="bubble" role="status" aria-live="polite"></div>
      <div class="hearts"></div>
    </div>
    <div class="title">
      <span class="tag" data-l="💖 K-pop center concept • Gehlee" data-d="🎸 Rockstar headliner • Jeo Ong">💖 K-pop center concept • Gehlee</span>
      <h1 data-l="🎀 DSA Idol Calculator ✨" data-d="DSA Rockstar Calculator 🎸">🎀 DSA Idol Calculator ✨</h1>
      <p data-l="Sums, products, factorials, logs and variables. Stage ready!" data-d="Sums, products, factorials, logs and variables. Turn it up.">Sums, products, factorials, logs and variables. Stage ready!</p>
    </div>
  </header>

  <div class="chips">
    <button id="bgm" type="button" title="Toggle background music"><span class="eq"><i></i><i></i><i></i><i></i></span><span id="bgmt">BGM: OFF</span></button>
    <button id="track" type="button" title="Next track">⏭ Track 1/3</button>
    <button id="sfx" type="button" title="Toggle button sounds">🔊 SFX: ON</button>
    <button id="theme" type="button" title="Switch character and theme">💖 Gehlee</button>
  </div>

  <div class="display">
    <label class="lbl" for="formula" data-l="💗 Formula input" data-d="Formula input">💗 Formula input</label>
    <input id="formula" type="text" autocomplete="off" spellcheck="false" placeholder="Type or tap buttons, e.g. Σ(i=1 to 5) i" autofocus>
    <div class="out">
      <div>
        <span class="lbl" data-l="💗 Stage result" data-d="Amp output">💗 Stage result</span>
        <div id="answer" aria-live="polite">&nbsp;</div>
      </div>
      <button id="copy" type="button">Copy</button>
    </div>
  </div>

  <div class="main">
    <div class="pad">
      <?php foreach ($pad as $b):
        [$label, $insert] = $b; $cls = $b[2] ?? ''; $id = $b[3] ?? ''; ?>
        <button type="button"
                <?= $id ? 'id="' . $h($id) . '"' : '' ?>
                <?= $cls ? 'class="' . $h($cls) . '"' : '' ?>
                <?= $insert !== null ? 'data-ins="' . $h($insert) . '"' : '' ?>><?= $h($label) ?></button>
      <?php endforeach; ?>
      <button id="eq" type="button">=</button>
    </div>

    <div class="tools">
      <div class="tabs" role="tablist" aria-label="More functions">
        <?php $first = true; foreach ($tabs as $id => [$title]): ?>
          <button type="button" role="tab" id="tab-<?= $h($id) ?>" data-tab="<?= $h($id) ?>"
                  aria-controls="panel-<?= $h($id) ?>" aria-selected="<?= $first ? 'true' : 'false' ?>"
                  tabindex="<?= $first ? 0 : -1 ?>"><?= $h($title) ?></button>
        <?php $first = false; endforeach; ?>
        <button type="button" role="tab" id="tab-vars" data-tab="vars" aria-controls="panel-vars"
                aria-selected="false" tabindex="-1">Variables<span class="badge" id="vbadge" hidden>0</span></button>
      </div>

      <p class="peek" id="peek"></p>

      <?php $first = true; foreach ($tabs as $id => [, , $buttons]): ?>
        <div role="tabpanel" id="panel-<?= $h($id) ?>" data-panel="<?= $h($id) ?>" aria-labelledby="tab-<?= $h($id) ?>" <?= $first ? '' : 'hidden' ?>>
          <div class="row">
            <?php foreach ($buttons as [$label, $insert]): ?>
              <button type="button" data-ins="<?= $h($insert) ?>"><?= $h($label) ?></button>
            <?php endforeach; ?>
          </div>
        </div>
      <?php $first = false; endforeach; ?>

      <div role="tabpanel" id="panel-vars" data-panel="vars" aria-labelledby="tab-vars" hidden>
        <div class="vars">
          <?php foreach ($variables as $v): ?>
            <div class="var">
              <button type="button" data-ins="<?= $h($v) ?>"><?= $h($v) ?></button>
              <input type="text" inputmode="decimal" data-var="<?= $h($v) ?>" placeholder="0" aria-label="Value of <?= $h($v) ?>">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="hint">
    Press <b>Enter</b> to calculate. Implicit multiplication works: <code>2x</code>, <code>xy</code>, <code>Cx</code>.
    For longer Σ/Π bodies use brackets: <code>Σ(i=1 to 4) (i^2+1)</code>.
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
const ART = <?= json_encode($art, JSON_UNESCAPED_SLASHES) ?>;
const HINTS = <?= json_encode($hints, JSON_UNESCAPED_UNICODE) ?>;

const f = document.getElementById('formula'), ans = document.getElementById('answer');
const theme = () => document.documentElement.dataset.theme;
const pick = a => a[Math.floor(Math.random() * a.length)];

/* ---------- Formula entry ---------- */
function insert(t) {
  f.setRangeText(t, f.selectionStart, f.selectionEnd, 'end');
  f.focus();
}
document.querySelectorAll('[data-ins]').forEach(b => b.addEventListener('click', () => insert(b.dataset.ins)));

/* ---------- Tabs ---------- */
const tabs = [...document.querySelectorAll('[role=tab]')];
const panels = [...document.querySelectorAll('[role=tabpanel]')];
const peek = document.getElementById('peek');
let current = 'basic';
function showTab(id, focus) {
  current = id;
  tabs.forEach(t => { const on = t.dataset.tab === id; t.setAttribute('aria-selected', on); t.tabIndex = on ? 0 : -1; if (on && focus) t.focus(); });
  panels.forEach(p => p.hidden = p.dataset.panel !== id);
  peek.textContent = HINTS[id];
}
tabs.forEach((t, i) => {
  t.addEventListener('click', () => showTab(t.dataset.tab));
  t.addEventListener('keydown', e => {
    const d = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0;
    if (d) { e.preventDefault(); showTab(tabs[(i + d + tabs.length) % tabs.length].dataset.tab, true); }
  });
});
// Show exactly what a button will insert while hovering / focusing it
const showInsert = e => {
  const b = e.target.closest('.tools [data-ins]');
  if (!b) return;
  peek.textContent = 'Inserts: ';
  const bold = document.createElement('b'); bold.textContent = b.dataset.ins.trim(); peek.append(bold);
};
const resetPeek = () => { peek.textContent = HINTS[current]; };
const tools = document.querySelector('.tools');
tools.addEventListener('mouseover', showInsert); tools.addEventListener('focusin', showInsert);
tools.addEventListener('mouseout', resetPeek);   tools.addEventListener('focusout', resetPeek);
showTab('basic');

// Badge on the Variables tab = how many variables have a value
const badge = document.getElementById('vbadge');
const varInputs = [...document.querySelectorAll('[data-var]')];
function updateBadge() {
  const n = varInputs.filter(i => i.value.trim() !== '').length;
  badge.textContent = n; badge.hidden = n === 0;
}
varInputs.forEach(i => i.addEventListener('input', updateBadge));

/* ---------- Mascot ---------- */
const boxes = [...document.querySelectorAll('.mascot-box')];
const LINES = {
  light: {
    hi:    ['Hiii! Ready when you are.', 'Hiii! What are we solving?'],
    win:   ['Got it!', 'Nailed it!', 'Easy peasy!'],
    oops:  ['Nom nom… that one is hard to digest.', 'Nom nom… check that formula?'],
    poke:  ['Hehe, that tickles!', 'Need a hand?', 'Peace ✌', 'Hiii!'],
    snack: ['Snack time… nom nom.', 'Still here, just snacking.'],
    swap:  ['Gehlee is on duty!']
  },
  dark: {
    hi:    ['Hiii! Let’s crunch some numbers.', 'Hey! What are we solving?'],
    win:   ['Boom. Solved.', 'Clean.', 'Easy.'],
    oops:  ['Nom nom… that one didn’t digest.', 'Nom nom… check that formula?'],
    poke:  ['Hey, careful!', 'Need a hand?', 'Peace ✌', 'Yo!'],
    snack: ['Snack break… nom nom.', 'Still here, just snacking.'],
    swap:  ['Jeo is on duty.']
  }
};
// preload every pose so swaps never flicker
Object.values(ART).forEach(s => ['idle', 'hi', 'jump', 'nom'].forEach(k => { new Image().src = s[k]; }));

function play(el, cls) {
  el.classList.remove('pop', 'hop', 'wobble');
  void el.offsetWidth;
  el.classList.add(cls);
}
function hearts(n = 6) {
  boxes.forEach(box => {
    const wrap = box.querySelector('.hearts');
    for (let i = 0; i < n; i++) {
      const s = document.createElement('span');
      s.textContent = pick(['♥', '♡', '✦']);
      s.style.left = (30 + Math.random() * 40) + '%';
      s.style.setProperty('--dx', (Math.random() * 120 - 60) + 'px');
      s.style.animationDelay = (i * 70) + 'ms';
      wrap.appendChild(s);
      setTimeout(() => s.remove(), 1500);
    }
  });
}
let bubbleT, backT, idleT, snacking = false;
function setPose(pose, text, anim, { hold = 2600, stay = false } = {}) {
  const set = ART[theme()];
  boxes.forEach(box => {
    const img = box.querySelector('img'), bub = box.querySelector('.bubble');
    img.src = set[pose];
    box.querySelector('.mascot').setAttribute('aria-label', set.name + ' the mascot. Click to play.');
    if (anim) play(img, anim);
    if (text) { bub.textContent = text; bub.classList.add('show'); }
  });
  clearTimeout(bubbleT); clearTimeout(backT);
  if (text) bubbleT = setTimeout(() => boxes.forEach(b => b.querySelector('.bubble').classList.remove('show')), hold);
  if (pose !== 'idle' && !stay) backT = setTimeout(() => setPose('idle'), hold + 300);
  snacking = stay && pose === 'nom';
}
// Click the sticker to cycle poses
const order = ['hi', 'jump', 'nom']; let pokes = 0;
boxes.forEach(box => box.querySelector('.mascot').addEventListener('click', () => {
  const pose = order[pokes++ % order.length];
  setPose(pose, pick(LINES[theme()].poke), pose === 'jump' ? 'hop' : 'pop');
  hearts();
}));
// If you leave it alone for a while, it grabs a snack
function resetIdle() {
  clearTimeout(idleT);
  if (snacking) setPose('idle');
  idleT = setTimeout(() => setPose('nom', pick(LINES[theme()].snack), 'pop', { hold: 4000, stay: true }), 30000);
}
['pointerdown', 'keydown'].forEach(ev => document.addEventListener(ev, resetIdle));
resetIdle();
setPose('hi', pick(LINES[theme()].hi), 'pop', { hold: 3200 });

/* ================= AUDIO: your playlists, click sound and voice lines ================= */
const $ = id => document.getElementById(id);
const dark = () => theme() === 'dark';
const bgmBtn = $('bgm'), nextBgmBtn = $('track'), sfxBtn = $('sfx'), toastEl = $('audio-toast');
let audioCtx = null;
let bgmEnabled = true;
let sfxEnabled = true;

const playlists = {
  light: ['bgm-gehlee-1', 'bgm-gehlee-2', 'bgm-gehlee-3'],
  dark:  ['bgm-jeo-1', 'bgm-jeo-2', 'bgm-jeo-3']
};
let trackIndex = { light: 0, dark: 0 };
let voiceIndex = { light: 0, dark: 0 };

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
  [...playlists.light, ...playlists.dark].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.pause();
      el.currentTime = 0;
    }
  });
}

function chips() {
  $('bgmt').textContent = 'BGM: ' + (bgmEnabled ? 'ON' : 'OFF');
  updateTrackButtonLabel();
  sfxBtn.textContent = sfxEnabled ? '🔊 SFX: ON' : '🔈 SFX: OFF';
  $('theme').textContent = dark() ? '🎸 Jeo Ong' : '💖 Gehlee';
}
function updateTrackButtonLabel() {
  const mode = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
  const num = (trackIndex[mode] % 3) + 1;
  nextBgmBtn.textContent = `⏭️ Track ${num}/3`;
}

[...playlists.light, ...playlists.dark].forEach(id => {
  const el = document.getElementById(id);
  if (el) {
    el.addEventListener('playing', () => bgmBtn.classList.add('playing'));
    el.addEventListener('pause', () => bgmBtn.classList.remove('playing'));
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

  // If a song file is missing or blocked, try track 1; otherwise stay silent (no built-in music)
  tryPlayTrack(currentIdx, () => { if (currentIdx !== 0) tryPlayTrack(0, () => {}); });
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
  chips();
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
  chips();
  if (sfxEnabled) playClickSound();
});

function applyText() {
  document.querySelectorAll('[data-l]').forEach(e => { e.textContent = dark() ? e.dataset.d : e.dataset.l; });
}

// Button click sound (type = eq / clr / normal), same behaviour as before
document.addEventListener('click', e => {
  const b = e.target.closest('button');
  if (!b || b.matches('#bgm, #track, #sfx, #theme, .mascot')) return;
  playClickSound(b.id === 'eq' ? 'eq' : (b.id === 'clr' || b.id === 'back' ? 'clr' : 'normal'));
});

/* ---------- Calculate ---------- */
async function calculate() {
  const vars = {};
  varInputs.forEach(i => { if (i.value.trim() !== '') vars[i.dataset.var] = i.value.trim(); });
  ans.className = ''; ans.textContent = '…';
  let ok = false;
  try {
    const res = await fetch('api.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ expr: f.value, vars })
    });
    const data = await res.json();
    if (data.ok) { ans.textContent = data.result; ok = true; }
    else { ans.className = 'err'; ans.textContent = data.error; }
  } catch (e) {
    ans.className = 'err'; ans.textContent = 'Could not reach the server.';
  }
  const L = LINES[theme()];
  if (ok) { setPose('jump', pick(L.win), 'hop'); hearts(4); }
  else setPose('nom', pick(L.oops), 'wobble');
}

document.getElementById('eq').addEventListener('click', calculate);
document.getElementById('clr').addEventListener('click', () => { f.value = ''; ans.className = ''; ans.innerHTML = '&nbsp;'; f.focus(); });
document.getElementById('back').addEventListener('click', () => {
  const s = f.selectionStart, e = f.selectionEnd;
  if (s !== e) f.setRangeText('', s, e, 'end'); else if (s > 0) f.setRangeText('', s - 1, s, 'end');
  f.focus();
});
const copyBtn = document.getElementById('copy');
copyBtn.addEventListener('click', () => {
  if (ans.classList.contains('err')) return;
  navigator.clipboard?.writeText(ans.textContent);
  showToast('📋 Answer copied!');
  copyBtn.textContent = 'Copied';
  setTimeout(() => copyBtn.textContent = 'Copy', 1000);
});
$('theme').addEventListener('click', () => {
  const r = document.documentElement;
  const nextTheme = dark() ? 'light' : 'dark';
  r.dataset.theme = nextTheme;
  try { localStorage.setItem('theme', nextTheme); } catch (e) {}
  applyText(); chips();
  const n = (trackIndex[nextTheme] % 3) + 1;
  showToast(nextTheme === 'dark' ? `⚡ Jeo Ong Mode • Track ${n}/3` : `💖 Gehlee K-Pop Idol Mode • Track ${n}/3`);
  setPose('hi', pick(LINES[nextTheme].swap), 'pop');
  hearts();
  playThemeCharacterSound(nextTheme);
  if (bgmEnabled) startBgm();
  trackIndex[nextTheme] = (trackIndex[nextTheme] + 1) % 3;
});
applyText(); chips();
f.addEventListener('keydown', e => { if (e.key === 'Enter') { playClickSound('eq'); calculate(); } });
</script>
</body>
</html>
