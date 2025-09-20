<?php
include "../../config.php";

$awal = $_GET['awal'] ?? date("Y-m-01");
$akhir = $_GET['akhir'] ?? date("Y-m-t");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_gabungan.xls");

echo "LAPORAN GABUNGAN ($awal s/d $akhir)\n\n";

echo "=== PENJUALAN ===\n";
$res = $conn->query("SELECT p.no_invoice,p.tanggal,c.nama_customer,p.jenis,p.total
                     FROM penjualan p
                     LEFT JOIN customer c ON p.customer_id=c.id
                     WHERE p.tanggal BETWEEN '$awal' AND '$akhir'");
echo "No Invoice\tTanggal\tCustomer\tJenis\tTotal\n";
while($r=$res->fetch_assoc()){
    echo "{$r['no_invoice']}\t{$r['tanggal']}\t{$r['nama_customer']}\t{$r['jenis']}\t{$r['total']}\n";
}

echo "\n=== PEMBELIAN ===\n";
$res = $conn->query("SELECT p.no_invoice,p.tanggal,s.nama_supplier,p.metode_bayar,p.total
                     FROM pembelian p
                     LEFT JOIN supplier s ON p.supplier_id=s.id
                     WHERE p.tanggal BETWEEN '$awal' AND '$akhir'");
echo "No Invoice\tTanggal\tSupplier\tMetode\tTotal\n";
while($r=$res->fetch_assoc()){
    echo "{$r['no_invoice']}\t{$r['tanggal']}\t{$r['nama_supplier']}\t{$r['metode_bayar']}\t{$r['total']}\n";
}
exit;
