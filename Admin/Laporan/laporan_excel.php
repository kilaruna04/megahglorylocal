<?php
session_start();
include "../config.php";
if (!isset($_SESSION['username']) || $_SESSION['role']!='admin'){exit("Unauthorized");}

$tgl_awal = $_GET['tgl_awal'] ?? date("Y-m-01");
$tgl_akhir = $_GET['tgl_akhir'] ?? date("Y-m-t");

$tot_pembelian = $conn->query("SELECT SUM(total) as total FROM pembelian WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'")->fetch_assoc()['total'] ?? 0;
$tot_penjualan = $conn->query("SELECT SUM(total) as total FROM penjualan WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'")->fetch_assoc()['total'] ?? 0;
$hutang = $conn->query("SELECT SUM(sisa_hutang) as total FROM hutang WHERE status='Belum Lunas'")->fetch_assoc()['total'] ?? 0;
$piutang = $conn->query("SELECT SUM(sisa_piutang) as total FROM piutang WHERE status='Belum Lunas'")->fetch_assoc()['total'] ?? 0;

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=laporan_keuangan.csv");

$output = fopen("php://output","w");
fputcsv($output, ["Laporan Keuangan"]);
fputcsv($output, ["Periode", $tgl_awal." s.d ".$tgl_akhir]);
fputcsv($output, []);
fputcsv($output, ["Kategori","Jumlah (Rp)"]);
fputcsv($output, ["Total Pembelian", $tot_pembelian]);
fputcsv($output, ["Total Penjualan", $tot_penjualan]);
fputcsv($output, ["Hutang Berjalan", $hutang]);
fputcsv($output, ["Piutang Berjalan", $piutang]);
fclose($output);
