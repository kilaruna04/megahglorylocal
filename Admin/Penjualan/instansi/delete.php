<?php
if (!isset($_SESSION)) { session_start(); }
include "../../../config.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // hapus piutang instansi (jika ada)
    $conn->query("DELETE FROM piutang_instansi WHERE penjualan_id=$id");

    // hapus detail produk
    $conn->query("DELETE FROM penjualan_instansi_detail WHERE penjualan_id=$id");

    // hapus header
    $conn->query("DELETE FROM penjualan_instansi WHERE id=$id");

    echo "<script>alert('Data penjualan instansi berhasil dihapus'); window.location='index.php';</script>";
} else {
    echo "<script>alert('ID tidak valid'); window.location='index.php';</script>";
}
?>
