<?php
include "../../../config.php";

$sql = "SELECT p.*, c.nama_customer 
        FROM penjualan_instansi p 
        LEFT JOIN customer c ON p.customer_id=c.id 
        ORDER BY p.tanggal DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Cetak Penjualan Instansi</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    h2, h3 { margin: 0; }
    table { border-collapse: collapse; width: 100%; margin-top: 15px; }
    th, td { border: 1px solid #000; padding: 6px; }
    th { background: #f0f0f0; }
    .text-center { text-align: center; }
    .text-end { text-align: right; }
    .footer { margin-top: 40px; width: 100%; display: flex; justify-content: space-between; }
    .ttd { text-align: center; width: 200px; }
  </style>
</head>
<body onload="window.print()">

  <div class="text-center">
    <h2>PT. Megah Glory</h2>
    <h3>Laporan Penjualan Instansi</h3>
    <small>Periode: <?=date("d/m/Y")?></small>
  </div>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>No Invoice</th>
        <th>Tanggal</th>
        <th>Customer</th>
        <th>Jenis</th>
        <th>Total</th>
        <th>Total Masuk</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php $no=1; while($row=$result->fetch_assoc()): ?>
      <tr>
        <td class="text-center"><?=$no++?></td>
        <td><?=$row['no_invoice']?></td>
        <td class="text-center"><?=date("d/m/Y",strtotime($row['tanggal']))?></td>
        <td><?=$row['nama_customer']?></td>
        <td class="text-center"><?=$row['jenis_penjualan']?></td>
        <td class="text-end"><?=number_format($row['total'],0,",",".")?></td>
        <td class="text-end"><?=number_format($row['total_masuk'],0,",",".")?></td>
        <td class="text-center"><?=$row['status']?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <div class="footer">
    <div class="ttd">
      <p><b>Disetujui,</b></p>
      <br><br><br>
      <p>(___________________)</p>
    </div>
    <div class="ttd">
      <p><b>Dibuat,</b></p>
      <br><br><br>
      <p>(___________________)</p>
    </div>
  </div>

</body>
</html>
