<?php
include "../../config.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: customer.php");
    exit;
}

// --- CEK apakah customer sudah dipakai di transaksi ---
$used = false;

// Cek di penjualan_toko
$q1 = $conn->query("SELECT 1 FROM penjualan_toko WHERE customer_id=$id LIMIT 1");
if ($q1 && $q1->num_rows > 0) $used = true;

// Cek di penjualan_service
$q2 = $conn->query("SELECT 1 FROM penjualan_service WHERE customer_id=$id LIMIT 1");
if ($q2 && $q2->num_rows > 0) $used = true;

// Cek di penjualan_instansi
$q3 = $conn->query("SELECT 1 FROM penjualan_instansi WHERE customer_id=$id LIMIT 1");
if ($q3 && $q3->num_rows > 0) $used = true;

// Jika sudah dipakai, jangan hapus
if ($used) {
    header("Location: customer.php?msg=used");
    exit;
}

// --- Hapus customer ---
$stmt = $conn->prepare("DELETE FROM customer WHERE id=?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: customer.php?msg=deleted");
    exit;
} else {
    header("Location: customer.php?msg=error");
    exit;
}
