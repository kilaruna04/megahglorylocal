<?php
require("../fpdf/fpdf.php");
include "../config.php";

if (!isset($_GET['type']) || !isset($_GET['id'])) {
    die("Parameter tidak lengkap. Gunakan ?type=penjualan|pembelian&id=xx");
}

$type = $_GET['type']; // penjualan / pembelian
$id   = intval($_GET['id']);

// Ambil data transaksi
if ($type == "penjualan") {
    $sql = "SELECT p.*, c.nama_customer AS pihak, c.alamat, c.no_telp
            FROM penjualan p
            LEFT JOIN customer c ON p.customer_id=c.id
            WHERE p.id=$id";
    $data = $conn->query($sql)->fetch_assoc();
    if (!$data) die("Data penjualan tidak ditemukan");
    $judul = "Kwitansi Penjualan";
} elseif ($type == "pembelian") {
    $sql = "SELECT p.*, s.nama_supplier AS pihak, s.alamat, s.no_telp
            FROM pembelian p
            LEFT JOIN supplier s ON p.supplier_id=s.id
            WHERE p.id=$id";
    $data = $conn->query($sql)->fetch_assoc();
    if (!$data) die("Data pembelian tidak ditemukan");
    $judul = "Kwitansi Pembelian";
} else {
    die("Type tidak valid");
}

// Cari apakah sudah ada kwitansi sebelumnya
$cek = $conn->query("SELECT * FROM kwitansi WHERE type='$type' AND transaksi_id=$id")->fetch_assoc();
if ($cek) {
    $no_kwitansi = $cek['no_kwitansi'];
} else {
    // Generate nomor baru
    $last = $conn->query("SELECT no_kwitansi FROM kwitansi ORDER BY id DESC LIMIT 1")->fetch_assoc();
    if ($last) {
        $urut = intval(substr($last['no_kwitansi'], -5)) + 1;
    } else {
        $urut = 1;
    }
    $no_kwitansi = "KWT/MG-".str_pad($urut,5,"0",STR_PAD_LEFT);

    // Simpan ke tabel kwitansi
    $stmt = $conn->prepare("INSERT INTO kwitansi (no_kwitansi,type,transaksi_id,total) VALUES (?,?,?,?)");
    $stmt->bind_param("ssii",$no_kwitansi,$type,$id,$data['total']);
    $stmt->execute();
}

$pdf = new FPDF("P","mm","A5");
$pdf->AddPage();
$pdf->SetFont("Arial","B",14);
$pdf->Cell(0,10,$judul,0,1,"C");

$pdf->SetFont("Arial","",10);
$pdf->Cell(40,8,"No Kwitansi",0,0);
$pdf->Cell(70,8,": ".$no_kwitansi,0,1);

$pdf->Cell(40,8,"No Invoice",0,0);
$pdf->Cell(70,8,": ".$data['no_invoice'],0,1);

$pdf->Cell(40,8,"Tanggal",0,0);
$pdf->Cell(70,8,": ".$data['tanggal'],0,1);

$pdf->Cell(40,8,"Diterima dari",0,0);
$pdf->Cell(70,8,": ".$data['pihak'],0,1);

$pdf->Cell(40,8,"Alamat",0,0);
$pdf->MultiCell(100,8,": ".$data['alamat'],0,1);

$pdf->Cell(40,8,"No Telp",0,0);
$pdf->Cell(70,8,": ".$data['no_telp'],0,1);

$pdf->Ln(5);
$pdf->SetFont("Arial","B",12);
$pdf->Cell(0,8,"Jumlah: Rp ".number_format($data['total'],0,",","."),0,1);

$pdf->Ln(20);
$pdf->SetFont("Arial","",10);
$pdf->Cell(90,8,"Penerima,",0,0,"C");
$pdf->Cell(90,8,"Hormat Kami,",0,1,"C");

$pdf->Ln(20);
$pdf->Cell(90,8,"__________________",0,0,"C");
$pdf->Cell(90,8,"__________________",0,1,"C");

$pdf->Output();
