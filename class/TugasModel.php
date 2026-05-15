<?php

require_once __DIR__ . "/Tugas.php";
require_once __DIR__ . "/../config/database.php";

class TugasModel extends Tugas {

    private $conn;
    private $database;
    private $defaultLists = [
        ['nama' => 'Pribadi', 'slug' => 'pribadi', 'warna' => '#008b8b', 'ikon' => '*'],
    ];

    public function __construct(
        $namaTugas = "",
        $statusTugas = "Belum Selesai",
        $dueDate = null,
        $prioritas = "",
        $kategori = "",
        $userId = 0
    ) {
        parent::__construct(
            $namaTugas,
            $statusTugas,
            $dueDate,
            $prioritas,
            $kategori,
            $userId
        );

        // Inheritance: TugasModel mewarisi property tugas dari class Tugas,
        // lalu menambahkan kemampuan database sebagai model.
        $this->database = new Database();
        $this->conn = $this->database->connect();
        $this->ensureListSchema();
    }

    public function getDatabase() {
        return $this->database;
    }

    public function getConn() {
        return $this->conn;
    }

    private function ensureListSchema() {
        $this->conn->query("
            CREATE TABLE IF NOT EXISTS task_lists (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                nama_list VARCHAR(100) NOT NULL,
                slug VARCHAR(120) NOT NULL,
                jenis VARCHAR(20) NOT NULL DEFAULT 'pribadi',
                warna VARCHAR(20) NOT NULL DEFAULT '#b87200',
                ikon VARCHAR(20) NOT NULL DEFAULT '.',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_slug (user_id, slug)
            )
        ");

        if (!$this->hasColumn('task_lists', 'jenis')) {
            $this->conn->query("ALTER TABLE task_lists ADD jenis VARCHAR(20) NOT NULL DEFAULT 'pribadi' AFTER slug");
        }

        $this->conn->query("
            CREATE TABLE IF NOT EXISTS task_list_members (
                id INT AUTO_INCREMENT PRIMARY KEY,
                list_id INT NOT NULL,
                user_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_list_member (list_id, user_id),
                KEY idx_member_user (user_id)
            )
        ");

        if (!$this->hasColumn('tugas', 'list_id')) {
            $this->conn->query("ALTER TABLE tugas ADD list_id INT NULL AFTER user_id");
        }
    }

    private function hasColumn($table, $column) {
        $table = $this->conn->real_escape_string($table);
        $column = $this->conn->real_escape_string($column);
        $result = $this->conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $result && $result->num_rows > 0;
    }

    private function slugify($text) {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug !== '' ? $slug : 'lainnya';
    }

    private function listPalette($index) {
        $colors = ['#008b8b', '#3366ff', '#c1006b', '#6b00c1', '#b87200', '#006b38', '#c10000'];
        return $colors[$index % count($colors)];
    }

    private function userCanAccessList($user_id, $list_id) {
        $user_id = (int) $user_id;
        $list_id = (int) $list_id;

        $result = $this->conn->query("
            SELECT task_lists.id
            FROM task_lists
            LEFT JOIN task_list_members
                ON task_list_members.list_id = task_lists.id
               AND task_list_members.user_id = $user_id
            WHERE task_lists.id = $list_id
              AND (task_lists.user_id = $user_id OR task_list_members.user_id IS NOT NULL)
            LIMIT 1
        ");

        return $result && $result->num_rows > 0;
    }

    private function insertListIfMissing($user_id, $nama, $slug, $warna, $ikon, $jenis = 'pribadi') {
        $user_id = (int) $user_id;
        $nama = $this->conn->real_escape_string($nama);
        $slug = $this->conn->real_escape_string($slug);
        $warna = $this->conn->real_escape_string($warna);
        $ikon = $this->conn->real_escape_string($ikon);
        $jenis = $this->conn->real_escape_string($jenis);

        return $this->conn->query("
            INSERT IGNORE INTO task_lists (user_id, nama_list, slug, jenis, warna, ikon)
            VALUES ($user_id, '$nama', '$slug', '$jenis', '$warna', '$ikon')
        ");
    }

    private function seedDefaultLists($user_id) {
        foreach ($this->defaultLists as $list) {
            $this->insertListIfMissing($user_id, $list['nama'], $list['slug'], $list['warna'], $list['ikon']);
        }
    }

    private function resolveListId($user_id, $kategori) {
        $user_id = (int) $user_id;
        $kategori = trim((string) $kategori);

        if (preg_match('/^list:(\d+)$/', $kategori, $match)) {
            $list_id = (int) $match[1];
            return $this->userCanAccessList($user_id, $list_id) ? $list_id : null;
        }

        $slug = $this->slugify($kategori ?: 'pribadi');
        $safeSlug = $this->conn->real_escape_string($slug);

        $result = $this->conn->query("
            SELECT id FROM task_lists
            WHERE user_id = $user_id AND slug = '$safeSlug'
            LIMIT 1
        ");

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int) $row['id'];
        }

        $nama = ucwords(str_replace('-', ' ', $slug));
        $count = $this->conn->query("SELECT COUNT(*) AS total FROM task_lists WHERE user_id = $user_id");
        $idx = $count ? (int) $count->fetch_assoc()['total'] : 0;
        $this->insertListIfMissing($user_id, $nama, $slug, $this->listPalette($idx), strtoupper(substr($nama, 0, 1)));

        $result = $this->conn->query("
            SELECT id FROM task_lists
            WHERE user_id = $user_id AND slug = '$safeSlug'
            LIMIT 1
        ");
        $row = $result ? $result->fetch_assoc() : null;
        return $row ? (int) $row['id'] : null;
    }

    private function syncLegacyTasks($user_id) {
        $user_id = (int) $user_id;
        $legacy = $this->conn->query("
            SELECT DISTINCT kategori
            FROM tugas
            WHERE user_id = $user_id
              AND (list_id IS NULL OR list_id = 0)
        ");

        if (!$legacy) return;

        while ($row = $legacy->fetch_assoc()) {
            $kategori = $row['kategori'] ?: 'pribadi';
            $slug = $this->slugify($kategori);
            $listId = $this->resolveListId($user_id, $slug);
            $safeSlug = $this->conn->real_escape_string($slug);
            if ($listId) {
                $this->conn->query("
                    UPDATE tugas
                    SET list_id = $listId
                    WHERE user_id = $user_id
                      AND (list_id IS NULL OR list_id = 0)
                      AND COALESCE(NULLIF(kategori, ''), 'pribadi') = '$safeSlug'
                ");
            }
        }
    }

    private function findUsersByUsernames($usernames) {
        $ids = [];
        $missing = [];
        $clean = [];

        foreach ($usernames as $username) {
            $username = trim($username);
            if ($username !== '' && !in_array($username, $clean, true)) {
                $clean[] = $username;
            }
        }

        foreach ($clean as $username) {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $ids[] = (int) $result->fetch_assoc()['id'];
            } else {
                $missing[] = $username;
            }
        }

        return ['ids' => $ids, 'missing' => $missing];
    }

    private function applyListMembers($list_id, $owner_id, $jenis, $member_usernames) {
        $list_id = (int) $list_id;
        $owner_id = (int) $owner_id;
        $jenis = $jenis === 'kelompok' ? 'kelompok' : 'pribadi';

        if ($jenis === 'pribadi') {
            $this->conn->query("DELETE FROM task_list_members WHERE list_id = $list_id");
            return ['ok' => true, 'msg' => ''];
        }

        $members = $this->findUsersByUsernames($member_usernames);
        $memberIds = array_values(array_filter($members['ids'], fn($id) => $id !== $owner_id));

        if (!empty($members['missing'])) {
            return ['ok' => false, 'msg' => 'Username tidak ditemukan: ' . implode(', ', $members['missing']) . '.'];
        }

        $this->conn->query("DELETE FROM task_list_members WHERE list_id = $list_id");

        foreach ($memberIds as $member_id) {
            $member_id = (int) $member_id;
            $this->conn->query("
                INSERT IGNORE INTO task_list_members (list_id, user_id)
                VALUES ($list_id, $member_id)
            ");
        }

        return ['ok' => true, 'msg' => ''];
    }

    public function tambahList($user_id, $nama_list, $jenis = 'pribadi', $member_usernames = []) {
        $user_id = (int) $user_id;
        $nama_list = trim($nama_list);
        $jenis = $jenis === 'kelompok' ? 'kelompok' : 'pribadi';

        if ($nama_list === '') {
            return ['ok' => false, 'msg' => 'Nama list tidak boleh kosong.'];
        }

        $members = ['ids' => [], 'missing' => []];
        if ($jenis === 'kelompok') {
            $members = $this->findUsersByUsernames($member_usernames);
            $members['ids'] = array_values(array_filter($members['ids'], fn($id) => $id !== $user_id));
            if (!empty($members['missing'])) {
                return ['ok' => false, 'msg' => 'Username tidak ditemukan: ' . implode(', ', $members['missing']) . '.'];
            }
        }

        $slug = $this->slugify($nama_list);
        $count = $this->conn->query("SELECT COUNT(*) AS total FROM task_lists WHERE user_id = $user_id");
        $idx = $count ? (int) $count->fetch_assoc()['total'] : 0;

        $ok = $this->insertListIfMissing(
            $user_id,
            $nama_list,
            $slug,
            $this->listPalette($idx),
            strtoupper(substr($nama_list, 0, 1)),
            $jenis
        );

        if (!$ok) {
            return ['ok' => false, 'msg' => 'List gagal dibuat.'];
        }

        $safeSlug = $this->conn->real_escape_string($slug);
        $safeJenis = $this->conn->real_escape_string($jenis);
        $this->conn->query("
            UPDATE task_lists
            SET jenis = '$safeJenis'
            WHERE user_id = $user_id AND slug = '$safeSlug'
        ");

        $list = $this->conn->query("
            SELECT id FROM task_lists
            WHERE user_id = $user_id AND slug = '$safeSlug'
            LIMIT 1
        ");
        $row = $list ? $list->fetch_assoc() : null;
        $list_id = $row ? (int) $row['id'] : 0;

        if ($jenis === 'pribadi' && $list_id > 0) {
            $this->conn->query("DELETE FROM task_list_members WHERE list_id = $list_id");
        }

        $memberResult = $this->applyListMembers($list_id, $user_id, $jenis, $member_usernames);
        if (!$memberResult['ok']) return $memberResult;

        return ['ok' => true, 'msg' => $jenis === 'kelompok' ? 'List kelompok berhasil dibuat.' : 'List pribadi berhasil dibuat.'];
    }

    public function editList($user_id, $list_id, $nama_list, $jenis = 'pribadi', $member_usernames = []) {
        $user_id = (int) $user_id;
        $list_id = (int) $list_id;
        $nama_list = trim($nama_list);
        $jenis = $jenis === 'kelompok' ? 'kelompok' : 'pribadi';

        if ($nama_list === '') {
            return ['ok' => false, 'msg' => 'Nama list tidak boleh kosong.'];
        }

        $current = $this->conn->query("
            SELECT id, slug FROM task_lists
            WHERE id = $list_id AND user_id = $user_id
            LIMIT 1
        ");

        if (!$current || $current->num_rows === 0) {
            return ['ok' => false, 'msg' => 'Kamu hanya bisa mengedit list milikmu sendiri.'];
        }

        $currentRow = $current->fetch_assoc();
        if ($currentRow['slug'] === 'pribadi') {
            return ['ok' => false, 'msg' => 'List Pribadi utama tidak bisa diedit.'];
        }

        if ($jenis === 'kelompok') {
            $members = $this->findUsersByUsernames($member_usernames);
            if (!empty($members['missing'])) {
                return ['ok' => false, 'msg' => 'Username tidak ditemukan: ' . implode(', ', $members['missing']) . '.'];
            }
        }

        $slug = $this->slugify($nama_list);
        $safeNama = $this->conn->real_escape_string($nama_list);
        $safeSlug = $this->conn->real_escape_string($slug);
        $safeJenis = $this->conn->real_escape_string($jenis);
        $safeIcon = $this->conn->real_escape_string(strtoupper(substr($nama_list, 0, 1)));

        $dupe = $this->conn->query("
            SELECT id FROM task_lists
            WHERE user_id = $user_id AND slug = '$safeSlug' AND id <> $list_id
            LIMIT 1
        ");

        if ($dupe && $dupe->num_rows > 0) {
            return ['ok' => false, 'msg' => 'Nama list sudah digunakan.'];
        }

        $ok = $this->conn->query("
            UPDATE task_lists
            SET nama_list = '$safeNama',
                slug = '$safeSlug',
                jenis = '$safeJenis',
                ikon = '$safeIcon'
            WHERE id = $list_id AND user_id = $user_id
        ");

        if (!$ok) {
            return ['ok' => false, 'msg' => 'List gagal diedit.'];
        }

        $memberResult = $this->applyListMembers($list_id, $user_id, $jenis, $member_usernames);
        if (!$memberResult['ok']) return $memberResult;

        $this->conn->query("
            UPDATE tugas
            SET kategori = '$safeSlug'
            WHERE list_id = $list_id
        ");

        return ['ok' => true, 'msg' => 'List berhasil diedit.'];
    }

    public function hapusList($user_id, $list_id) {
        $user_id = (int) $user_id;
        $list_id = (int) $list_id;

        $current = $this->conn->query("
            SELECT id, slug FROM task_lists
            WHERE id = $list_id AND user_id = $user_id
            LIMIT 1
        ");

        if (!$current || $current->num_rows === 0) {
            return ['ok' => false, 'msg' => 'Kamu hanya bisa menghapus list milikmu sendiri.'];
        }

        $row = $current->fetch_assoc();
        if ($row['slug'] === 'pribadi') {
            return ['ok' => false, 'msg' => 'List Pribadi utama tidak bisa dihapus.'];
        }

        $this->conn->query("DELETE FROM tugas WHERE list_id = $list_id");
        $this->conn->query("DELETE FROM task_list_members WHERE list_id = $list_id");
        $ok = $this->conn->query("DELETE FROM task_lists WHERE id = $list_id AND user_id = $user_id");

        return [
            'ok' => (bool) $ok,
            'msg' => $ok ? 'List berhasil dihapus.' : 'List gagal dihapus.',
        ];
    }

    public function tampilLists($user_id) {
        $user_id = (int) $user_id;
        $this->seedDefaultLists($user_id);
        $this->syncLegacyTasks($user_id);

        return $this->conn->query("
            SELECT task_lists.*,
                   owner.username AS owner_username,
                   CASE WHEN task_lists.user_id = $user_id THEN 1 ELSE 0 END AS is_owner,
                   GROUP_CONCAT(member_users.username ORDER BY member_users.username SEPARATOR ', ') AS member_usernames
            FROM task_lists
            LEFT JOIN users owner ON owner.id = task_lists.user_id
            LEFT JOIN task_list_members
                ON task_list_members.list_id = task_lists.id
               AND task_list_members.user_id = $user_id
            LEFT JOIN task_list_members all_members
                ON all_members.list_id = task_lists.id
            LEFT JOIN users member_users
                ON member_users.id = all_members.user_id
            WHERE task_lists.user_id = $user_id
               OR task_list_members.user_id IS NOT NULL
            GROUP BY task_lists.id
            ORDER BY task_lists.created_at ASC, task_lists.id ASC
        ");
    }

    public function tambahTugas() {
        $userId = (int) $this->userId;
        $listId = $this->resolveListId($userId, $this->kategori);
        if (!$listId) return false;

        $list = $this->conn->query("SELECT slug FROM task_lists WHERE id = $listId LIMIT 1");
        $listRow = $list ? $list->fetch_assoc() : null;
        $kategori = $this->conn->real_escape_string($listRow['slug'] ?? 'pribadi');
        $namaTugas = $this->conn->real_escape_string($this->namaTugas);
        $statusTugas = $this->conn->real_escape_string($this->statusTugas);
        $prioritas = $this->conn->real_escape_string($this->prioritas);
        $dueDate = $this->dueDate ? "'" . $this->conn->real_escape_string($this->dueDate) . "'" : "NULL";

        return $this->conn->query("
            INSERT INTO tugas (user_id, list_id, nama_tugas, status_tugas, due_date, prioritas, kategori)
            VALUES ($userId, $listId, '$namaTugas', '$statusTugas', $dueDate, '$prioritas', '$kategori')
        ");
    }

    public function tampilTugas($user_id) {
        $user_id = (int) $user_id;
        $this->seedDefaultLists($user_id);
        $this->syncLegacyTasks($user_id);

        return $this->conn->query("
            SELECT tugas.*,
                   task_lists.id AS accessible_list_id,
                   COALESCE(task_lists.slug, tugas.kategori, 'pribadi') AS kategori,
                   task_lists.nama_list,
                   task_lists.jenis AS list_jenis,
                   task_lists.warna AS list_warna,
                   task_lists.ikon AS list_ikon,
                   owner.username AS owner_username,
                   CASE WHEN task_lists.user_id = $user_id THEN 1 ELSE 0 END AS is_owner
            FROM tugas
            LEFT JOIN task_lists ON task_lists.id = tugas.list_id
            LEFT JOIN users owner ON owner.id = task_lists.user_id
            LEFT JOIN task_list_members
                ON task_list_members.list_id = task_lists.id
               AND task_list_members.user_id = $user_id
            WHERE task_lists.user_id = $user_id
               OR task_list_members.user_id IS NOT NULL
               OR (tugas.user_id = $user_id AND tugas.list_id IS NULL)
            ORDER BY tugas.id DESC
        ");
    }

    public function hapusTugas($id, $user_id) {
        $id = (int) $id;
        $user_id = (int) $user_id;

        return $this->conn->query("
            DELETE tugas FROM tugas
            LEFT JOIN task_lists ON task_lists.id = tugas.list_id
            LEFT JOIN task_list_members
                ON task_list_members.list_id = task_lists.id
               AND task_list_members.user_id = $user_id
            WHERE tugas.id = $id
              AND (task_lists.user_id = $user_id OR task_list_members.user_id IS NOT NULL OR tugas.user_id = $user_id)
        ");
    }

    public function selesaiTugas($id, $user_id) {
        $id = (int) $id;
        $user_id = (int) $user_id;

        return $this->conn->query("
            UPDATE tugas
            LEFT JOIN task_lists ON task_lists.id = tugas.list_id
            LEFT JOIN task_list_members
                ON task_list_members.list_id = task_lists.id
               AND task_list_members.user_id = $user_id
            SET tugas.status_tugas = 'Selesai'
            WHERE tugas.id = $id
              AND (task_lists.user_id = $user_id OR task_list_members.user_id IS NOT NULL OR tugas.user_id = $user_id)
        ");
    }

    public function hapusSemua($user_id) {
        $user_id = (int) $user_id;

        return $this->conn->query("
            DELETE tugas FROM tugas
            INNER JOIN task_lists ON task_lists.id = tugas.list_id
            WHERE task_lists.user_id = $user_id
        ");
    }

    public function hapusSelesai($user_id) {
        $user_id = (int) $user_id;

        return $this->conn->query("
            DELETE tugas FROM tugas
            INNER JOIN task_lists ON task_lists.id = tugas.list_id
            WHERE tugas.status_tugas = 'Selesai'
              AND task_lists.user_id = $user_id
        ");
    }
}

?>
