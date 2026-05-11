<?php

require_once "class/TugasModel.php";

$tugas = new TugasModel();

$data = $tugas->tampilTugas();

?>

<!DOCTYPE html>
<html>
<head>
    <title>To Do List</title>
</head>
<body>

    <h2>TO DO LIST TUGAS</h2>

    <form action="tambah.php" method="POST">

        <input type="text" name="nama_tugas" placeholder="Masukkan tugas">

        <button type="submit">Tambah</button>

    </form>

    <br>

    <table border="1" cellpadding="10">

        <tr>
            <th>No</th>
            <th>Nama Tugas</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;

        while($row = $data->fetch_assoc()) {
        ?>

        <tr>

            <td><?= $no++; ?></td>

            <td><?= $row['nama_tugas']; ?></td>

            <td><?= $row['status_tugas']; ?></td>

            <td>

                <a href="selesai.php?id=<?= $row['id']; ?>">
                    Selesai
                </a>

                |

                <a href="hapus.php?id=<?= $row['id']; ?>">
                    Hapus
                </a>

            </td>

        </tr>

        <?php } ?>

    </table>

</body>
</html>