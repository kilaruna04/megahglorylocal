<?php
require('../../../fpdf/fpdf.php');
include "../../../config.php";

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

$pdf = new FPDF("L","mm","A4");
$pdf->AddPage();
$pdf->SetFont("Arial","B",14);
$pdf->Cell(0,10,"Laporan Service",0,1,"C");
$pdf->Ln(5);

$pdf->SetFont("Arial","B",10);
$pdf->Cell(30,10,"No Service",1);
$pdf->Cell(30,10,"No Invoice",1);
$pdf->Cell(50,10,"Customer",1);
$pdf->Cell(40,10,"Teknisi",1);
$pdf->Cell(25,10,"Status",1);
$pdf->Cell(70,10,"Catatan Teknisi",1);
$pdf->Cell(30,10,"Tanggal",1);
$pdf->Ln();

$pdf->SetFont("Arial","",9);
while($row=$result->fetch_assoc()){
    $pdf->Cell(30,10,$row['no_service'],1);
    $pdf->Cell(30,10,$row['no_invoice'],1);
    $pdf->Cell(50,10,$row['nama_customer'],1);
    $pdf->Cell(40,10,$row['teknisi'],1);
    $pdf->Cell(25,10,$row['status_teknisi'],1);
    $pdf->Cell(70,10,substr($row['catatan_teknisi'],0,30),1);
    $pdf->Cell(30,10,$row['created_at'],1);
    $pdf->Ln();
}

$pdf->Output("D","laporan_service.pdf");
