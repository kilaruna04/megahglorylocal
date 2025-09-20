<?php
include "../../../config.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=penjualan_instansi.xls");

$sql = "SELECT p.*, c.nama_customer 
        FROM penjualan_instansi p 
        LEFT JOIN customer c ON p.customer_id=c.id 
        ORDER BY p.tanggal DESC";
$result = $conn->query($sql);

echo "<table border='1'>";
echo "<tr style='background:#ddd;font-weight:bold;'>
        <th>No</th>
        <th>No Invoice</th>
        <th>Tanggal</th>
        <th>Customer</th>
        <th>Jenis Penjualan</th>
        <th>Total</th>
        <th>DPP</th>
        <th>PPN</th>
        <th>PPh</th>
        <th>Biaya Adm</th>
        <th>Total Masuk</th>
        <th>Status</th>
      </tr>";

$no=1;
while($row = $result->fetch_assoc()){
  echo "<tr>
          <td>".$no."</td>
          <td>".$row['no_invoice']."</td>
          <td>".date("d/m/Y",strtotime($row['tanggal']))."</td>
          <td>".$row['nama_customer']."</td>
          <td>".$row['jenis_penjualan']."</td>
          <td>".number_format($row['total'],0,',','.')."</td>
          <td>".number_format($row['dpp'],0,',','.')."</td>
          <td>".number_format($row['ppn'],0,',','.')."</td>
          <td>".number_format($row['pph'],0,',','.')."</td>
          <td>".number_format($row['biaya_adm'],0,',','.')."</td>
          <td>".number_format($row['total_masuk'],0,',','.')."</td>
          <td>".$row['status']."</td>
        </tr>";
  $no++;
}
echo "</table>";
?>
