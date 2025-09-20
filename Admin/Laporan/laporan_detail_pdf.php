<?php
session_start();
include "../config.php";
require "../fpdf/fpdf.php";

if (!isset($_SESSION['username']) || $_SESSION['role']!='admin'){exit("Unauthorized");}

$tgl_awal = $_GET['tgl_awal'] ?? date("Y-m-01");
$tgl_akhir = $_GET['tgl_akhir'] ?? date("Y-m-t");

// ambil pembelian
$pembelian=$conn->query("SELECT p.no_invoice,p.tanggal,s.nama_supplier,p.total,p.metode_bayar,p.status_hutang
                         FROM pembelian p JOIN supplier s ON p.supplier_id=s.id
                         WHERE p.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'");

// ambil penjualan
$penjualan=$conn->query("SELECT pj.no_invoice,pj.tanggal,c.nama_customer,pj.jenis,pj.total,pj.metode_bayar,pj.status_piutang
                         FROM penjualan pj JOIN customer c ON pj.customer_id=c.id
                         WHERE pj.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'");

$pdf = new FPDF("L","mm","A4");
$pdf->AddPage();
$pdf->SetFont("Arial","B",14);
$pdf->Cell(0,10,"Laporan Detail Transaksi ($tgl_awal s.d $tgl_akhir)",0,1,"C");

$pdf->Ln(5);
$pdf->SetFont("Arial","B",12);
$pdf->Cell(0,10,"Pembelian",0,1);

$pdf->SetFont("Arial","",10);
foreach(["No Invoice","Tanggal","Supplier","Total","Metode","Status Hutang"] as $col){
    $pdf->Cell(45,8,$col,1,0,"C");
}
$pdf->Ln();
while($row=$pembelian->fetch_assoc()){
    $pdf->Cell(45,8,$row['no_invoice'],1);
    $pdf->Cell(45,8,$row['tanggal'],1);
    $pdf->Cell(45,8,$row['nama_supplier'],1);
    $pdf->Cell(45,8,number_format($row['total'],0,",","."),1,0,"R");
    $pdf->Cell(45,8,$row['metode_bayar'],1);
    $pdf->Cell(45,8,$row['status_hutang'],1);
    $pdf->Ln();
}

$pdf->Ln(5);
$pdf->SetFont("Arial","B",12);
$pdf->Cell(0,10,"Penjualan",0,1);

$pdf->SetFont("Arial","",10);
foreach(["No Invoice","Tanggal","Customer","Jenis","Total","Metode","Status Piutang"] as $col){
    $pdf->Cell(40,8,$col,1,0,"C");
}
$pdf->Ln();
while($row=$penjualan->fetch_assoc()){
    $pdf->Cell(40,8,$row['no_invoice'],1);
    $pdf->Cell(40,8,$row['tanggal'],1);
    $pdf->Cell(40,8,$row['nama_customer'],1);
    $pdf->Cell(40,8,$row['jenis'],1);
    $pdf->Cell(40,8,number_format($row['total'],0,",","."),1,0,"R");
    $pdf->Cell(40,8,$row['metode_bayar'],1);
    $pdf->Cell(40,8,$row['status_piutang'],1);
    $pdf->Ln();
}

$pdf->Output("I","laporan_detail.pdf");
