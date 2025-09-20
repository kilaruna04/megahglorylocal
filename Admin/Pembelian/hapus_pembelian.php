<?php
// Service/Admin/Pembelian/hapus_pembelian.php
// ===========================================
include "../../config.php";
include "hutang_helpers.php";

if (!isset($_GET['id'])) {
    header("Location: pembelian.php?msg=invalid");
    exit;
}

$id = intval($_GET['id']);

// ==================
// Hapus hutang terkait
// ==================
remove_hutang_for_pembelian($conn, $id);

// ==================
// Kembalikan stok (jika pembelian punya detail)
// ==================
$detail = $conn->query("SELECT produk_id, qty FROM pembelian_detail WHERE pembelian_id=$id");
if ($detail && $detail->num_rows > 0) {
    while ($row = $detail->fetch_assoc()) {
        $conn->query("UPDATE produk SET stok = stok - {$row['qty']} WHERE id={$row['produk_id']}");
    }
}
// Hapus detail pembelian
$conn->query("DELETE FROM pembelian_detail WHERE pembelian_id=$id");

// ==================
// Hapus pembelian utama
// ==================
$stmt = $conn->prepare("DELETE FROM pembelian WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: pembelian.php?msg=deleted");
exit;
