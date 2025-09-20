<?php
if (!isset($_SESSION)) { session_start(); }
include "../../config.php";

function e($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

$q = trim($_GET['q'] ?? '');
$html = "";

if ($q !== "") {
    $safe = $conn->real_escape_string($q);
    $sql = "SELECT id, nama_produk, kode_barcode, harga_beli 
            FROM produk 
            WHERE nama_produk LIKE '%$safe%' OR kode_barcode LIKE '%$safe%'
            ORDER BY nama_produk ASC 
            LIMIT 15";
    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
        $html .= '
        <div class="table-responsive mt-2">
          <table class="table-modern align-middle">
            <thead>
              <tr>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th class="text-right">Harga</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>';
        while ($row = $res->fetch_assoc()) {
            $html .= '
              <tr class="rowProduk" data-id="'.$row['id'].'">
                <td class="text-center">'.e($row['kode_barcode']).'</td>
                <td>'.e($row['nama_produk']).'</td>
                <td class="text-right">Rp '.number_format($row['harga_beli'],0,',','.').'</td>
                <td class="text-center">
                  <button type="button" class="btn-aksi btn-aksi-success btnAdd"
                          data-id="'.$row['id'].'" data-qty="1">
                    <i class="fa fa-plus"></i> Tambah
                  </button>
                </td>
              </tr>';
        }
        $html .= '</tbody></table></div>';
    } else {
        $html = '<div class="text-muted mt-2">Tidak ada produk ditemukan.</div>';
    }
}

echo $html;
