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

<link rel="stylesheet" href="style.css">
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

            <div class="sort-wrap">
                <span class="sort-label">Urut</span>
                <select class="sort-select" id="sortSelect">
                    <option value="default">Default</option>
                    <option value="az">A–Z</option>
                    <option value="za">Z–A</option>
                    <option value="due">Deadline</option>
                    <option value="priority">Prioritas</option>
                </select>
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
                    $pal   = $palettes[$i % count($palettes)];
                    $shape = $shapes[$i % count($shapes)];
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
                        <input
                            class="card-edit-input"
                            type="text"
                            value="<?= $nama ?>"
                            data-id="<?= $row['id'] ?>"
                        >

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
                            <button class="card-btn edit" onclick="startEdit(this)">✎ Edit</button>
                            <button class="card-btn save" onclick="saveEdit(this, <?= $row['id'] ?>)">✓ Simpan</button>
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

<script src="script.js"></script>
</body>
</html>