<?php
include "../../config.php";
session_start();

// Fungsi hapus titik ribuan (1.250.000 -> 1250000)
function unformatRupiah($str) {
    return (int) str_replace('.', '', $str);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk   = $_POST['nama_produk'];
    $kategori_id   = (int)$_POST['kategori_id'];
    $stok          = (int)$_POST['stok'];
    $harga_beli    = unformatRupiah($_POST['harga_beli']);
    $harga_jual    = unformatRupiah($_POST['harga_jual']);
    $harga_online  = unformatRupiah($_POST['harga_jual_online']);
    $kode_barcode  = $_POST['kode_barcode'];
    $deskripsi     = $_POST['deskripsi'];
    $serial_number = $_POST['serial_number'];
    $link_siplah   = $_POST['link_siplah'];
    $link_inaproc  = $_POST['link_inaproc'];

    // === Atur folder upload & default ===
    $uploadDir  = __DIR__ . "/../../uploads/produk/";
    $defaultImg = "Belum_ada_gambar_barang-removebg-preview.png";
    $gambar     = $defaultImg;

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // === Jika ada upload gambar ===
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        $origName = basename($_FILES["gambar"]["name"]);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if (in_array($ext, $allowed)) {
            $newName = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $origName);
            $targetFile = $uploadDir . $newName;

            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFile)) {
                $gambar = $newName;
            }
        }
    }

    // === Simpan ke database ===
    $sql = "INSERT INTO produk 
        (nama_produk, kategori_id, stok, harga_beli, harga_jual, harga_jual_online, 
         kode_barcode, deskripsi, gambar, serial_number, link_siplah, link_inaproc)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "siidddssssss",
        $nama_produk,
        $kategori_id,
        $stok,
        $harga_beli,
        $harga_jual,
        $harga_online,
        $kode_barcode,
        $deskripsi,
        $gambar,
        $serial_number,
        $link_siplah,
        $link_inaproc
    );

    if ($stmt->execute()) {
        header("Location: produk.php?msg=success");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
}
?>
