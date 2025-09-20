<?php
include "../config.php";
include "../header.php";

// ========================
// Query ringkasan data
// ========================

// Penjualan Toko
$res = $conn->query("SELECT SUM(total) AS total FROM penjualan_toko");
$penjualan_toko = ($res && $row=$res->fetch_assoc()) ? (int)$row['total'] : 0;

// Penjualan Instansi
$res = $conn->query("SELECT SUM(total) AS total FROM penjualan_instansi");
$penjualan_instansi = ($res && $row=$res->fetch_assoc()) ? (int)$row['total'] : 0;

// Penjualan Service
$res = $conn->query("SELECT SUM(total) AS total FROM penjualan_service");
$penjualan_service = ($res && $row=$res->fetch_assoc()) ? (int)$row['total'] : 0;

// Piutang
$res = $conn->query("SELECT SUM(sisa_piutang) AS total FROM piutang_toko");
$piutang_toko = ($res && $row=$res->fetch_assoc()) ? (int)$row['total'] : 0;
$res = $conn->query("SELECT SUM(sisa_piutang) AS total FROM piutang_instansi");
$piutang_instansi = ($res && $row=$res->fetch_assoc()) ? (int)$row['total'] : 0;
$res = $conn->query("SELECT SUM(sisa_piutang) AS total FROM piutang_service");
$piutang_service = ($res && $row=$res->fetch_assoc()) ? (int)$row['total'] : 0;

// Total Pembelian
$res = $conn->query("SELECT SUM(total) AS total FROM pembelian");
$total_pembelian = ($res && $row=$res->fetch_assoc()) ? (int)$row['total'] : 0;

// Total Hutang
$res = $conn->query("SELECT SUM(sisa_hutang) AS total FROM hutang");
$total_hutang = ($res && $row=$res->fetch_assoc()) ? (int)$row['total'] : 0;

// Produk hampir habis
$res = $conn->query("SELECT COUNT(*) AS jml FROM produk WHERE stok <= 5");
$produk_hampir_habis = ($res && $row=$res->fetch_assoc()) ? (int)$row['jml'] : 0;

// ========================
// Data Grafik (semua bulan)
// ========================
$bulan = [];
$penjualanToko = [];
$penjualanInstansi = [];
$penjualanService = [];
$pembelian = [];

// Penjualan Toko
$res = $conn->query("
  SELECT DATE_FORMAT(tanggal, '%b %Y') AS bulan, SUM(total) AS total
  FROM penjualan_toko
  GROUP BY YEAR(tanggal), MONTH(tanggal)
  ORDER BY tanggal
");
$tokoMap = [];
while ($row = $res->fetch_assoc()) {
  $tokoMap[$row['bulan']] = (int)$row['total'];
}

// Penjualan Instansi
$res = $conn->query("
  SELECT DATE_FORMAT(tanggal, '%b %Y') AS bulan, SUM(total) AS total
  FROM penjualan_instansi
  GROUP BY YEAR(tanggal), MONTH(tanggal)
  ORDER BY tanggal
");
$instansiMap = [];
while ($row = $res->fetch_assoc()) {
  $instansiMap[$row['bulan']] = (int)$row['total'];
}

// Penjualan Service
$res = $conn->query("
  SELECT DATE_FORMAT(tanggal, '%b %Y') AS bulan, SUM(total) AS total
  FROM penjualan_service
  GROUP BY YEAR(tanggal), MONTH(tanggal)
  ORDER BY tanggal
");
$serviceMap = [];
while ($row = $res->fetch_assoc()) {
  $serviceMap[$row['bulan']] = (int)$row['total'];
}

// Pembelian
$res = $conn->query("
  SELECT DATE_FORMAT(tanggal, '%b %Y') AS bulan, SUM(total) AS total
  FROM pembelian
  GROUP BY YEAR(tanggal), MONTH(tanggal)
  ORDER BY tanggal
");
$pembelianMap = [];
while ($row = $res->fetch_assoc()) {
  $pembelianMap[$row['bulan']] = (int)$row['total'];
}

// Gabungkan semua bulan
$allMonths = array_unique(array_merge(
  array_keys($tokoMap),
  array_keys($instansiMap),
  array_keys($serviceMap),
  array_keys($pembelianMap)
));
sort($allMonths);

foreach ($allMonths as $b) {
  $bulan[]             = $b;
  $penjualanToko[]     = $tokoMap[$b] ?? 0;
  $penjualanInstansi[] = $instansiMap[$b] ?? 0;
  $penjualanService[]  = $serviceMap[$b] ?? 0;
  $pembelian[]         = $pembelianMap[$b] ?? 0;
}
?>

<h2 class="fw-bold text-gradient mb-3"><i class="fa fa-tachometer-alt"></i> Dashboard Admin</h2>


  <!-- Grid Ringkasan -->
  <div class="dashboard-grid">
    <div class="card"><h6>Penjualan Toko</h6><h4>Rp <?= number_format($penjualan_toko,0,',','.'); ?></h4><div class="bg-icon"><i class="fa fa-store"></i></div></div>
    <div class="card"><h6>Penjualan Instansi</h6><h4>Rp <?= number_format($penjualan_instansi,0,',','.'); ?></h4><div class="bg-icon"><i class="fa fa-building"></i></div></div>
    <div class="card"><h6>Penjualan Service</h6><h4>Rp <?= number_format($penjualan_service,0,',','.'); ?></h4><div class="bg-icon"><i class="fa fa-tools"></i></div></div>
    <div class="card"><h6>Piutang Toko</h6><h4>Rp <?= number_format($piutang_toko,0,',','.'); ?></h4><div class="bg-icon"><i class="fa fa-wallet"></i></div></div>
    <div class="card"><h6>Piutang Instansi</h6><h4>Rp <?= number_format($piutang_instansi,0,',','.'); ?></h4><div class="bg-icon"><i class="fa fa-university"></i></div></div>
    <div class="card"><h6>Piutang Service</h6><h4>Rp <?= number_format($piutang_service,0,',','.'); ?></h4><div class="bg-icon"><i class="fa fa-screwdriver-wrench"></i></div></div>
    <div class="card"><h6>Total Pembelian</h6><h4>Rp <?= number_format($total_pembelian,0,',','.'); ?></h4><div class="bg-icon"><i class="fa fa-shopping-cart"></i></div></div>
    <div class="card"><h6>Total Hutang</h6><h4>Rp <?= number_format($total_hutang,0,',','.'); ?></h4><div class="bg-icon"><i class="fa fa-money-bill-wave"></i></div></div>
    <div class="card"><h6>Produk Hampir Habis</h6><h4><?= $produk_hampir_habis; ?> item</h4><div class="bg-icon"><i class="fa fa-exclamation-triangle"></i></div></div>
  </div>

  <!-- Grafik -->
  <div class="card-dark">
    <h5><i class="fa fa-chart-line"></i> Grafik Penjualan & Pembelian</h5>
    <canvas id="chartPenjualan" height="100"></canvas>
  </div>

<?php
$extra_js = '
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById("chartPenjualan").getContext("2d");
new Chart(ctx, {
  type: "line",
  data: {
    labels: ' . json_encode($bulan) . ',
    datasets: [
      { label: "Penjualan Toko", data: ' . json_encode($penjualanToko) . ', borderColor: "#28a745", backgroundColor: "rgba(40,167,69,0.2)", fill:true, tension:0.3 },
      { label: "Penjualan Instansi", data: ' . json_encode($penjualanInstansi) . ', borderColor: "#17a2b8", backgroundColor: "rgba(23,162,184,0.2)", fill:true, tension:0.3 },
      { label: "Penjualan Service", data: ' . json_encode($penjualanService) . ', borderColor: "#ffc107", backgroundColor: "rgba(255,193,7,0.2)", fill:true, tension:0.3 },
      { label: "Pembelian", data: ' . json_encode($pembelian) . ', borderColor: "#dc3545", backgroundColor: "rgba(220,53,69,0.2)", fill:true, tension:0.3 }
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { labels: { color: "#fff" } } },
    scales: { x: { ticks: { color: "#fff" } }, y: { ticks: { color: "#fff" } } }
  }
});
</script>
';
include "../footer.php";
?>
