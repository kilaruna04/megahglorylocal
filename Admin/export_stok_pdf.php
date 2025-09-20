<?php
session_start();
include "../config.php";
require("../fpdf/fpdf.php");

if (!isset($_SESSION['username']) || $_SESSION['role']!='admin') {
    header("Location: ../login.php?role=admin");
    exit;
}

$tgl_awal = $_GET['tgl_awal'] ?? date("Y-m-01");
$tgl_akhir = $_GET['tgl_akhir'] ?? date("Y-m-d");

$pdf = new FPDF("L","mm","A4");
$pdf->AddPage();
$pdf->SetFont("Arial","B",14);
$pdf->Cell(0,10,"LAPORAN STOK BARANG",0,1,"C");
$pdf->SetFont("Arial","",10);
$pdf->Cell(0,8,"Periode: $tgl_awal s/d $tgl_akhir",0,1,"C");
$pdf->Ln(3);

$pdf->SetFont("Arial","B",10);
$pdf->Cell(10,10,"No",1,0,"C");
$pdf->Cell(60,10,"Nama Produk",1,0,"C");
$pdf->Cell(35,10,"Kategori",1,0,"C");
$pdf->Cell(20,10,"Satuan",1,0,"C");
$pdf->Cell(25,10,"Stok Awal",1,0,"C");
$pdf->Cell(25,10,"Masuk",1,0,"C");
$pdf->Cell(25,10,"Keluar",1,0,"C");
$pdf->Cell(25,10,"Stok Akhir",1,1,"C");

$pdf->SetFont("Arial","",10);

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

    $pdf->Cell(10,8,$no++,1,0,"C");
    $pdf->Cell(60,8,$row['nama_produk'],1,0);
    $pdf->Cell(35,8,$row['kategori'],1,0,"C");
    $pdf->Cell(20,8,$row['satuan'],1,0,"C");
    $pdf->Cell(25,8,$stokAwal,1,0,"C");
    $pdf->Cell(25,8,$masuk,1,0,"C");
    $pdf->Cell(25,8,$keluar,1,0,"C");
    $pdf->Cell(25,8,$stokAkhir,1,1,"C");
}

$pdf->Output();
