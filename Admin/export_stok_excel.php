<?php
session_start();
include "../config.php";

if (!isset($_SESSION['username']) || $_SESSION['role']!='admin') {
    header("Location: ../login.php?role=admin");
    exit;
}

$tgl_awal = $_GET['tgl_awal'] ?? date("Y-m-01");
$tgl_akhir = $_GET['tgl_akhir'] ?? date("Y-m-d");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_stok.xls");

echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Nama Produk</th>
        <th>Kategori</th>
        <th>Satuan</th>
        <th>Stok Awal</th>
        <th>Masuk (Beli)</th>
        <th>Keluar (Jual/Service)</th>
        <th>Stok Akhir</th>
      </tr>";

$sql = "SELECT id,nama_produk,kategori,satuan FROM produk ORDER BY nama_produk ASC";
$res = $conn->query($sql);
$no=1;
while($row=$res->fetch_assoc()){
    $id=$row['id'];

    // Hitung stok awal
    $q1=$conn->query("SELECT SUM(qty) as masuk FROM pembelian_detail pd
                      JOIN pembelian p ON pd.pembelian_id=p.id
                      WHERE pd.produk_id=$id AND p.tanggal < '$tgl_awal'");
    $masukSebelum=$q1->fetch_assoc()['masuk'] ?? 0;

    $q2=$conn->query("SELECT SUM(qty) as keluar FROM penjualan_detail pd
                      JOIN penjualan pj ON pd.penjualan_id=pj.id
                      WHERE pd.produk_id=$id AND pj.tanggal < '$tgl_awal'");
    $keluarSebelum=$q2->fetch_assoc()['keluar'] ?? 0;

    $stokAwal = $masukSebelum - $keluarSebelum;

    // Periode berjalan
    $q3=$conn->query("SELECT SUM(qty) as masuk FROM pembelian_detail pd
                      JOIN pembelian p ON pd.pembelian_id=p.id
                      WHERE pd.produk_id=$id AND p.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'");
    $masuk=$q3->fetch_assoc()['masuk'] ?? 0;

    $q4=$conn->query("SELECT SUM(qty) as keluar FROM penjualan_detail pd
                      JOIN penjualan pj ON pd.penjualan_id=pj.id
                      WHERE pd.produk_id=$id AND pj.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'");
    $keluar=$q4->fetch_assoc()['keluar'] ?? 0;

    $stokAkhir = $stokAwal + $masuk - $keluar;

    echo "<tr>
            <td>".$no++."</td>
            <td>".$row['nama_produk']."</td>
            <td>".$row['kategori']."</td>
            <td>".$row['satuan']."</td>
            <td>".$stokAwal."</td>
            <td>".$masuk."</td>
            <td>".$keluar."</td>
            <td>".$stokAkhir."</td>
          </tr>";
}
echo "</table>";
