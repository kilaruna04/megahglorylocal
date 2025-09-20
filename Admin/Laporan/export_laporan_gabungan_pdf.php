<?php
require("../../fpdf/fpdf.php");
include "../../config.php";

$awal = $_GET['awal'] ?? date("Y-m-01");
$akhir = $_GET['akhir'] ?? date("Y-m-t");

$pdf = new FPDF("P","mm","A4");
$pdf->AddPage();
$pdf->SetFont("Arial","B",14);
$pdf->Cell(0,10,"Laporan Gabungan ($awal s/d $akhir)",0,1,"C");
$pdf->Ln(5);

// Penjualan
$pdf->SetFont("Arial","B",12);
$pdf->Cell(0,7,"Penjualan",0,1);
$pdf->SetFont("Arial","B",10);
$pdf->Cell(30,7,"No Invoice",1);
$pdf->Cell(30,7,"Tanggal",1);
$pdf->Cell(60,7,"Customer",1);
$pdf->Cell(30,7,"Jenis",1);
$pdf->Cell(40,7,"Total",1);
$pdf->Ln();

$res = $conn->query("SELECT p.no_invoice,p.tanggal,c.nama_customer,p.jenis,p.total
                     FROM penjualan p
                     LEFT JOIN customer c ON p.customer_id=c.id
                     WHERE p.tanggal BETWEEN '$awal' AND '$akhir'");
$pdf->SetFont("Arial","",10);
while($r=$res->fetch_assoc()){
    $pdf->Cell(30,7,$r['no_invoice'],1);
    $pdf->Cell(30,7,$r['tanggal'],1);
    $pdf->Cell(60,7,$r['nama_customer'],1);
    $pdf->Cell(30,7,$r['jenis'],1);
    $pdf->Cell(40,7,number_format($r['total'],0,",","."),1);
    $pdf->Ln();
}

$pdf->Ln(5);
// Pembelian
$pdf->SetFont("Arial","B",12);
$pdf->Cell(0,7,"Pembelian",0,1);
$pdf->SetFont("Arial","B",10);
$pdf->Cell(30,7,"No Invoice",1);
$pdf->Cell(30,7,"Tanggal",1);
$pdf->Cell(60,7,"Supplier",1);
$pdf->Cell(30,7,"Metode",1);
$pdf->Cell(40,7,"Total",1);
$pdf->Ln();

$res = $conn->query("SELECT p.no_invoice,p.tanggal,s.nama_supplier,p.metode_bayar,p.total
                     FROM pembelian p
                     LEFT JOIN supplier s ON p.supplier_id=s.id
                     WHERE p.tanggal BETWEEN '$awal' AND '$akhir'");
$pdf->SetFont("Arial","",10);
while($r=$res->fetch_assoc()){
    $pdf->Cell(30,7,$r['no_invoice'],1);
    $pdf->Cell(30,7,$r['tanggal'],1);
    $pdf->Cell(60,7,$r['nama_supplier'],1);
    $pdf->Cell(30,7,$r['metode_bayar'],1);
    $pdf->Cell(40,7,number_format($r['total'],0,",","."),1);
    $pdf->Ln();
}

$pdf->Output();
