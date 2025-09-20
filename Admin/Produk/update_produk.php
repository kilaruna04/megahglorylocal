<?php
include "../../config.php";
session_start();

// Fungsi hapus titik ribuan
function unformatRupiah($str) {
    return str_replace('.', '', $str);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id            = (int)$_POST['id'];
    $nama_produk   = $_POST['nama_produk'];
    $kategori_id   = (int)$_POST['kategori_id'];
    $harga_beli    = unformatRupiah($_POST['harga_beli']);
    $harga_jual    = unformatRupiah($_POST['harga_jual']);
    $harga_online  = unformatRupiah($_POST['harga_jual_online']);
    $kode_barcode  = $_POST['kode_barcode'];
    $deskripsi     = $_POST['deskripsi'];
    $serial_number = $_POST['serial_number'];
    $stok          = (int)$_POST['stok'];
    $link_siplah   = $_POST['link_siplah'];
    $link_inaproc  = $_POST['link_inaproc'];

    $uploadDir   = __DIR__ . "/../../uploads/produk/";
    $defaultImg  = "Belum_ada_gambar_barang-removebg-preview.png";
    $gambar_sql  = "";

    // Ambil data lama untuk cek gambar
    $qOld = $conn->query("SELECT gambar FROM produk WHERE id=$id");
    $oldData = $qOld ? $qOld->fetch_assoc() : null;
    $gambar_lama = $oldData ? $oldData['gambar'] : $defaultImg;

    // === Jika ada upload baru ===
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        $origName = basename($_FILES["gambar"]["name"]);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if (in_array($ext, $allowed)) {
            $newName = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $origName);
            $targetFile = $uploadDir . $newName;

            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFile)) {
                $gambar_sql = ", gambar='$newName'";

                // Hapus gambar lama kalau bukan default
                if ($gambar_lama && $gambar_lama != $defaultImg && file_exists($uploadDir.$gambar_lama)) {
                    unlink($uploadDir.$gambar_lama);
                }
            }
        }
    } else {
        // Jika tidak upload → pastikan tetap ada gambar (lama atau default)
        if (empty($gambar_lama) || !file_exists($uploadDir.$gambar_lama)) {
            $gambar_sql = ", gambar='$defaultImg'";
        }
    }

    // === Update DB ===
    $sql = "UPDATE produk SET 
        nama_produk       = '".$conn->real_escape_string($nama_produk)."',
        kategori_id       = '$kategori_id',
        harga_beli        = '$harga_beli',
        harga_jual        = '$harga_jual',
        harga_jual_online = '$harga_online',
        kode_barcode      = '".$conn->real_escape_string($kode_barcode)."',
        deskripsi         = '".$conn->real_escape_string($deskripsi)."',
        serial_number     = '".$conn->real_escape_string($serial_number)."',
        stok              = '$stok',
        link_siplah       = '".$conn->real_escape_string($link_siplah)."',
        link_inaproc      = '".$conn->real_escape_string($link_inaproc)."'
        $gambar_sql
        WHERE id = '$id'";

    if ($conn->query($sql)) {
        header("Location: produk.php?msg=updated");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>
