<?php
include "../../../config.php";
include "../../header.php";

if (!isset($_GET['id'])) { header("Location: service.php"); exit; }
$id = intval($_GET['id']);

$service = $conn->query("SELECT s.*, p.no_invoice, c.nama_customer, u.nama_lengkap AS teknisi
                         FROM service s
                         JOIN penjualan p ON s.penjualan_id=p.id
                         JOIN customer c ON p.customer_id=c.id
                         LEFT JOIN users u ON s.teknisi_id=u.id
                         WHERE s.id=$id")->fetch_assoc();
if (!$service) { header("Location: service.php"); exit; }
?>
<h2 class="fw-bold text-primary mb-4">📜 Detail Service</h2>
<a href="service.php" class="btn btn-secondary mb-3">⬅ Kembali</a>

<table class="table table-bordered">
  <tr><th>No Service</th><td><?=$service['no_service'];?></td></tr>
  <tr><th>No Invoice</th><td><?=$service['no_invoice'];?></td></tr>
  <tr><th>Customer</th><td><?=$service['nama_customer'];?></td></tr>
  <tr><th>Teknisi</th><td><?=$service['teknisi'];?></td></tr>
  <tr><th>Status Admin</th><td><?=$service['status_admin'];?></td></tr>
  <tr><th>Status Teknisi</th><td><?=$service['status_teknisi'];?></td></tr>
  <tr><th>Catatan Admin</th><td><?=$service['catatan_admin'];?></td></tr>
  <tr><th>Catatan Teknisi</th><td><?=$service['catatan_teknisi'];?></td></tr>
</table>
