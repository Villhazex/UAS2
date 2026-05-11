<?php

require_once "class/TugasModel.php";

$tugas   = new TugasModel();
$data    = $tugas->tampilTugas();

$all_rows = [];
while($r = $data->fetch_assoc()) $all_rows[] = $r;
$total   = count($all_rows);
$selesai = count(array_filter($all_rows, fn($r) => $r['status_tugas'] === 'Selesai'));
$belum   = $total - $selesai;
$pct     = $total > 0 ? round(($selesai/$total)*100) : 0;

$palettes = [
    ['bg'=>'#ffd4e8','ink'=>'#c1006b'],
    ['bg'=>'#d4eaff','ink'=>'#0055c1'],
    ['bg'=>'#d4ffea','ink'=>'#006b38'],
    ['bg'=>'#fff3d4','ink'=>'#b87200'],
    ['bg'=>'#ead4ff','ink'=>'#6b00c1'],
    ['bg'=>'#ffd4d4','ink'=>'#c10000'],
    ['bg'=>'#d4fff3','ink'=>'#008b8b'],
    ['bg'=>'#fff0d4','ink'=>'#c16b00'],
];

$shapes = ['●','▲','■','◆','★','▶','✦','◉'];

?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tugas — Zine Edition</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anybody:wght@300;400;600;700;800;900&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
<style>

:root {
    --bg:     #f0ebe2;
    --ink:    #1a1208;
    --cream:  #faf6ee;
    --deco1:  #ff5c8a;
    --deco2:  #3366ff;
    --deco3:  #00cc77;
    --sidebar-w: 320px;
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
.prog-fill  {
    fill: none;
    stroke: var(--deco3);
    stroke-width: 6;
    stroke-linecap: butt;
    stroke-dasharray: 220;
    stroke-dashoffset: <?= 220 - round(2.20 * $pct) ?>;
    transform-origin: center;
    transform: rotate(-90deg);
    transition: stroke-dashoffset 1s ease;
}
.prog-label {
    display: flex;
    flex-direction: column;
}
.prog-pct {
    font-size: 44px;
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
.stat-cell-val.blue  { color: #6699ff; }
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

/* ── TOMBOL AKSI MASSAL ───────────────────── */
.aksi-tugas {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 18px;
    margin-bottom: 18px;
}

.btn-hapus-semua,
.btn-hapus-selesai {
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
.btn-hapus-semua:hover {
    background: var(--deco1);
    color: #fff;
}

.btn-hapus-selesai {
    background: rgba(255,255,255,0.12);
    color: var(--cream);
}
.btn-hapus-selesai:hover {
    background: var(--deco3);
    color: var(--ink);
}

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
}
.main::-webkit-scrollbar { width: 5px; }
.main::-webkit-scrollbar-thumb { background: rgba(26,18,8,0.15); }

.main-inner {
    padding: 36px 36px 60px;
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
}

/* ── CARD GRID ────────────────────────────── */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}

/* ── TASK CARD ────────────────────────────── */
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
.task-card.done { opacity: 0.52; }

.task-card::before {
    content: '';
    position: absolute; inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 120 120' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.72' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.06'/%3E%3C/svg%3E");
    background-size: 120px 120px;
    pointer-events: none;
    z-index: 1;
}

.card-band { height: 6px; background: var(--card-ink, #000); }

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

.card-title {
    font-size: 18px;
    font-weight: 700;
    line-height: 1.3;
    color: var(--ink);
    min-height: 46px;
    word-break: break-word;
}
.task-card.done .card-title { text-decoration: line-through; opacity: 0.45; }

.card-divider { height: 1.5px; background: var(--card-ink, #000); opacity: 0.1; margin: 12px 0; }

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

/* ── TOMBOL KARTU ─────────────────────────── */
.card-actions { display: flex; gap: 8px; margin-top: 14px; }

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
.card-btn.ok:hover {
    background: var(--card-ink);
    color: var(--card-bg);
}
.card-btn.rm {
    border-color: rgba(193,0,0,0.25);
    color: rgba(193,0,0,0.45);
}
.card-btn.rm:hover {
    border-color: #c10000;
    color: #c10000;
    background: rgba(193,0,0,0.06);
}

/* ── EMPTY ────────────────────────────────── */
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

/* ── RESPONSIVE ──────────────────────────── */
@media (max-width: 768px) {
    html, body { overflow: auto; }
    body { flex-direction: column; }
    .sidebar {
        width: 100%;
        height: auto;
        overflow: visible;
        padding: 28px 20px 24px;
    }
    .main { height: auto; overflow: visible; }
    .main-inner { padding: 24px 20px 48px; }
    .cards { grid-template-columns: repeat(auto-fill, minmax(160px,1fr)); gap: 14px; }
}

</style>
</head>
<body>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar">

    <div class="issue-tag">✦ Luminous &nbsp;·&nbsp; Vol. <?= date('Y') ?></div>

    <div class="main-title">
        <div class="shadow-text" aria-hidden="true">To do list<br><em>Organisasi</em></div>
        <div class="main-text">To do list<br><span class="accent-word">Organisasi</span></div>
    </div>
    <br>
    <div class="subtitle"><?= date('d M Y') ?></div>

    <!-- progress circle -->
    <div class="prog-wrap">
        <svg class="prog-svg" width="72" height="72" viewBox="0 0 72 72">
            <circle class="prog-track" cx="36" cy="36" r="30"/>
            <circle class="prog-fill"  cx="36" cy="36" r="30"
                style="stroke-dashoffset: <?= 188 - round(1.88 * $pct) ?>"/>
            <text x="36" y="41" text-anchor="middle"
                font-family="Anybody,sans-serif" font-weight="900" font-size="16"
                fill="#faf6ee"><?= $pct ?>%</text>
        </svg>
        <div class="prog-label">
            <span class="prog-sub">Progress</span>
            <span class="prog-pct" style="font-size:28px;color:var(--deco3)"><?= $pct ?>%</span>
            <span class="prog-sub"><?= $selesai ?> / <?= $total ?> selesai</span>
        </div>
    </div>

    <!-- stat cells -->
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

    <!-- input -->
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
        <button class="input-submit" type="submit">+ Tambah</button>
    </form>

    <div class="aksi-tugas">
        <a href="hapus_semua.php"
           onclick="return confirm('Hapus semua tugas?')"
           class="btn-hapus-semua">
           ✕ Hapus Semua
        </a>
        <a href="hapus_selesai.php"
           onclick="return confirm('Hapus semua tugas selesai?')"
           class="btn-hapus-selesai">
           ✓ Hapus yang Selesai
        </a>
    </div>

    <div class="sidebar-spacer"></div>

    <div class="sidebar-footer">
        Tetap Semangat,<br>pantang menyerah ✦
    </div>

</aside>

<!-- ═══ MAIN ═══ -->
<main class="main">
    <div class="main-inner">

        <div class="section-head">
            <div class="section-head-line"></div>
            <div class="section-head-title">✦ Daftar Tugas</div>
            <div class="section-head-line"></div>
        </div>

        <div class="cards">
            <?php if(empty($all_rows)): ?>
                <div class="empty">
                    <div class="empty-icon">◉</div>
                    <div class="empty-text">Belum ada tugas — yuk tambahkan!</div>
                </div>
            <?php else: ?>
                <?php foreach($all_rows as $i => $row):
                    $done  = $row['status_tugas'] === 'Selesai';
                    $pal   = $palettes[$i % count($palettes)];
                    $shape = $shapes[$i % count($shapes)];
                    $delay = $i * 0.055;
                ?>
                <div
                    class="task-card <?= $done ? 'done' : '' ?>"
                    style="
                        --card-bg: <?= $pal['bg'] ?>;
                        --card-ink: <?= $pal['ink'] ?>;
                        background: <?= $pal['bg'] ?>;
                        animation-delay: <?= $delay ?>s;
                    "
                >
                    <div class="card-band"></div>
                    <div class="card-inner">
                        <div class="card-shape"><?= $shape ?></div>
                        <div class="card-index">№ <?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?></div>
                        <div class="card-title"><?= htmlspecialchars($row['nama_tugas']) ?></div>
                        <div class="card-divider"></div>
                        <div class="card-status <?= $done ? 'done-badge' : '' ?>">
                            <?= $done ? '✓ Selesai' : '○ Pending' ?>
                        </div>
                        <div class="card-actions">
                            <?php if(!$done): ?>
                            <a href="selesai.php?id=<?= $row['id'] ?>" class="card-btn ok">✓ Done</a>
                            <?php endif; ?>
                            <a href="hapus.php?id=<?= $row['id'] ?>" class="card-btn rm"
                               onclick="return">✕ Hapus</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</main>

</body>
</html>