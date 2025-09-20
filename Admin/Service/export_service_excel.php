<?php
include "../../../config.php";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_service.xls");

$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

$where = "1=1";
if ($tgl_awal && $tgl_akhir) {
    $where = "s.created_at BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$sql = "SELECT s.*, p.no_invoice, c.nama_customer, u.nama_lengkap AS teknisi
        FROM service s
        JOIN penjualan p ON s.penjualan_id=p.id
        JOIN customer c ON p.customer_id=c.id
        LEFT JOIN users u ON s.teknisi_id=u.id
        WHERE $where
        ORDER BY s.created_at DESC";
$result = $conn->query($sql);

echo "<table border='1'>
<tr>
  <th>No Service</th>
  <th>No Invoice</th>
  <th>Customer</th>
  <th>Teknisi</th>
  <th>Status</th>
  <th>Catatan Teknisi</th>
  <th>Tanggal</th>
</tr>";
while($row=$result->fetch_assoc()){
    echo "<tr>
      <td>{$row['no_service']}</td>
      <td>{$row['no_invoice']}</td>
      <td>{$row['nama_customer']}</td>
      <td>{$row['teknisi']}</td>
      <td>{$row['status_teknisi']}</td>
      <td>{$row['catatan_teknisi']}</td>
      <td>{$row['created_at']}</td>
    </tr>";
}
echo "</table>";
