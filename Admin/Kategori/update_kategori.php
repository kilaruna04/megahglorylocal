<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php?role=admin");
    exit;
}

include "../../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama_kategori = trim($_POST['nama_kategori']);
    $gambar_sql = "";

    if ($id <= 0 || $nama_kategori === "") {
        $_SESSION['error'] = "Data kategori tidak valid.";
        header("Location: kategori.php");
        exit;
    }

    // --- Ambil data lama untuk hapus gambar lama bila perlu ---
    $stmt = $conn->prepare("SELECT gambar FROM kategori_produk WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $old = $res->fetch_assoc();
    $oldImage = $old['gambar'] ?? null;
    $stmt->close();

    $uploadDir = "../../uploads/kategori/";
    $newImage = $oldImage;

    // --- Jika ada file baru diupload ---
    if (!empty($_FILES['gambar']['name'])) {
        $fileTmp  = $_FILES['gambar']['tmp_name'];
        $fileName = basename($_FILES['gambar']['name']);
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed  = ['jpg','jpeg','png','gif'];

        if (in_array($fileExt, $allowed)) {
            $newFileName = "kategori_" . time() . "." . $fileExt;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $destPath)) {
                // Hapus gambar lama jika ada
                if ($oldImage && file_exists($uploadDir.$oldImage)) {
                    unlink($uploadDir.$oldImage);
                }
                $newImage = $newFileName;
            }
        }
    }

    // --- Update kategori ---
    $stmt = $conn->prepare("UPDATE kategori_produk SET nama_kategori=?, gambar=? WHERE id=?");
    $stmt->bind_param("ssi", $nama_kategori, $newImage, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Kategori berhasil diperbarui.";
        header("Location: kategori.php");
    } else {
        $_SESSION['error'] = "Terjadi kesalahan: " . $stmt->error;
        header("Location: kategori.php");
    }
    $stmt->close();
    exit;
} else {
    header("Location: kategori.php");
    exit;
}
?>
