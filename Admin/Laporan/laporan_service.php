<?php
session_start();
include "../config.php";

if (!isset($_SESSION['username']) || $_SESSION['role']!='admin') {
    header("Location: ../login.php?role=admin");
    exit;
}

$tgl_awal = $_GET['tgl_awal'] ?? date("Y-m-01");
$tgl_akhir = $_GET['tgl_akhir'] ?? date("Y-m-d");

$sql = "SELECT s.*, p.no_invoice, c.nama_customer, u.nama_lengkap as teknisi_nama
        FROM service s
        JOIN penjualan p ON s.penjualan_id=p.id
        JOIN customer c ON p.customer_id=c.id
        LEFT JOIN users u ON s.teknisi_id=u.id
        WHERE DATE(s.created_at) BETWEEN '$tgl_awal' AND '$tgl_akhir'
        ORDER BY s.created_at DESC";
$services = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Service</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">
  <div class="card p-4">
    <h2 class="fw-bold text-primary text-center mb-4">🛠 Laporan Service</h2>

    <!-- Filter -->
    <form class="row g-3 mb-4" method="get">
      <div class="col-md-4">
        <label class="form-label">Tanggal Awal</label>
        <input type="date" name="tgl_awal" value="<?=$tgl_awal;?>" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">Tanggal Akhir</label>
        <input type="date" name="tgl_akhir" value="<?=$tgl_akhir;?>" class="form-control">
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">🔍 Tampilkan</button>
      </div>
    </form>

    <div class="d-flex justify-content-end mb-3 gap-2">
      <a href="export_service_excel.php?tgl_awal=<?=$tgl_awal;?>&tgl_akhir=<?=$tgl_akhir;?>" class="btn btn-success btn-sm">⬇ Export Excel</a>
      <a href="export_service_pdf.php?tgl_awal=<?=$tgl_awal;?>&tgl_akhir=<?=$tgl_akhir;?>" class="btn btn-danger btn-sm">⬇ Export PDF</a>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>No Service</th>
            <th>No Invoice</th>
            <th>Customer</th>
            <th>Teknisi</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Total Sparepart</th>
            <th>Catatan</th>
          </tr>
        </thead>
        <tbody>
          <?php if($services->num_rows>0): $no=1; while($row=$services->fetch_assoc()): ?>
          <?php
            // Hitung total sparepart
            $q = $conn->query("SELECT SUM(subtotal) as total FROM service_sparepart WHERE service_id=".$row['id']);
            $total_sparepart = $q->fetch_assoc()['total'] ?? 0;
          ?>
          <tr>
            <td><?=$no++;?></td>
            <td><?=$row['no_service'];?></td>
            <td><?=$row['no_invoice'];?></td>
            <td><?=$row['nama_customer'];?></td>
            <td><?=$row['teknisi_nama'] ?? "-";?></td>
            <td><?=$row['status_admin'];?></td>
            <td><?=date("d-m-Y",strtotime($row['created_at']));?></td>
            <td>Rp <?=number_format($total_sparepart,0,",",".");?></td>
            <td><?=$row['catatan_teknisi'];?></td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="9" class="text-center">Tidak ada data service</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
