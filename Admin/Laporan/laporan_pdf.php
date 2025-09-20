<?php
session_start();
include "../config.php";
require "../fpdf/fpdf.php"; // pastikan FPDF sudah ada di ../fpdf/

if (!isset($_SESSION['username']) || $_SESSION['role']!='admin'){exit("Unauthorized");}

$tgl_awal = $_GET['tgl_awal'] ?? date("Y-m-01");
$tgl_akhir = $_GET['tgl_akhir'] ?? date("Y-m-t");

$tot_pembelian = $conn->query("SELECT SUM(total) as total FROM pembelian WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'")->fetch_assoc()['total'] ?? 0;
$tot_penjualan = $conn->query("SELECT SUM(total) as total FROM penjualan WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'")->fetch_assoc()['total'] ?? 0;
$hutang = $conn->query("SELECT SUM(sisa_hutang) as total FROM hutang WHERE status='Belum Lunas'")->fetch_assoc()['total'] ?? 0;
$piutang = $conn->query("SELECT SUM(sisa_piutang) as total FROM piutang WHERE status='Belum Lunas'")->fetch_assoc()['total'] ?? 0;

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont("Arial","B",16);
$pdf->Cell(0,10,"Laporan Keuangan",0,1,"C");

$pdf->SetFont("Arial","",12);
$pdf->Cell(0,8,"Periode: $tgl_awal s.d $tgl_akhir",0,1,"C");
$pdf->Ln(5);

$pdf->SetFont("Arial","B",12);
$pdf->Cell(90,10,"Kategori",1,0,"C");
$pdf->Cell(90,10,"Jumlah (Rp)",1,1,"C");

$pdf->SetFont("Arial","",12);
$pdf->Cell(90,10,"Total Pembelian",1,0);
$pdf->Cell(90,10,number_format($tot_pembelian,0,",","."),1,1,"R");
$pdf->Cell(90,10,"Total Penjualan",1,0);
$pdf->Cell(90,10,number_format($tot_penjualan,0,",","."),1,1,"R");
$pdf->Cell(90,10,"Hutang Berjalan",1,0);
$pdf->Cell(90,10,number_format($hutang,0,",","."),1,1,"R");
$pdf->Cell(90,10,"Piutang Berjalan",1,0);
$pdf->Cell(90,10,number_format($piutang,0,",","."),1,1,"R");

$pdf->Output("I","laporan_keuangan.pdf");
