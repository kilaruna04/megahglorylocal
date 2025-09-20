<?php
session_start();
include "../config.php";

if (!isset($_SESSION['username']) || $_SESSION['role']!='admin') {
    header("Location: ../login.php?role=admin");
    exit;
}

// --- Hutang (Belum Lunas) ---
$qHutang = "SELECT h.id, p.no_invoice, s.nama_supplier, h.sisa_hutang, h.jatuh_tempo, h.status
            FROM hutang h
            JOIN pembelian p ON h.pembelian_id=p.id
            JOIN supplier s ON p.supplier_id=s.id
            WHERE h.status='Belum Lunas'";
$hutang = $conn->query($qHutang);
$totalHutang = 0; while($r=$hutang->fetch_assoc()){ $dataHutang[]=$r; $totalHutang += $r['sisa_hutang']; }

// --- Piutang (Belum Lunas) ---
$qPiutang = "SELECT pi.id, pj.no_invoice, c.nama_customer, pi.sisa_piutang, pi.jatuh_tempo, pi.status
             FROM piutang pi
             JOIN penjualan pj ON pi.penjualan_id=pj.id
             JOIN customer c ON pj.customer_id=c.id
             WHERE pi.status='Belum Lunas'";
$piutang = $conn->query($qPiutang);
$totalPiutang = 0; while($r=$piutang->fetch_assoc()){ $dataPiutang[]=$r; $totalPiutang += $r['sisa_piutang']; }

// --- Service (Ringkasan) ---
$qService = "SELECT s.no_service, p.no_invoice, c.nama_customer, s.status_admin, s.status_teknisi, s.updated_at
             FROM service s
             JOIN penjualan p ON s.penjualan_id=p.id
             JOIN customer c ON p.customer_id=c.id
             ORDER BY s.updated_at DESC LIMIT 10";
$service = $conn->query($qService);
$dataService=[];
while($r=$service->fetch_assoc()){ $dataService[]=$r; }

// Hitung service per status
$res = $conn->query("SELECT status_admin, COUNT(*) as total FROM service GROUP BY status_admin");
$serviceStats=[];
while($r=$res->fetch_assoc()){ $serviceStats[$r['status_admin']]=$r['total']; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Gabungan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
    .card { border-radius: 15px; box-shadow: 0 6px 12px rgba(0,0,0,0.15); margin-bottom:20px; }
    .table thead { background: #1E90FF; color: white; }
    canvas { max-height: 300px; }
  </style>
</head>
<body>
<div class="container my-4">
  <h2 class="fw-bold text-primary mb-4 text-center">📊 Laporan Gabungan</h2>
  <div class="d-flex justify-content-end mb-3 gap-2">
    <a href="export_laporan_gabungan_excel.php" class="btn btn-success btn-sm">⬇ Export Excel</a>
    <a href="export_laporan_gabungan_pdf.php" class="btn btn-danger btn-sm">⬇ Export PDF</a>
  </div>

  <!-- Grafik -->
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card p-3">
        <h5 class="fw-bold text-center">💰 Hutang vs Piutang</h5>
        <canvas id="pieChart"></canvas>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-3">
        <h5 class="fw-bold text-center">🔧 Service per Status (Admin)</h5>
        <canvas id="barChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Hutang -->
  <div class="card p-4">
    <h4 class="fw-bold text-danger">💰 Hutang (Belum Lunas)</h4>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr><th>No Invoice</th><th>Supplier</th><th>Sisa Hutang</th><th>Jatuh Tempo</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php if(!empty($dataHutang)): foreach($dataHutang as $row): ?>
          <tr>
            <td><?=$row['no_invoice'];?></td>
            <td><?=$row['nama_supplier'];?></td>
            <td>Rp <?=number_format($row['sisa_hutang'],0,",",".");?></td>
            <td><?=$row['jatuh_tempo'];?></td>
            <td><span class="badge bg-danger"><?=$row['status'];?></span></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="5" class="text-center">Tidak ada hutang</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Piutang -->
  <div class="card p-4">
    <h4 class="fw-bold text-warning">📌 Piutang (Belum Lunas)</h4>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr><th>No Invoice</th><th>Customer</th><th>Sisa Piutang</th><th>Jatuh Tempo</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php if(!empty($dataPiutang)): foreach($dataPiutang as $row): ?>
          <tr>
            <td><?=$row['no_invoice'];?></td>
            <td><?=$row['nama_customer'];?></td>
            <td>Rp <?=number_format($row['sisa_piutang'],0,",",".");?></td>
            <td><?=$row['jatuh_tempo'];?></td>
            <td><span class="badge bg-danger"><?=$row['status'];?></span></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="5" class="text-center">Tidak ada piutang</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Service -->
  <div class="card p-4">
    <h4 class="fw-bold text-info">🔧 Service (10 Terbaru)</h4>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr><th>No Service</th><th>No Invoice</th><th>Customer</th><th>Status Admin</th><th>Status Teknisi</th><th>Update Terakhir</th></tr>
        </thead>
        <tbody>
        <?php if(!empty($dataService)): foreach($dataService as $row): ?>
          <tr>
            <td><?=$row['no_service'];?></td>
            <td><?=$row['no_invoice'];?></td>
            <td><?=$row['nama_customer'];?></td>
            <td><?=$row['status_admin'];?></td>
            <td><?=$row['status_teknisi'];?></td>
            <td><?=$row['updated_at'];?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="6" class="text-center">Belum ada data service</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Pie Chart Hutang vs Piutang
const pieCtx = document.getElementById('pieChart');
new Chart(pieCtx, {
  type: 'pie',
  data: {
    labels: ['Hutang','Piutang'],
    datasets: [{
      data: [<?=$totalHutang;?>, <?=$totalPiutang;?>],
      backgroundColor: ['#dc3545','#ffc107']
    }]
  }
});

// Bar Chart Service per Status
const barCtx = document.getElementById('barChart');
new Chart(barCtx, {
  type: 'bar',
  data: {
    labels: <?=json_encode(array_keys($serviceStats));?>,
    datasets: [{
      label: 'Jumlah Service',
      data: <?=json_encode(array_values($serviceStats));?>,
      backgroundColor: '#0d6efd'
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});
</script>
</body>
</html>
