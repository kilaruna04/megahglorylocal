<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php?role=admin");
    exit;
}

include "../../../config.php";

function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

$id = intval($_GET['id']);

// Ambil data header
$sql = "SELECT pi.*, c.nama_customer 
        FROM penjualan_instansi pi
        JOIN customer c ON pi.customer_id = c.id
        WHERE pi.id = $id";
$res = $conn->query($sql);
$penjualan = $res->fetch_assoc();

// Detail SPJ
$sqlSPJ = "SELECT * FROM penjualan_instansi_detail WHERE penjualan_id=$id AND jenis_item='SPJ'";
$resSPJ = $conn->query($sqlSPJ);

// Detail Real
$sqlReal = "SELECT * FROM penjualan_instansi_detail WHERE penjualan_id=$id AND jenis_item='Real'";
$resReal = $conn->query($sqlReal);

// Piutang
$sqlPiutang = "SELECT * FROM piutang_instansi WHERE penjualan_id=$id LIMIT 1";
$resPiutang = $conn->query($sqlPiutang);
$piutang = $resPiutang->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; color:#000; }
    .header { text-align:center; margin-bottom:10px; }
    .header img { max-height:60px; }
    h3 { margin:5px 0; }
    table { width:100%; border-collapse: collapse; margin-bottom:15px; }
    th, td { border:1px solid #000; padding:6px; }
    th { background:#f2f2f2; }
    .no-border td { border:none; }
    .right { text-align:right; }
    .center { text-align:center; }
    .bold { font-weight:bold; }
    .total-row td { font-weight:bold; background:#f9f9f9; }
  </style>
</head>
<body onload="window.print()">

<!-- Baris pertama: tanggal & judul -->
<table class="no-border">
  <tr>
    <td style="text-align:left; font-size:11px;">
      <?= date("d/m/Y, H:i") ?>
    </td>
    <td style="text-align:right; font-size:11px;">
      Cetak Detail Penjualan Instansi
    </td>
  </tr>
</table>

<div class="header">
  <img src="../../../assets/img/mgtransparant2048.png" alt="Logo"><br>
  <strong>MEGAH GLORY</strong><br>
  Jl. Raya Tim. No.51, Cipakat, Kec. Singaparna, Kabupaten Tasikmalaya, Jawa Barat 46417<br>
  Telp/WA: 085290008816 | Email: admin@megahglory.com
</div>

<h3 style="text-align:center; margin-bottom:20px;">Detail Penjualan Instansi</h3>

<!-- Informasi Utama -->
<table class="no-border">
  <tr>
    <td><strong>No Invoice:</strong> <?= htmlspecialchars($penjualan['no_invoice']) ?></td>
    <td class="right"><strong>Status:</strong> <?= $penjualan['status'] ?></td>
  </tr>
  <tr>
    <td><strong>Tanggal:</strong> <?= date("d/m/Y", strtotime($penjualan['tanggal'])) ?></td>
    <td class="right"><strong>Total:</strong> <?= rupiah($penjualan['total']) ?></td>
  </tr>
  <tr>
    <td><strong>Customer:</strong> <?= htmlspecialchars($penjualan['nama_customer']) ?></td>
    <td class="right"><strong>Total Masuk:</strong> <?= rupiah($penjualan['total_masuk']) ?></td>
  </tr>
</table>

<!-- Produk SPJ -->
<h4>Produk SPJ (Manual)</h4>
<table>
  <thead>
    <tr>
      <th>Produk</th>
      <th>Qty</th>
      <th>Harga</th>
      <th>Subtotal</th>
      <th>Keterangan</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $totalSPJ = 0;
    if ($resSPJ->num_rows > 0): 
      while($row = $resSPJ->fetch_assoc()): 
        $totalSPJ += $row['subtotal']; ?>
        <tr>
          <td><?= htmlspecialchars($row['nama_produk']) ?></td>
          <td class="center"><?= $row['qty'] ?></td>
          <td class="right"><?= rupiah($row['harga']) ?></td>
          <td class="right"><?= rupiah($row['subtotal']) ?></td>
          <td><?= htmlspecialchars($row['keterangan']) ?></td>
        </tr>
      <?php endwhile; ?>
      <tr class="total-row">
        <td colspan="3" class="right">Total SPJ</td>
        <td class="right"><?= rupiah($totalSPJ) ?></td>
        <td></td>
      </tr>
    <?php else: ?>
      <tr><td colspan="5" class="center">Tidak ada produk SPJ</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- Produk Real -->
<h4>Produk Real (Database)</h4>
<table>
  <thead>
    <tr>
      <th>Produk</th>
      <th>Qty</th>
      <th>Harga</th>
      <th>Subtotal</th>
      <th>Keterangan</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $totalReal = 0;
    if ($resReal->num_rows > 0): 
      while($row = $resReal->fetch_assoc()): 
        $totalReal += $row['subtotal']; ?>
        <tr>
          <td><?= htmlspecialchars($row['nama_produk']) ?></td>
          <td class="center"><?= $row['qty'] ?></td>
          <td class="right"><?= rupiah($row['harga']) ?></td>
          <td class="right"><?= rupiah($row['subtotal']) ?></td>
          <td><?= htmlspecialchars($row['keterangan']) ?></td>
        </tr>
      <?php endwhile; ?>
      <tr class="total-row">
        <td colspan="3" class="right">Total Real</td>
        <td class="right"><?= rupiah($totalReal) ?></td>
        <td></td>
      </tr>
    <?php else: ?>
      <tr><td colspan="5" class="center">Tidak ada produk Real</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- Grand Total -->
<h4 style="text-align:right;">Grand Total: <?= rupiah($penjualan['total']) ?></h4>

<!-- Ringkasan Keuangan -->
<h4>Ringkasan Keuangan</h4>
<ul>
  <li><strong>DPP:</strong> <?= rupiah($penjualan['dpp']) ?></li>
  <li><strong>PPN:</strong> <?= rupiah($penjualan['ppn']) ?></li>
  <li><strong>PPh:</strong> <?= rupiah($penjualan['pph']) ?></li>
  <li><strong>Biaya Adm:</strong> <?= rupiah($penjualan['biaya_adm']) ?></li>
  <li><strong>Total Masuk:</strong> <?= rupiah($penjualan['total_masuk']) ?></li>
</ul>

<!-- Informasi Piutang -->
<?php if ($piutang): ?>
<h4>Informasi Piutang</h4>
<ul>
  <li><strong>Jatuh Tempo:</strong> <?= date("d/m/Y", strtotime($piutang['jatuh_tempo'])) ?></li>
  <li><strong>Sisa Piutang:</strong> <?= rupiah($piutang['sisa_piutang']) ?></li>
  <li><strong>Status:</strong> <?= $piutang['status'] ?></li>
</ul>
<?php endif; ?>

</body>
</html>
