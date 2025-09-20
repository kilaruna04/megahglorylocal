<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php?role=admin");
    exit;
}

include "../../../config.php";

header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$data = [];

if ($id > 0) {
    // Ambil daftar SN yang masih tersedia
    $stmt = $conn->prepare("SELECT id, sn FROM produk_sn WHERE produk_id=? AND status='tersedia' ORDER BY sn ASC");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
}

echo json_encode(["sn_list" => $data]);
