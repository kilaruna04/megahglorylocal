<?php
include "../header.php";
include "../../config.php";

if(!isset($_GET['id'])){ header("Location: service.php"); exit; }
$id = intval($_GET['id']);

$service = $conn->query("SELECT s.*, p.no_invoice, c.nama_customer, u.nama_lengkap AS teknisi
                         FROM service s
                         LEFT JOIN penjualan p ON s.penjualan_id=p.id
                         LEFT JOIN customer c ON p.customer_id=c.id
                         LEFT JOIN users u ON s.teknisi_id=u.id
                         WHERE s.id=$id")->fetch_assoc();
if(!$service){ die("Data tidak ditemukan"); }
?>
<h2 class="fw-bold text-primary mb-4">📜 Detail Service</h2>

<div class="mb-3">
  <strong>No Service:</strong> <?=$service['no_service'];?><br>
  <strong>No Invoice:</strong> <?=$service['no_invoice'];?><br>
  <strong>Customer:</strong> <?=$service['nama_customer'];?><br>
  <strong>Teknisi:</strong> <?=$service['teknisi'];?><br>
  <strong>Status Admin:</strong> <?=$service['status_admin'];?><br>
  <strong>Status Teknisi:</strong> <?=$service['status_teknisi'];?><br>
  <strong>Catatan Admin:</strong> <?=$service['catatan_admin'];?><br>
  <strong>Catatan Teknisi:</strong> <?=$service['catatan_teknisi'];?>
</div>

<a href="service.php" class="btn btn-secondary">⬅ Kembali</a>

<?php include "../footer.php"; ?>
