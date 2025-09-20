<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php?role=admin");
    exit;
}

include "../../../config.php";
include "../../../header.php";

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

// Ambil detail produk SPJ & Real
$sqlSPJ = "SELECT * FROM penjualan_instansi_detail WHERE penjualan_id=$id AND jenis_item='SPJ'";
$resSPJ = $conn->query($sqlSPJ);

$sqlReal = "SELECT * FROM penjualan_instansi_detail WHERE penjualan_id=$id AND jenis_item='Real'";
$resReal = $conn->query($sqlReal);

// Ambil data piutang instansi
$sqlPiutang = "SELECT * FROM piutang_instansi WHERE penjualan_id=$id LIMIT 1";
$resPiutang = $conn->query($sqlPiutang);
$piutang = $resPiutang->fetch_assoc();
?>

<div class="container-fluid">
  <!-- Judul + Tombol -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="fa fa-file"></i> Detail Penjualan Instansi</h4>
    <div class="d-flex gap-2">
      <a href="print_detail.php?id=<?= $id ?>" target="_blank" class="btn btn-secondary">
        <i class="fa fa-print"></i> Cetak
      </a>
      <a href="index.php" class="btn btn-primary">
        <i class="fa fa-arrow-left"></i> Kembali
      </a>
    </div>
  </div>

  <!-- Header -->
  <div class="card-dark p-3 mb-3">
    <div class="row">
      <div class="col-md-6">
        <p><strong>No Invoice:</strong> <?= htmlspecialchars($penjualan['no_invoice']) ?></p>
        <p><strong>Tanggal:</strong> <?= date("d/m/Y", strtotime($penjualan['tanggal'])) ?></p>
        <p><strong>Customer:</strong> <?= htmlspecialchars($penjualan['nama_customer']) ?></p>
      </div>
      <div class="col-md-6 text-end">
        <p><strong>Status:</strong> 
          <span class="badge <?= $penjualan['status']=='Lunas'?'bg-success':'bg-warning text-dark' ?>">
            <?= $penjualan['status'] ?>
          </span>
        </p>
        <p><strong>Total:</strong> <?= rupiah($penjualan['total']) ?></p>
        <p><strong>Total Masuk:</strong> <?= rupiah($penjualan['total_masuk']) ?></p>
      </div>
    </div>
  </div>

  <!-- Produk SPJ -->
  <div class="card-dark p-3 mb-3">
    <h6><i class="fa fa-file"></i> Produk SPJ (Manual)</h6>
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-primary text-center">
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
            $totalSPJ += $row['subtotal'];
        ?>
          <tr>
            <td><?= htmlspecialchars($row['nama_produk']) ?></td>
            <td class="text-center"><?= $row['qty'] ?></td>
            <td class="text-end"><?= rupiah($row['harga']) ?></td>
            <td class="text-end"><?= rupiah($row['subtotal']) ?></td>
            <td><?= htmlspecialchars($row['keterangan']) ?></td>
          </tr>
        <?php endwhile; ?>
          <tr class="fw-bold">
            <td colspan="3" class="text-end">Total SPJ</td>
            <td class="text-end"><?= rupiah($totalSPJ) ?></td>
            <td></td>
          </tr>
        <?php else: ?>
          <tr><td colspan="5" class="text-center">Tidak ada produk SPJ</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Produk Real -->
  <div class="card-dark p-3 mb-3">
    <h6><i class="fa fa-box"></i> Produk Real (Database)</h6>
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-success text-center">
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
            $totalReal += $row['subtotal'];
        ?>
          <tr>
            <td><?= htmlspecialchars($row['nama_produk']) ?></td>
            <td class="text-center"><?= $row['qty'] ?></td>
            <td class="text-end"><?= rupiah($row['harga']) ?></td>
            <td class="text-end"><?= rupiah($row['subtotal']) ?></td>
            <td><?= htmlspecialchars($row['keterangan']) ?></td>
          </tr>
        <?php endwhile; ?>
          <tr class="fw-bold">
            <td colspan="3" class="text-end">Total Real</td>
            <td class="text-end"><?= rupiah($totalReal) ?></td>
            <td></td>
          </tr>
        <?php else: ?>
          <tr><td colspan="5" class="text-center">Tidak ada produk Real</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Grand Total -->
  <?php $grandTotal = $totalSPJ + $totalReal; ?>
  <h6 class="text-end fw-bold">Grand Total: <?= rupiah($grandTotal) ?></h6>

  <!-- Ringkasan Keuangan -->
  <div class="card-dark p-3 mb-3">
    <h6>💰 Ringkasan Keuangan</h6>
    <ul>
      <li><strong>DPP:</strong> <?= rupiah($penjualan['dpp']) ?></li>
      <li><strong>PPN:</strong> <?= rupiah($penjualan['ppn']) ?></li>
      <li><strong>PPh:</strong> <?= rupiah($penjualan['pph']) ?></li>
      <li><strong>Biaya Adm:</strong> <?= rupiah($penjualan['biaya_adm']) ?></li>
      <li><strong>Total Masuk:</strong> <?= rupiah($penjualan['total_masuk']) ?></li>
    </ul>
  </div>

  <!-- Informasi Piutang -->
  <?php if ($piutang): ?>
  <div class="card-dark p-3 mb-3">
    <h6>📌 Informasi Piutang</h6>
    <p><strong>Jatuh Tempo:</strong> <?= date("d/m/Y", strtotime($piutang['jatuh_tempo'])) ?></p>
    <p><strong>Sisa Piutang:</strong> <?= rupiah($piutang['sisa_piutang']) ?></p>
    <p><strong>Status:</strong> <?= $piutang['status'] ?></p>
  </div>
  <?php endif; ?>
</div>

<?php include "../../../footer.php"; ?>
