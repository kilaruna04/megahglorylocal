<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php?role=admin");
    exit;
}

include "../../../config.php";
include "../../../header.php";

function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Ambil data penjualan instansi
$sql = "SELECT pi.*, c.nama_customer AS customer_nama 
        FROM penjualan_instansi pi
        JOIN customer c ON pi.customer_id = c.id
        ORDER BY pi.tanggal DESC";
$result = $conn->query($sql);
?>

<!-- Header -->
<div class="page-header">
  <h2><i class="fa fa-building"></i> Penjualan Instansi</h2>
  <a href="tambah.php" class="btn-add"><i class="fa fa-plus"></i> Tambah Penjualan</a>
</div>

<!-- Card Tabel -->
<div class="table-card">
  <div class="table-responsive">
    <table class="table table-modern align-middle mb-0">
      <thead>
        <tr>
          <th>No</th>
          <th>No Invoice</th>
          <th>Tanggal</th>
          <th>Customer</th>
          <th>Jenis</th>
          <th>Total</th>
          <th>Total Masuk</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php $no=1; while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['no_invoice']) ?></td>
              <td><?= date("d-m-Y", strtotime($row['tanggal'])) ?></td>
              <td><?= htmlspecialchars($row['customer_nama']) ?></td>
              <td class="text-center"><?= htmlspecialchars($row['jenis_penjualan']) ?></td>
              <td class="text-right"><?= rupiah($row['total']) ?></td>
              <td class="text-right"><?= rupiah($row['total_masuk']) ?></td>
              <td class="text-center">
                <?php if ($row['status'] === 'Lunas'): ?>
                  <span class="badge badge-success">Lunas</span>
                <?php else: ?>
                  <span class="badge badge-warning text-dark"><?= htmlspecialchars($row['status']) ?></span>
                <?php endif; ?>
              </td>
              <td class="td-aksi">
                <a href="detail.php?id=<?= (int)$row['id'] ?>" class="btn-aksi btn-aksi-info">
                  <i class="fa fa-eye"></i> Detail
                </a>
                <a href="edit.php?id=<?= (int)$row['id'] ?>" class="btn-aksi btn-aksi-warning">
                  <i class="fa fa-edit"></i> Edit
                </a>
                <a href="print.php?id=<?= (int)$row['id'] ?>" target="_blank" class="btn-aksi btn-aksi-success">
                  <i class="fa fa-print"></i> Cetak
                </a>
                <a href="delete.php?id=<?= (int)$row['id'] ?>" 
                   onclick="return confirm('Yakin hapus data ini?')" 
                   class="btn-aksi btn-aksi-danger">
                  <i class="fa fa-trash"></i> Hapus
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" class="text-center">Belum ada transaksi instansi</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include "../../../footer.php"; ?>
