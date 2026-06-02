<?php

// Class untuk pencarian user berdasarkan username dan pengelolaan anggota list kelompok

class TugasListMembers
{
    private $conn;  // Koneksi database

    // Constructor: simpan koneksi database
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Mencari ID user dari daftar username; mengembalikan ID yang ditemukan dan yang tidak ditemukan
    private function CariUser($usernames)
    {
        $ids = [];
        $missing = [];
        $clean = [];

        // Bersihkan duplikat dan spasi dari daftar username
        foreach ($usernames as $username) {
            $username = trim($username);
            if ($username !== '' && !in_array($username, $clean, true)) {
                $clean[] = $username;
            }
        }

        // Cari setiap username di database
        foreach ($clean as $username) {
            $stmt = $this->conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
            $stmt->bind_param('s', $username);
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

    // Menerapkan daftar anggota ke suatu list (hapus lama, insert baru)
    private function applyListMembers($list_id, $owner_id, $jenis, $member_usernames)
    {
        $list_id = (int) $list_id;
        $owner_id = (int) $owner_id;
        $jenis = $jenis === 'kelompok' ? 'kelompok' : 'pribadi';

        // List pribadi: hapus semua anggota (tidak boleh punya anggota)
        if ($jenis === 'pribadi') {
            $this->conn->query("DELETE FROM task_list_members WHERE list_id = $list_id");

            return ['ok' => true, 'msg' => ''];
        }

        $members = $this->CariUser($member_usernames);
        $memberIds = array_values(array_filter($members['ids'], fn ($id) => $id !== $owner_id));

        if (!empty($members['missing'])) {
            return ['ok' => false, 'msg' => 'Username tidak ditemukan: '.implode(', ', $members['missing']).'.'];
        }

        // Hapus anggota lama, lalu insert anggota baru
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

    // Public wrapper untuk CariUser
    public function findMembers($usernames)
    {
        return $this->CariUser($usernames);
    }

    // Public wrapper untuk applyListMembers
    public function applyMembers($list_id, $owner_id, $jenis, $member_usernames)
    {
        return $this->applyListMembers($list_id, $owner_id, $jenis, $member_usernames);
    }
}
