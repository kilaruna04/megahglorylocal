<?php
include "../../config.php";
require(__DIR__ . "/../../assets/FPDF/fpdf.php");

$q    = $_GET['q'] ?? '';
$awal = $_GET['awal'] ?? '';
$akhir= $_GET['akhir'] ?? '';

$where = [];
if ($q !== '') {
    $q = $conn->real_escape_string($q);
    $where[] = "(p.no_invoice LIKE '%$q%' OR s.nama_supplier LIKE '%$q%')";
}
if ($awal !== '' && $akhir !== '') {
    $where[] = "p.tanggal BETWEEN '$awal' AND '$akhir'";
}
$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

$sql = "
    SELECT p.no_invoice, p.tanggal, p.metode_bayar, p.total, p.status_hutang, s.nama_supplier
    FROM pembelian p
    LEFT JOIN supplier s ON p.supplier_id = s.id
    $whereSql
    ORDER BY p.tanggal DESC
";
$result = $conn->query($sql);

// PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// === Kop Perusahaan ===
if (file_exists(__DIR__ . "/../../assets/images/logo.png")) {
    $pdf->Image(__DIR__ . "/../../assets/images/logo.png", 15, 8, 20);
}
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,7,'MEGAH GLORY',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,'Jl. Raya Tim No.51, Cipakat, Singaparna, Tasikmalaya',0,1,'C');
$pdf->Cell(0,5,'Telp: 0852-9000-8816 | Email: admin@megahglory.com',0,1,'C');
$pdf->Ln(5);
$pdf->Line(10,35,200,35);
$pdf->Ln(10);

// === Judul Laporan ===
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,6,'LAPORAN PEMBELIAN',0,1,'C');

$periode = "Semua Periode";
if ($awal && $akhir) {
    $periode = date('d-m-Y', strtotime($awal)) . " s/d " . date('d-m-Y', strtotime($akhir));
}
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,'Periode: ' . $periode,0,1,'C');
$pdf->Cell(0,6,'Tanggal Cetak: '.date('d-m-Y'),0,1,'C');
$pdf->Ln(5);

// === Header Tabel ===
$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,8,'No',1,0,'C');
$pdf->Cell(30,8,'Invoice',1,0,'C');
$pdf->Cell(25,8,'Tanggal',1,0,'C');
$pdf->Cell(50,8,'Supplier',1,0,'C');
$pdf->Cell(25,8,'Metode',1,0,'C');
$pdf->Cell(25,8,'Status',1,0,'C');
$pdf->Cell(25,8,'Total',1,1,'C');

// === Isi Tabel ===
$pdf->SetFont('Arial','',10);
$grandTotal = 0;
$no = 1;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(10,7,$no++,1,0,'C'); // No rata tengah
        $pdf->Cell(30,7,$row['no_invoice'],1,0,'C'); // Invoice rata tengah
        $pdf->Cell(25,7,date('d-m-Y', strtotime($row['tanggal'])),1,0,'C'); // Tanggal rata tengah
        $pdf->Cell(50,7,$row['nama_supplier'],1,0,'C'); // Supplier rata tengah
        $pdf->Cell(25,7,$row['metode_bayar'],1,0,'C'); // Metode rata tengah
        $pdf->Cell(25,7,$row['status_hutang'],1,0,'C'); // Status rata tengah
        $pdf->Cell(25,7,number_format($row['total'],0,',','.'),1,1,'R'); // Total rata kanan
        $grandTotal += $row['total'];
    }

    // Baris Total
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(165,8,'Grand Total',1,0,'R');
    $pdf->Cell(25,8,number_format($grandTotal,0,',','.'),1,1,'R');
} else {
    $pdf->Cell(190,7,'Tidak ada data',1,1,'C');
}

// === Tanda tangan ===
$pdf->Ln(15);
$pdf->SetFont('Arial','',10);
$pdf->Cell(120,6,'',0,0);
$pdf->Cell(70,6,'Tasikmalaya, '.date('d-m-Y'),0,1,'C');
$pdf->Ln(20);
$pdf->Cell(120,6,'',0,0);
$pdf->Cell(70,6,'( ______________________ )',0,1,'C');

$pdf->Output('I','Laporan_Pembelian.pdf');
