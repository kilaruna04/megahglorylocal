<?php
include "../../../config.php";
include "../../header.php";

$where = "1=1";
if ($_SERVER['REQUEST_METHOD']=="POST") {
    $tgl_awal = $_POST['tgl_awal'];
    $tgl_akhir = $_POST['tgl_akhir'];
    if ($tgl_awal && $tgl_akhir) {
        $where = "s.created_at BETWEEN '$tgl_awal' AND '$tgl_akhir'";
    }
}

$sql = "SELECT s.*, p.no_invoice, c.nama_customer, u.nama_lengkap AS teknisi
        FROM service s
        JOIN penjualan p ON s.penjualan_id=p.id
        JOIN customer c ON p.customer_id=c.id
        LEFT JOIN users u ON s.teknisi_id=u.id
        WHERE $where
        ORDER BY s.created_at DESC";
$result = $conn->query($sql);
?>
<h2 class="fw-bold text-primary mb-4">📑 Laporan Service</h2>

<form method="post" class="row g-3 mb-3">
  <div class="col-md-3">
    <input type="date" name="tgl_awal" class="form-control" value="<?=isset($tgl_awal)?$tgl_awal:'';?>">
  </div>
  <div class="col-md-3">
    <input type="date" name="tgl_akhir" class="form-control" value="<?=isset($tgl_akhir)?$tgl_akhir:'';?>">
  </div>
  <div class="col-md-3">
    <button type="submit" class="btn btn-primary">🔍 Filter</button>
  </div>
</form>

<div class="mb-3">
  <a href="export_service_excel.php?tgl_awal=<?=$tgl_awal??'';?>&tgl_akhir=<?=$tgl_akhir??'';?>" class="btn btn-success">📊 Export Excel</a>
  <a href="export_service_pdf.php?tgl_awal=<?=$tgl_awal??'';?>&tgl_akhir=<?=$tgl_akhir??'';?>" class="btn btn-danger">📄 Export PDF</a>
</div>

<div class="table-responsive">
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
      <tr>
        <th>No Service</th>
        <th>No Invoice</th>
        <th>Customer</th>
        <th>Teknisi</th>
        <th>Status</th>
        <th>Catatan Teknisi</th>
        <th>Tanggal</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows>0): while($row=$result->fetch_assoc()): ?>
      <tr>
        <td><?=$row['no_service'];?></td>
        <td><?=$row['no_invoice'];?></td>
        <td><?=$row['nama_customer'];?></td>
        <td><?=$row['teknisi'];?></td>
        <td><?=$row['status_teknisi'];?></td>
        <td><?=$row['catatan_teknisi'];?></td>
        <td><?=$row['created_at'];?></td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include "../../footer.php"; ?>
