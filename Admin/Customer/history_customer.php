<?php
include "../../config.php";
include "../../header.php";

if (!isset($_GET['id'])) {
    header("Location: customer.php");
    exit;
}
$id = intval($_GET['id']);
$customer = $conn->query("SELECT * FROM customer WHERE id=$id")->fetch_assoc();
if (!$customer) {
    header("Location: customer.php");
    exit;
}

$result = $conn->query("SELECT * FROM penjualan WHERE customer_id=$id ORDER BY tanggal DESC");
?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="fa fa-history"></i> History Customer: <?= htmlspecialchars($customer['nama_customer']); ?></h2>
    <a href="customer.php" class="btn btn-secondary">
      <i class="fa fa-arrow-left"></i> Kembali
    </a>
  </div>

  <div class="card-dark">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-primary text-center">
          <tr>
            <th>No Invoice</th>
            <th>Tanggal</th>
            <th>Jenis</th>
            <th>Metode Bayar</th>
            <th>Total</th>
            <th>Status Piutang</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr class="text-center">
                <td><?= htmlspecialchars($row['no_invoice']); ?></td>
                <td><?= htmlspecialchars(date("d-m-Y", strtotime($row['tanggal']))); ?></td>
                <td><?= htmlspecialchars($row['jenis']); ?></td>
                <td><?= htmlspecialchars($row['metode_bayar']); ?></td>
                <td class="fw-bold text-success">Rp <?= number_format($row['total'],0,",","."); ?></td>
                <td>
                  <?php if ($row['status_piutang'] == "Lunas"): ?>
                    <span class="badge bg-success">Lunas</span>
                  <?php elseif ($row['status_piutang'] == "Belum Lunas"): ?>
                    <span class="badge bg-danger">Belum Lunas</span>
                  <?php else: ?>
                    <span class="badge bg-warning text-dark"><?= htmlspecialchars($row['status_piutang']); ?></span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center text-muted">Belum ada transaksi</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../../footer.php"; ?>
