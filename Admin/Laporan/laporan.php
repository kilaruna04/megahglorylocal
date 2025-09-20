<?php
include "../header.php";
include "../../config.php";

// Hitung ringkas
$total_penjualan = $conn->query("SELECT SUM(total) as total FROM penjualan")->fetch_assoc()['total'] ?? 0;
$total_pembelian = $conn->query("SELECT SUM(total) as total FROM pembelian")->fetch_assoc()['total'] ?? 0;
$total_hutang    = $conn->query("SELECT SUM(sisa_hutang) as total FROM hutang WHERE status='Belum Lunas'")->fetch_assoc()['total'] ?? 0;
$total_piutang   = $conn->query("SELECT SUM(sisa_piutang) as total FROM piutang WHERE status='Belum Lunas'")->fetch_assoc()['total'] ?? 0;
?>
<h2 class="fw-bold text-primary mb-4">📊 Laporan Ringkas</h2>

<div class="row">
  <div class="col-md-3">
    <div class="card text-white bg-success mb-3">
      <div class="card-body">
        <h5 class="card-title">Total Penjualan</h5>
        <p class="card-text">Rp <?=number_format($total_penjualan,0,",",".");?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-warning mb-3">
      <div class="card-body">
        <h5 class="card-title">Total Pembelian</h5>
        <p class="card-text">Rp <?=number_format($total_pembelian,0,",",".");?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-danger mb-3">
      <div class="card-body">
        <h5 class="card-title">Total Hutang</h5>
        <p class="card-text">Rp <?=number_format($total_hutang,0,",",".");?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-info mb-3">
      <div class="card-body">
        <h5 class="card-title">Total Piutang</h5>
        <p class="card-text">Rp <?=number_format($total_piutang,0,",",".");?></p>
      </div>
    </div>
  </div>
</div>

<a href="laporan_detail.php" class="btn btn-primary">📑 Laporan Detail</a>
<a href="laporan_stok.php" class="btn btn-secondary">📦 Laporan Stok</a>
<a href="laporan_service.php" class="btn btn-info">🛠 Laporan Service</a>

<?php include "../footer.php"; ?>
