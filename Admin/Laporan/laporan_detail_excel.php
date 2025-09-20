<?php
session_start();
include "../config.php";
if (!isset($_SESSION['username']) || $_SESSION['role']!='admin'){exit("Unauthorized");}

$tgl_awal = $_GET['tgl_awal'] ?? date("Y-m-01");
$tgl_akhir = $_GET['tgl_akhir'] ?? date("Y-m-t");

$pembelian=$conn->query("SELECT p.no_invoice,p.tanggal,s.nama_supplier,p.total,p.metode_bayar,p.status_hutang
                         FROM pembelian p JOIN supplier s ON p.supplier_id=s.id
                         WHERE p.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'");

$penjualan=$conn->query("SELECT pj.no_invoice,pj.tanggal,c.nama_customer,pj.jenis,pj.total,pj.metode_bayar,pj.status_piutang
                         FROM penjualan pj JOIN customer c ON pj.customer_id=c.id
                         WHERE pj.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'");

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=laporan_detail.csv");

$out=fopen("php://output","w");
fputcsv($out,["Laporan Detail Transaksi"]);
fputcsv($out,["Periode",$tgl_awal." s.d ".$tgl_akhir]);
fputcsv($out,[]);

// Pembelian
fputcsv($out,["Pembelian"]);
fputcsv($out,["No Invoice","Tanggal","Supplier","Total","Metode","Status Hutang"]);
while($row=$pembelian->fetch_assoc()){
    fputcsv($out,[$row['no_invoice'],$row['tanggal'],$row['nama_supplier'],$row['total'],$row['metode_bayar'],$row['status_hutang']]);
}
fputcsv($out,[]);

// Penjualan
fputcsv($out,["Penjualan"]);
fputcsv($out,["No Invoice","Tanggal","Customer","Jenis","Total","Metode","Status Piutang"]);
while($row=$penjualan->fetch_assoc()){
    fputcsv($out,[$row['no_invoice'],$row['tanggal'],$row['nama_customer'],$row['jenis'],$row['total'],$row['metode_bayar'],$row['status_piutang']]);
}
fclose($out);
