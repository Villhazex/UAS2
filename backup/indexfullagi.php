<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once "class/TugasModel.php";

$tugas   = new TugasModel();
$data    = $tugas->tampilTugas();

$all_rows = [];
while($r = $data->fetch_assoc()) $all_rows[] = $r;
$total   = count($all_rows);
$selesai = count(array_filter($all_rows, fn($r) => $r['status_tugas'] === 'Selesai'));
$belum   = $total - $selesai;
$pct     = $total > 0 ? round(($selesai/$total)*100) : 0;

$priority_palettes = [

    'tinggi' => [
        'bg'  => '#ffd4d4',
        'ink' => '#c10000'
    ],

    'sedang' => [
        'bg'  => '#fff3d4',
        'ink' => '#b87200'
    ],

    'rendah' => [
        'bg'  => '#d4ffea',
        'ink' => '#006b38'
    ],

    'default' => [
        'bg'  => '#d4eaff',
        'ink' => '#0055c1'
    ]
];

$shapes = ['●','▲','■','◆','★','▶','✦','◉'];

$priority_map = [
    'tinggi' => ['label'=>'↑ Tinggi', 'color'=>'#c10000', 'bg'=>'rgba(193,0,0,0.10)'],
    'sedang' => ['label'=>'→ Sedang', 'color'=>'#b87200', 'bg'=>'rgba(184,114,0,0.10)'],
    'rendah' => ['label'=>'↓ Rendah', 'color'=>'#006b38', 'bg'=>'rgba(0,107,56,0.10)'],
];

$category_colors = [
    'pelajaran' => '#3366ff',
    'proyek'    => '#c1006b',
    'organisasi'=> '#6b00c1',
    'pribadi'   => '#008b8b',
    'lainnya'   => '#b87200',
];

session_start();
$toast = $_SESSION['toast'] ?? null;
unset($_SESSION['toast']);

?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tugas — Zine Edition</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anybody:wght@300;400;600;700;800;900&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

<!-- <link rel="stylesheet" href="css/index.css"> -->
 <style>
    :root {
    --bg:     #f0ebe2;
    --ink:    #1a1208;
    --cream:  #faf6ee;
    --deco1:  #ff5c8a;
    --deco2:  #3366ff;
    --deco3:  #00cc77;
    --sidebar-w: 320px;
    --card-radius: 0px;
}

*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
html, body { height: 100%; overflow: hidden; }

body {
    font-family: 'Anybody', sans-serif;
    background: var(--bg);
    color: var(--ink);
    display: flex;
}

/* ── SIDEBAR ─────────────────────────────── */
.sidebar {
    width: var(--sidebar-w);
    flex-shrink: 0;
    height: 100vh;
    background: var(--ink);
    color: var(--cream);
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 36px 28px 32px;
    border-right: 3px solid var(--ink);
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.12) transparent;
}
.sidebar::-webkit-scrollbar { width: 4px; }
.sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }

.issue-tag {
    display: inline-block;
    border: 1.5px solid rgba(255,255,255,0.18);
    color: rgba(250,246,238,0.55);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.26em;
    text-transform: uppercase;
    padding: 4px 10px;
    margin-bottom: 20px;
}

.main-title {
    font-family: 'DM Serif Display', serif;
    font-size: 56px;
    font-weight: 400;
    line-height: 0.92;
    letter-spacing: -0.02em;
    position: relative;
    margin-bottom: 6px;
}
.main-title .shadow-text {
    position: absolute;
    top: 3px; left: 3px;
    color: var(--deco1);
    opacity: 0.3;
    user-select: none;
    pointer-events: none;
}
.main-title .accent-word { font-style: italic; color: var(--deco2); }

.subtitle {
    font-size: 10px;
    font-weight: 400;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: rgba(250,246,238,0.35);
    margin-bottom: 32px;
}

.prog-wrap {
    display: flex;
    align-items: center;
    gap: 18px;
    margin-bottom: 28px;
}
.prog-svg { flex-shrink: 0; }
.prog-track { fill: none; stroke: rgba(255,255,255,0.08); stroke-width: 6; }
.prog-fill {
    fill: none;
    stroke: var(--deco3);
    stroke-width: 6;
    stroke-linecap: butt;
    stroke-dasharray: 188;
    stroke-dashoffset: 188;
    transform-origin: center;
    transform: rotate(-90deg);
    transition: stroke-dashoffset 1s ease;
}
.prog-label {
    display: flex;
    flex-direction: column;
}
.prog-pct {
    font-size: 28px;
    font-weight: 900;
    line-height: 1;
    color: var(--deco3);
}
.prog-sub {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(250,246,238,0.35);
    margin-top: 3px;
}

.stat-row {
    display: flex;
    gap: 0;
    border: 1.5px solid rgba(255,255,255,0.1);
    margin-bottom: 32px;
    overflow: hidden;
}
.stat-cell {
    flex: 1;
    padding: 12px 14px;
    border-right: 1.5px solid rgba(255,255,255,0.1);
}
.stat-cell:last-child { border-right: none; }
.stat-cell-label {
    font-size: 8px;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(250,246,238,0.3);
    margin-bottom: 3px;
}
.stat-cell-val {
    font-size: 32px;
    font-weight: 900;
    line-height: 1;
}
.stat-cell-val.pink  { color: var(--deco1); }
.stat-cell-val.green { color: var(--deco3); }

.s-divider {
    height: 1px;
    background: rgba(255,255,255,0.08);
    margin-bottom: 24px;
}

.input-label {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.26em;
    text-transform: uppercase;
    color: rgba(250,246,238,0.35);
    margin-bottom: 10px;
}
.input-form {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.input-field {
    border: 1.5px solid rgba(255,255,255,0.18);
    background: rgba(255,255,255,0.06);
    color: var(--cream);
    font-family: 'Anybody', sans-serif;
    font-size: 15px;
    font-weight: 600;
    padding: 13px 16px;
    outline: none;
    transition: border-color 0.15s, background 0.15s;
}
.input-field::placeholder { color: rgba(250,246,238,0.2); font-weight: 400; }
.input-field:focus {
    border-color: var(--deco2);
    background: rgba(51,102,255,0.08);
}

.input-date {
    border: 1.5px solid rgba(255,255,255,0.18);
    background: rgba(255,255,255,0.06);
    color: rgba(250,246,238,0.55);
    font-family: 'Anybody', sans-serif;
    font-size: 12px;
    font-weight: 600;
    padding: 9px 16px;
    outline: none;
    cursor: pointer;
    transition: border-color 0.15s;
    color-scheme: dark;
}
.input-date:focus { border-color: var(--deco2); }

/* ── PRIORITY + CATEGORY SELECTS ── */
.input-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.input-select {
    border: 1.5px solid rgba(255,255,255,0.18);
    background: rgba(255,255,255,0.06);
    color: rgba(250,246,238,0.7);
    font-family: 'Anybody', sans-serif;
    font-size: 11px;
    font-weight: 700;
    padding: 9px 12px;
    outline: none;
    cursor: pointer;
    color-scheme: dark;
    transition: border-color 0.15s;
    letter-spacing: 0.04em;
}
.input-select:focus { border-color: var(--deco2); }
.input-select option { background: #1a1208; }

.input-submit {
    border: none;
    background: var(--cream);
    color: var(--ink);
    font-family: 'Anybody', sans-serif;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 13px 20px;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border-radius: 4px;
}
.input-submit:hover { background: var(--deco1); color: #fff; }

.aksi-tugas {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 18px;
    margin-bottom: 18px;
}

.btn-hapus-semua,
.btn-hapus-selesai,
.btn-logout {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    font-family: 'Anybody', sans-serif;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 13px 20px;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
    border-radius: 4px;
    border: none;
}
.btn-hapus-semua {
    background: rgba(255,255,255,0.12);
    color: var(--cream);
}
.btn-hapus-semua:hover { background: var(--deco1); color: #fff; }
.btn-hapus-selesai {
    background: rgba(255,255,255,0.12);
    color: var(--cream);
}
.btn-hapus-selesai:hover { background: var(--deco3); color: var(--ink); }
.btn-logout {
    background: rgba(255,255,255,0.06);
    color: rgba(250,246,238,0.5);
    border: 1.5px solid rgba(255,255,255,0.1);
}
.btn-logout:hover { background: rgba(193,0,0,0.75); color: #fff; border-color: transparent; }

.sidebar-spacer { flex: 1; }

.sidebar-footer {
    font-family: 'DM Serif Display', serif;
    font-style: italic;
    font-size: 13px;
    color: rgba(250,246,238,0.2);
    padding-top: 24px;
    border-top: 1px solid rgba(255,255,255,0.08);
    line-height: 1.6;
}

/* ── MAIN AREA ────────────────────────────── */
.main {
    flex: 1;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    background-color: var(--bg);
    background-image: radial-gradient(circle, rgba(26,18,8,.16) 1px, transparent 1px);
    background-size: 22px 22px;
    scrollbar-width: thin;
    scrollbar-color: rgba(26,18,8,0.15) transparent;
    display: flex;
    flex-direction: column;
}
.main::-webkit-scrollbar { width: 5px; }
.main::-webkit-scrollbar-thumb { background: rgba(26,18,8,0.15); }

/* ── STICKY PROGRESS BAR ── */
.sticky-progress {
    position: sticky;
    top: 0;
    z-index: 100;
    background: var(--ink);
    color: var(--cream);
    padding: 10px 36px;
    display: flex;
    align-items: center;
    gap: 18px;
    border-bottom: 2px solid var(--ink);
    box-shadow: 0 2px 12px rgba(0,0,0,0.18);
}
.sticky-bar-label {
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.26em;
    text-transform: uppercase;
    color: rgba(250,246,238,0.4);
    white-space: nowrap;
}
.sticky-bar-track {
    flex: 1;
    height: 6px;
    background: rgba(255,255,255,0.1);
    border-radius: 0;
    overflow: hidden;
}
.sticky-bar-fill {
    height: 100%;
    background: var(--deco3);
    width: 0%;
    transition: width 1s ease;
    position: relative;
    overflow: hidden;
}
.sticky-bar-fill::after {
    content: '';
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(
        90deg,
        transparent,
        transparent 8px,
        rgba(255,255,255,0.15) 8px,
        rgba(255,255,255,0.15) 10px
    );
    animation: stripe 0.8s linear infinite;
}
@keyframes stripe { from { background-position-x: 0; } to { background-position-x: 20px; } }

.sticky-bar-pct {
    font-size: 13px;
    font-weight: 900;
    color: var(--deco3);
    white-space: nowrap;
    min-width: 40px;
    text-align: right;
}
.sticky-bar-counts {
    font-size: 9px;
    font-weight: 700;
    color: rgba(250,246,238,0.35);
    white-space: nowrap;
    letter-spacing: 0.1em;
}

.main-inner {
    padding: 28px 36px 60px;
    flex: 1;
}

/* ── TOOLBAR ── */
.toolbar {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.search-wrap {
    flex: 1;
    min-width: 180px;
    position: relative;
}
.search-wrap::before {
    content: '⌕';
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 18px;
    color: var(--ink);
    opacity: 0.3;
    pointer-events: none;
}
.search-input {
    width: 100%;
    border: 2px solid var(--ink);
    background: transparent;
    color: var(--ink);
    font-family: 'Anybody', sans-serif;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 14px 10px 36px;
    outline: none;
    transition: border-color 0.15s, background 0.15s;
}
.search-input::placeholder { color: var(--ink); opacity: 0.3; }
.search-input:focus {
    border-color: var(--deco2);
    background: rgba(51,102,255,0.04);
}

.filter-tabs {
    display: flex;
    gap: 0;
    border: 2px solid var(--ink);
    overflow: hidden;
}
.filter-tab {
    padding: 9px 16px;
    font-family: 'Anybody', sans-serif;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    cursor: pointer;
    border: none;
    background: transparent;
    color: var(--ink);
    opacity: 0.4;
    border-right: 1.5px solid var(--ink);
    transition: all 0.15s;
}
.filter-tab:last-child { border-right: none; }
.filter-tab:hover { opacity: 0.7; }
.filter-tab.active { opacity: 1; background: var(--ink); color: var(--bg); }

/* ── PRIORITY FILTER DOTS ── */
.priority-filters {
    display: flex;
    gap: 6px;
    align-items: center;
}
.priority-dot-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 12px;
    font-family: 'Anybody', sans-serif;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    cursor: pointer;
    border: 2px solid transparent;
    background: transparent;
    color: var(--ink);
    opacity: 0.4;
    transition: all 0.15s;
}
.priority-dot-btn.active { opacity: 1; border-color: currentColor; }
.priority-dot-btn:hover { opacity: 0.75; }
.priority-dot-btn .dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.section-head {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 28px;
}
.section-head-line { flex: 1; height: 2px; background: var(--ink); opacity: 0.12; }
.section-head-title {
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.32em;
    text-transform: uppercase;
    opacity: 0.35;
    color: var(--ink);
}

/* ── CARD GRID ── */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}

/* ── TASK CARD ── */
.task-card {
    border: 2.5px solid var(--ink);
    background: var(--card-bg, #fff);
    position: relative;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    animation: cardPop 0.42s cubic-bezier(.22,.68,0,1.28) both;
    overflow: hidden;
    box-shadow: 5px 5px 0 var(--card-ink, #000);
}
@keyframes cardPop {
    from { opacity:0; transform:scale(0.88) translateY(12px); }
    to   { opacity:1; transform:scale(1)    translateY(0); }
}
.task-card:hover {
    transform: translate(-3px,-3px);
    box-shadow: 8px 8px 0 var(--card-ink, #000);
}
.task-card.done {
    background: #dedad4 !important;
    border-color: #9a9590;
    box-shadow: 3px 3px 0 #9a9590 !important;
    filter: saturate(0.1);
    opacity: 0.75;
}
.task-card.done:hover {
    transform: translate(-2px,-2px);
    box-shadow: 5px 5px 0 #9a9590 !important;
}
.task-card.hidden-card { display: none !important; }

.done-stamp {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%,-50%) rotate(-20deg);
    font-family: 'DM Serif Display', serif;
    font-style: italic;
    font-size: 42px;
    color: rgba(0,0,0,0.065);
    white-space: nowrap;
    pointer-events: none;
    z-index: 0;
    user-select: none;
}

.task-card::before {
    content: '';
    position: absolute; inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 120 120' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.72' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.06'/%3E%3C/svg%3E");
    background-size: 120px 120px;
    pointer-events: none;
    z-index: 1;
}

/* ── PRIORITY STRIPE (top band) ── */
.card-band {
    height: 6px;
    background: var(--card-ink, #000);
    position: relative;
}
.card-band.priority-tinggi { background: #c10000; }
.card-band.priority-sedang { background: #b87200; }
.card-band.priority-rendah { background: #006b38; }

.card-inner {
    padding: 16px 18px 18px;
    position: relative;
    z-index: 2;
}

.card-shape {
    position: absolute;
    top: 14px; right: 16px;
    font-size: 20px;
    color: var(--card-ink, #000);
    opacity: 0.1;
    line-height: 1;
}

.card-index {
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.2em;
    color: var(--card-ink, #000);
    opacity: 0.38;
    margin-bottom: 8px;
}

/* ── CATEGORY TAG ── */
.card-category {
    display: inline-block;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    padding: 2px 8px;
    margin-bottom: 8px;
    border-radius: 2px;
    background: var(--cat-bg, rgba(0,0,0,0.08));
    color: var(--cat-color, var(--card-ink));
}

.card-title {
    font-size: 18px;
    font-weight: 700;
    line-height: 1.3;
    color: var(--ink);
    min-height: 46px;
    word-break: break-word;
}
.task-card.done .card-title {
    text-decoration: line-through;
    text-decoration-thickness: 2px;
    color: #777;
}

.card-edit-input {
    width: 100%;
    font-family: 'Anybody', sans-serif;
    font-size: 16px;
    font-weight: 700;
    border: 1.5px solid var(--card-ink, #000);
    background: rgba(255,255,255,0.5);
    color: var(--ink);
    padding: 6px 10px;
    outline: none;
    display: none;
}

.card-divider { height: 1.5px; background: var(--card-ink, #000); opacity: 0.1; margin: 12px 0; }

.card-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 8px;
}

.card-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 4px 10px;
    border: 2px solid var(--card-ink, #000);
    color: var(--card-ink, #000);
}
.card-status.done-badge {
    background: var(--card-ink, #000);
    color: var(--card-bg, #fff);
}

/* ── PRIORITY BADGE ── */
.priority-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 3px 8px;
    border: 1.5px solid;
}

.due-badge {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.06em;
    padding: 3px 8px;
    border: 1.5px solid;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.due-badge.overdue {
    border-color: #c10000;
    color: #c10000;
    background: rgba(193,0,0,0.06);
    animation: pulse 1.5s ease-in-out infinite;
}
.due-badge.soon {
    border-color: #b87200;
    color: #b87200;
    background: rgba(184,114,0,0.06);
}
.due-badge.ok {
    border-color: var(--card-ink);
    color: var(--card-ink);
    opacity: 0.45;
}

@keyframes pulse {
    0%,100% { opacity:1; }
    50%      { opacity:0.55; }
}

/* ── CARD ACTIONS ── */
.card-actions {
    display: flex;
    gap: 8px;
    margin-top: 14px;
    opacity: 0;
    transform: translateY(4px);
    transition: opacity 0.18s ease, transform 0.18s ease;
    flex-wrap: wrap;
}
.task-card:hover .card-actions {
    opacity: 1;
    transform: translateY(0);
}

.card-btn {
    text-decoration: none;
    font-family: 'Anybody', sans-serif;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 7px 14px;
    border: 1.5px solid;
    cursor: pointer;
    background: transparent;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border-radius: 4px;
}
.card-btn.ok {
    border-color: var(--card-ink);
    color: var(--card-ink);
    background: rgba(0,0,0,0.04);
}
.card-btn.ok:hover { background: var(--card-ink); color: var(--card-bg); }
.card-btn.edit {
    border-color: var(--card-ink);
    color: var(--card-ink);
    opacity: 0.55;
}
.card-btn.edit:hover { opacity: 1; background: rgba(0,0,0,0.05); }
.card-btn.save {
    border-color: var(--deco3);
    color: #006b38;
    display: none;
}
.card-btn.save:hover { background: var(--deco3); color: #fff; }
.card-btn.rm {
    border-color: rgba(193,0,0,0.25);
    color: rgba(193,0,0,0.45);
}
.card-btn.rm:hover {
    border-color: #c10000;
    color: #c10000;
    background: rgba(193,0,0,0.06);
}

/* ── EMPTY ── */
.empty {
    grid-column: 1/-1;
    text-align: center;
    padding: 80px 20px;
    border: 2px dashed rgba(26,18,8,0.18);
}
.empty-icon { font-size: 44px; margin-bottom: 14px; }
.empty-text {
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.1em;
    opacity: 0.28;
    text-transform: uppercase;
}
.no-results {
    display: none;
    grid-column: 1/-1;
    text-align: center;
    padding: 60px 20px;
    border: 2px dashed rgba(26,18,8,0.18);
}
.no-results.show { display: block; }

/* ── TOAST ── */
.toast-container {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    pointer-events: none;
}
.toast {
    background: var(--ink);
    color: var(--bg);
    font-family: 'Anybody', sans-serif;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    padding: 13px 22px;
    border-left: 4px solid var(--deco3);
    box-shadow: 4px 4px 0 rgba(0,0,0,0.25);
    animation: toastIn 0.35s cubic-bezier(.22,.68,0,1.2) both, toastOut 0.35s 2.8s ease forwards;
    max-width: 300px;
    line-height: 1.4;
}
.toast.error { border-left-color: var(--deco1); }
@keyframes toastIn {
    from { opacity:0; transform: translateY(20px) scale(0.92); }
    to   { opacity:1; transform: translateY(0) scale(1); }
}
@keyframes toastOut {
    from { opacity:1; transform: translateY(0); }
    to   { opacity:0; transform: translateY(10px); }
}

#confetti-canvas {
    position: fixed; inset: 0;
    pointer-events: none;
    z-index: 9998;
    opacity: 0;
    transition: opacity 0.5s;
}
#confetti-canvas.active { opacity: 1; }

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
    html, body { overflow: auto; }
    body { flex-direction: column; }
    .sidebar { width: 100%; height: auto; overflow: visible; padding: 28px 20px 24px; }
    .main { height: auto; overflow: visible; }
    .main-inner { padding: 24px 20px 48px; }
    .sticky-progress { padding: 8px 20px; }
    .cards { grid-template-columns: repeat(auto-fill, minmax(160px,1fr)); gap: 14px; }
    .card-actions { opacity: 1; transform: none; }
    .toolbar { gap: 8px; }
    .filter-tab { padding: 8px 10px; font-size: 10px; }
}
 </style>
</head>
<body>

<canvas id="confetti-canvas"></canvas>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar">

    <div class="issue-tag">✦ Luminous &nbsp;·&nbsp; Vol. <?= date('Y') ?></div>

    <div class="main-title">
        <div class="shadow-text" aria-hidden="true">To do list<br><em>Organisasi</em></div>
        <div class="main-text">To do list<br><span class="accent-word">Organisasi</span></div>
    </div>
    <br>
    <div class="subtitle"><?= date('d M Y') ?></div>

    <div class="prog-wrap">
        <svg class="prog-svg" width="72" height="72" viewBox="0 0 72 72">
            <circle class="prog-track" cx="36" cy="36" r="30"/>
            <circle class="prog-fill"  cx="36" cy="36" r="30"/>
            <text x="36" y="41" text-anchor="middle"
                font-family="Anybody,sans-serif" font-weight="900" font-size="16"
                fill="#faf6ee"><?= $pct ?>%</text>
        </svg>
        <div class="prog-label">
            <span class="prog-sub">Progress</span>
            <span class="prog-pct"><?= $pct ?>%</span>
            <span class="prog-sub"><?= $selesai ?> / <?= $total ?> selesai</span>
        </div>
    </div>

    <div class="stat-row">
        <div class="stat-cell">
            <div class="stat-cell-label">Total</div>
            <div class="stat-cell-val"><?= $total ?></div>
        </div>
        <div class="stat-cell">
            <div class="stat-cell-label">Selesai</div>
            <div class="stat-cell-val green"><?= $selesai ?></div>
        </div>
        <div class="stat-cell">
            <div class="stat-cell-label">Pending</div>
            <div class="stat-cell-val pink"><?= $belum ?></div>
        </div>
    </div>

    <div class="s-divider"></div>

    <div class="input-label">✦ Tambah tugas baru</div>
    <form class="input-form" action="tambah.php" method="POST">
        <input
            class="input-field"
            type="text"
            name="nama_tugas"
            placeholder="ketik nama tugas..."
            required
            autocomplete="off"
        >
        <input
            class="input-date input-field"
            type="date"
            name="due_date"
            title="Deadline (opsional)"
        >
        <!-- IDE BARU: Priority + Category -->
        <div class="input-row">
            <select class="input-select" name="prioritas" title="Prioritas">
                <option value="">— Prioritas</option>
                <option value="tinggi">↑ Tinggi</option>
                <option value="sedang">→ Sedang</option>
                <option value="rendah">↓ Rendah</option>
            </select>
            <select class="input-select" name="kategori" title="Kategori">
                <option value="">— Kategori</option>
                <option value="pelajaran">Pelajaran</option>
                <option value="proyek">Proyek</option>
                <option value="organisasi">Organisasi</option>
                <option value="pribadi">Pribadi</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>
        <button class="input-submit" type="submit">+ Tambah</button>
    </form>

    <div class="aksi-tugas">
        <a href="hapus_semua.php"
           onclick="return confirm('Hapus semua tugas?')"
           class="btn-hapus-semua">✕ Hapus Semua</a>
        <a href="hapus_selesai.php"
           onclick="return confirm('Hapus semua tugas selesai?')"
           class="btn-hapus-selesai">✓ Hapus yang Selesai</a>
        <a href="logout.php"
           onclick="return confirm('Yakin ingin logout?')"
           class="btn-logout">⎋ Logout</a>
    </div>

    <div class="sidebar-spacer"></div>

    <div class="sidebar-footer">
        Tetap Semangat,<br>pantang menyerah ✦
    </div>

</aside>

<!-- ═══ MAIN ═══ -->
<main class="main">

    <!-- IDE BARU: Sticky progress bar -->
    <div class="sticky-progress">
        <span class="sticky-bar-label">Progress</span>
        <div class="sticky-bar-track">
            <div class="sticky-bar-fill" id="stickyFill"></div>
        </div>
        <span class="sticky-bar-pct"><?= $pct ?>%</span>
        <span class="sticky-bar-counts"><?= $selesai ?>/<?= $total ?></span>
    </div>

    <div class="main-inner">

        <div class="toolbar">
            <div class="search-wrap">
                <input
                    class="search-input"
                    type="text"
                    id="searchInput"
                    placeholder="Cari tugas..."
                    autocomplete="off"
                >
            </div>

            <div class="filter-tabs" id="filterTabs">
                <button class="filter-tab active" data-filter="all">Semua <span id="cntAll"><?= $total ?></span></button>
                <button class="filter-tab" data-filter="pending">Pending <span id="cntPending"><?= $belum ?></span></button>
                <button class="filter-tab" data-filter="done">Selesai <span id="cntDone"><?= $selesai ?></span></button>
            </div>

            <!-- IDE BARU: Priority filter dots -->
            <div class="priority-filters" id="priorityFilters">
                <button class="priority-dot-btn active" data-prio="all" style="color:#1a1208">
                    <span class="dot" style="background:#1a1208"></span> Semua
                </button>
                <button class="priority-dot-btn" data-prio="tinggi" style="color:#c10000">
                    <span class="dot" style="background:#c10000"></span> Tinggi
                </button>
                <button class="priority-dot-btn" data-prio="sedang" style="color:#b87200">
                    <span class="dot" style="background:#b87200"></span> Sedang
                </button>
                <button class="priority-dot-btn" data-prio="rendah" style="color:#006b38">
                    <span class="dot" style="background:#006b38"></span> Rendah
                </button>
            </div>

        </div>

        <div class="section-head">
            <div class="section-head-line"></div>
            <div class="section-head-title">✦ Daftar Tugas</div>
            <div class="section-head-line"></div>
        </div>

        <div class="cards" id="cardGrid">
            <?php if(empty($all_rows)): ?>
                <div class="empty">
                    <div class="empty-icon">◉</div>
                    <div class="empty-text">Belum ada tugas — yuk tambahkan!</div>
                </div>
            <?php else: ?>
                <?php foreach($all_rows as $i => $row):
                    $done  = $row['status_tugas'] === 'Selesai';
                    $prio = $row['prioritas'] ?? '';
                    $pal = $priority_palettes[$prio]
                        ?? $priority_palettes['default'];
                    $shape = $shapes[$row['id'] % count($shapes)];
                    $delay = $i * 0.055;
                    $nama  = htmlspecialchars($row['nama_tugas']);

                    // Priority
                    $prio      = $row['prioritas'] ?? '';
                    $prioInfo  = $priority_map[$prio] ?? null;
                    $prioOrder = ['tinggi'=>0,'sedang'=>1,'rendah'=>2,''=>3];

                    // Category
                    $kat      = $row['kategori'] ?? '';
                    $katColor = $category_colors[$kat] ?? null;

                    // Band class
                    $bandClass = $prio ? "priority-$prio" : '';

                    // Due date logic
                    $dueDateStr = '';
                    $dueClass   = '';
                    $dueLabel   = '';
                    if (!empty($row['due_date'])) {
                        $due  = strtotime($row['due_date']);
                        $diff = $due - strtotime('today');
                        $dueDateStr = date('d M Y', $due);
                        if (!$done) {
                            if ($diff < 0)           { $dueClass='overdue'; $dueLabel='⚠ Terlambat!'; }
                            elseif ($diff <= 86400*2) { $dueClass='soon';    $dueLabel='⏳ Segera'; }
                            else                     { $dueClass='ok';      $dueLabel='📅 '.$dueDateStr; }
                        } else {
                            $dueClass='ok'; $dueLabel='📅 '.$dueDateStr;
                        }
                    }
                ?>
                <div
                    class="task-card <?= $done ? 'done' : '' ?>"
                    data-name="<?= strtolower($nama) ?>"
                    data-status="<?= $done ? 'done' : 'pending' ?>"
                    data-due="<?= $row['due_date'] ?? '' ?>"
                    data-prio="<?= htmlspecialchars($prio) ?>"
                    data-prio-order="<?= $prioOrder[$prio] ?? 3 ?>"
                    style="
                        --card-bg: <?= $pal['bg'] ?>;
                        --card-ink: <?= $pal['ink'] ?>;
                        background: <?= $pal['bg'] ?>;
                        animation-delay: <?= $delay ?>s;
                    "
                >
                    <div class="card-band <?= $bandClass ?>"></div>
                    <?php if($done): ?><div class="done-stamp">Selesai</div><?php endif; ?>
                    <div class="card-inner">
                        <div class="card-shape"><?= $shape ?></div>
                        <div class="card-index">№ <?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?></div>

                        <?php if($kat && $katColor): ?>
                        <div class="card-category" style="
                            --cat-color: <?= $katColor ?>;
                            --cat-bg: <?= $katColor ?>22;
                            color: <?= $katColor ?>;
                        ">
                            <?= ucfirst($kat) ?>
                        </div>
                        <?php endif; ?>

                        <div class="card-title"><?= $nama ?></div>

                        <div class="card-divider"></div>

                        <div class="card-meta">
                            <div class="card-status <?= $done ? 'done-badge' : '' ?>">
                                <?= $done ? '✓ Selesai' : '○ Pending' ?>
                            </div>
                            <?php if($prioInfo): ?>
                            <span class="priority-badge" style="
                                border-color: <?= $prioInfo['color'] ?>;
                                color: <?= $prioInfo['color'] ?>;
                                background: <?= $prioInfo['bg'] ?>;
                            "><?= $prioInfo['label'] ?></span>
                            <?php endif; ?>
                            <?php if($dueDateStr): ?>
                            <span class="due-badge <?= $dueClass ?>"><?= $dueLabel ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="card-actions">
                            <?php if(!$done): ?>
                            <a href="selesai.php?id=<?= $row['id'] ?>" class="card-btn ok">✓ Done</a>
                            <?php endif; ?>
                            <a href="hapus.php?id=<?= $row['id'] ?>" class="card-btn rm"
                               onclick="return confirm('Hapus tugas ini?')">✕ Hapus</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="no-results" id="noResults">
                <div class="empty-icon">◎</div>
                <div class="empty-text">Tugas tidak ditemukan</div>
            </div>
        </div>

    </div>
</main>

<div class="toast-container" id="toastContainer"></div>

<?php if($toast): ?>
<script>
document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($toast['msg']) ?>, <?= json_encode($toast['type'] ?? 'ok') ?>));
</script>
<?php endif; ?>

<script>
const pct = <?= $pct ?>;
const totalTask = <?= $total ?>;
</script>

<script>
    /* ─── TOAST ─── */
function showToast(msg, type = 'ok') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');

    t.className = 'toast' + (type === 'error' ? ' error' : '');
    t.textContent = msg;

    c.appendChild(t);

    setTimeout(() => t.remove(), 3300);
}

/* ─── FILTER + SEARCH + SORT + PRIORITY FILTER ─── */

const searchInput  = document.getElementById('searchInput');
const filterTabs   = document.querySelectorAll('.filter-tab');
const priorityBtns = document.querySelectorAll('.priority-dot-btn');
const cardGrid     = document.getElementById('cardGrid');
const noResults    = document.getElementById('noResults');

stickyFill.style.width = pct + '%';

const progCircle = document.querySelector('.prog-fill');

const offset = 188 - (1.88 * pct);

progCircle.style.strokeDashoffset = offset;

let currentFilter = 'all';
let currentPrio   = 'all';

function applyFilters() {

    const query = searchInput.value.toLowerCase().trim();

    const cards = [...cardGrid.querySelectorAll('.task-card')];

    let visible = 0;

    cards.forEach(card => {

        const matchFilter =
            currentFilter === 'all' ||
            (currentFilter === 'done' &&
                card.dataset.status === 'done') ||
            (currentFilter === 'pending' &&
                card.dataset.status === 'pending');

        const matchSearch =
            card.dataset.name.includes(query);

        const matchPrio =
            currentPrio === 'all' ||
            card.dataset.prio === currentPrio;

        if (matchFilter && matchSearch && matchPrio) {

            card.classList.remove('hidden-card');
            visible++;

        } else {

            card.classList.add('hidden-card');
        }
    });

    noResults.classList.toggle(
        'show',
        visible === 0 && cards.length > 0
    );
}

/* FILTER TAB */

filterTabs.forEach(tab => {

    tab.addEventListener('click', () => {

        filterTabs.forEach(t =>
            t.classList.remove('active')
        );

        tab.classList.add('active');

        currentFilter = tab.dataset.filter;

        applyFilters();
    });
});

/* PRIORITY FILTER */

priorityBtns.forEach(btn => {

    btn.addEventListener('click', () => {

        priorityBtns.forEach(b =>
            b.classList.remove('active')
        );

        btn.classList.add('active');

        currentPrio = btn.dataset.prio;

        applyFilters();
    });
});

searchInput.addEventListener('input', applyFilters);

/* ─── INLINE EDIT ─── */

function startEdit(btn) {

    const card    = btn.closest('.task-card');
    const title   = card.querySelector('.card-title');
    const input   = card.querySelector('.card-edit-input');
    const saveBtn = card.querySelector('.card-btn.save');

    title.style.display = 'none';
    input.style.display = 'block';

    input.focus();
    input.select();

    saveBtn.style.display = 'inline-flex';

    btn.style.display = 'none';
}

function saveEdit(btn, id) {

    const card    = btn.closest('.task-card');

    const title   = card.querySelector('.card-title');

    const input   = card.querySelector('.card-edit-input');

    const newVal  = input.value.trim();

    if (!newVal) {

        showToast(
            'Nama tugas tidak boleh kosong!',
            'error'
        );

        return;
    }

    fetch(`edit.php?id=${id}&nama=${encodeURIComponent(newVal)}`)

        .then(r => r.json())

        .then(d => {

            if (d.ok) {

                title.textContent = newVal;

                card.dataset.name = newVal.toLowerCase();

                input.value = newVal;

                showToast('✓ Tugas berhasil diubah');

            } else {

                showToast('Gagal menyimpan', 'error');
            }
        })

        .catch(() =>
            showToast('Gagal menyimpan', 'error')
        );

    title.style.display = '';

    input.style.display = 'none';

    btn.style.display = 'none';

    editBtn.style.display = 'inline-flex';
}

/* ─── CONFETTI ─── */

if (pct === 100 && totalTask > 0) {

    setTimeout(() => launchConfetti(), 400);
}

function launchConfetti() {

    const canvas = document.getElementById('confetti-canvas');

    canvas.classList.add('active');

    const ctx = canvas.getContext('2d');

    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;

    const colors = [
        '#ff5c8a',
        '#3366ff',
        '#00cc77',
        '#ffd166',
        '#c1006b',
        '#6699ff'
    ];

    const bits = Array.from({ length: 120 }, () => ({

        x: Math.random() * canvas.width,

        y: -20 - Math.random() * 200,

        w: 6 + Math.random() * 10,

        h: 8 + Math.random() * 6,

        r: Math.random() * Math.PI * 2,

        dr: (Math.random() - 0.5) * 0.15,

        vx: (Math.random() - 0.5) * 3,

        vy: 2 + Math.random() * 3,

        c: colors[
            Math.floor(Math.random() * colors.length)
        ]
    }));

    let frame = 0;

    function draw() {

        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );

        bits.forEach(b => {

            ctx.save();

            ctx.translate(b.x, b.y);

            ctx.rotate(b.r);

            ctx.fillStyle = b.c;

            ctx.fillRect(
                -b.w / 2,
                -b.h / 2,
                b.w,
                b.h
            );

            ctx.restore();

            b.x += b.vx;
            b.y += b.vy;
            b.r += b.dr;
        });

        frame++;

        if (frame < 140) {

            requestAnimationFrame(draw);

        } else {

            canvas.classList.remove('active');

            ctx.clearRect(
                0,
                0,
                canvas.width,
                canvas.height
            );
        }
    }

    draw();

    showToast(
        '🎉 Semua tugas selesai! Luar biasa!'
    );
}
</script>
</body>
</html>