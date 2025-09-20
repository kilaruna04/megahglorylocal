<?php
include "../../config.php";
session_start();

// pastikan hanya admin yang bisa
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php?role=admin");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // cek apakah produk ada
    $q = $conn->query("SELECT * FROM produk WHERE id='$id'");
    if ($q->num_rows > 0) {
        $data = $q->fetch_assoc();

        // hapus file gambar jika ada
        if (!empty($data['gambar']) && file_exists("../../uploads/produk/".$data['gambar'])) {
            unlink("../../uploads/produk/".$data['gambar']);
        }

        // hapus dari database
        $conn->query("DELETE FROM produk WHERE id='$id'");

        // redirect dengan pesan sukses
        header("Location: produk.php?msg=deleted");
        exit;
    } else {
        header("Location: produk.php?msg=notfound");
        exit;
    }
} else {
    header("Location: produk.php?msg=invalid");
    exit;
}
?>
