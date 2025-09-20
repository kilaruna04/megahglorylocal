<?php
include "../../config.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: supplier.php");
    exit;
}

// --- CEK apakah supplier sudah dipakai di pembelian ---
$q = $conn->query("SELECT 1 FROM pembelian WHERE supplier_id=$id LIMIT 1");
if ($q && $q->num_rows > 0) {
    header("Location: supplier.php?msg=used");
    exit;
}

// --- Hapus supplier ---
$stmt = $conn->prepare("DELETE FROM supplier WHERE id=?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: supplier.php?msg=deleted");
    exit;
} else {
    header("Location: supplier.php?msg=error");
    exit;
}
