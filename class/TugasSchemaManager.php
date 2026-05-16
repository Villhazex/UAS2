<?php

/*
 * TugasSchemaManager — infrastruktur / "mudah"
 * Bertanggung jawab untuk memastikan tabel database yang dibutuhkan sudah ada.
 */

class TugasSchemaManager {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->ensureListSchema();
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
}
