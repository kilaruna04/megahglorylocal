<?php
include "../../config.php";
include "../../header.php";

if (!isset($_GET['id'])) {
    header("Location: supplier.php");
    exit;
}

$id = intval($_GET['id']);
$supplier = $conn->query("SELECT * FROM supplier WHERE id=$id")->fetch_assoc();
if (!$supplier) {
    header("Location: supplier.php");
    exit;
}

$result = $conn->query("SELECT * FROM pembelian WHERE supplier_id=$id ORDER BY tanggal DESC");
?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold text-primary">
      <i class="fa fa-book"></i> History Supplier: <?= htmlspecialchars($supplier['nama_supplier']); ?>
    </h2>
    <a href="supplier.php" class="btn btn-secondary">
      <i class="fa fa-arrow-left"></i> Kembali
    </a>
  </div>

  <div class="card-dark">
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>No Invoice</th>
            <th>Tanggal</th>
            <th>Metode Bayar</th>
            <th>Total</th>
            <th>Status Hutang</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['no_invoice']); ?></td>
                <td><?= htmlspecialchars($row['tanggal']); ?></td>
                <td><?= htmlspecialchars($row['metode_bayar']); ?></td>
                <td>Rp <?= number_format($row['total'],0,",","."); ?></td>
                <td>
                  <?php if ($row['status_hutang'] == "Lunas"): ?>
                    <span class="badge bg-success">Lunas</span>
                  <?php elseif ($row['status_hutang'] == "Belum Lunas"): ?>
                    <span class="badge bg-danger">Belum Lunas</span>
                  <?php else: ?>
                    <span class="badge bg-secondary"><?= htmlspecialchars($row['status_hutang']); ?></span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center">Belum ada transaksi</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../../footer.php"; ?>
