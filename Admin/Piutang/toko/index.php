<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php?role=admin");
    exit;
}
include "../../../config.php";
include "../../../header.php";

$sql = "SELECT pi.*, c.nama_customer, p.no_invoice, p.tanggal
        FROM piutang_toko pi
        JOIN penjualan_toko p ON pi.penjualan_id=p.id
        LEFT JOIN customer c ON p.customer_id=c.id
        ORDER BY p.tanggal DESC";
$result = $conn->query($sql);
?>
<div class="container-fluid">
  <h4><i class="fa fa-store"></i> Piutang Toko</h4>
  <div class="card-dark mt-3">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark text-center">
        <tr>
          <th>No</th>
          <th>No Invoice</th>
          <th>Tanggal</th>
          <th>Customer</th>
          <th>Jatuh Tempo</th>
          <th>Sisa Piutang</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if($result->num_rows > 0): $no=1; while($row=$result->fetch_assoc()): ?>
        <tr>
          <td class="text-center"><?= $no++ ?></td>
          <td><?= $row['no_invoice'] ?></td>
          <td><?= date("d-m-Y",strtotime($row['tanggal'])) ?></td>
          <td><?= $row['nama_customer'] ?></td>
          <td><?= date("d-m-Y",strtotime($row['jatuh_tempo'])) ?></td>
          <td class="text-end">Rp <?= number_format($row['sisa_piutang'],0,",",".") ?></td>
          <td class="text-center">
            <span class="badge <?= $row['status']=='Lunas'?'bg-success':'bg-warning' ?>">
              <?= $row['status'] ?>
            </span>
          </td>
          <td class="text-center">
            <a href="pembayaran.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
              <i class="fa fa-money-bill-wave"></i> Bayar
            </a>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="8" class="text-center">Belum ada piutang toko</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include "../../../footer.php"; ?>
