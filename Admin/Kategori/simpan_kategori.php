<?php
include "../../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_kategori']);
    $deskripsi = trim($_POST['deskripsi']);

    if ($nama !== '') {
        // Pastikan tabel punya kolom 'deskripsi'
        $stmt = $conn->prepare("INSERT INTO kategori_produk (nama_kategori, deskripsi) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama, $deskripsi);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: kategori.php?msg=sukses");
            exit;
        } else {
            $stmt->close();
            header("Location: kategori.php?msg=gagal");
            exit;
        }
    } else {
        header("Location: tambah_kategori.php?msg=kosong");
        exit;
    }
}

$conn->close();
