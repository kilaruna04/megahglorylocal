<?php
include "../../../config.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil header transaksi
$sql = "SELECT p.*, c.nama_customer 
        FROM penjualan_instansi p 
        LEFT JOIN customer c ON p.customer_id=c.id 
        WHERE p.id=$id";
$data = $conn->query($sql)->fetch_assoc();

// Set header untuk Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=detail_penjualan_instansi_".$data['no_invoice'].".xls");

// Cetak header transaksi
echo "<h3>Detail Penjualan Instansi</h3>";
echo "<p><b>No Invoice:</b> ".$data['no_invoice']."</p>";
echo "<p><b>Tanggal:</b> ".date("d/m/Y",strtotime($data['tanggal']))."</p>";
echo "<p><b>Customer:</b> ".$data['nama_customer']."</p>";
echo "<p><b>Status:</b> ".$data['status']."</p>";
echo "<p><b>Total:</b> Rp ".number_format($data['total'],0,',','.')."</p>";

// Ambil detail produk
$detail = $conn->query("SELECT * FROM penjualan_instansi_detail WHERE penjualan_id=$id");

// Cetak tabel produk
echo "<table border='1'>";
echo "<tr style='background:#ddd;font-weight:bold;'>
        <th>No</th>
        <th>Nama Produk</th>
        <th>Qty</th>
        <th>Harga</th>
        <th>Subtotal</th>
        <th>Jenis</th>
      </tr>";

$no=1;
while($row=$detail->fetch_assoc()){
  echo "<tr>
          <td>".$no."</td>
          <td>".$row['nama_produk']."</td>
          <td class='text-center'>".$row['qty']."</td>
          <td>".number_format($row['harga'],0,',','.')."</td>
          <td>".number_format($row['subtotal'],0,',','.')."</td>
          <td>".$row['jenis_item']."</td>
        </tr>";
  $no++;
}
echo "</table>";
?>
