<?php
include "../../config.php";
include "../../header.php";

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return "Rp " . number_format((float)$angka, 0, ",", ".");
    }
}

$sql = "SELECT h.*, p.no_invoice, s.nama_supplier, p.total 
        FROM hutang h
        LEFT JOIN pembelian p ON h.pembelian_id=p.id
        LEFT JOIN supplier s ON p.supplier_id=s.id
        ORDER BY h.jatuh_tempo ASC";
$rs = $conn->query($sql);
?>

<div class="container-fluid">
  <!-- Header -->
  <div class="page-header">
    <h2 class="fw-bold text-gradient mb-0">
      <i class="fa fa-credit-card"></i> Data Hutang
    </h2>
    <a href="laporan_hutang.php" class="btn-add">
      <i class="fa fa-file-alt"></i> Laporan Hutang
    </a>
  </div>

  <!-- Card Tabel -->
  <div class="card-dark">
    <div class="table-responsive">
      <table class="table table-modern align-middle mb-0">
        <thead>
          <tr>
            <th>No</th>
            <th>No. Invoice</th>
            <th>Supplier</th>
            <th>Jatuh Tempo</th>
            <th>Total</th>
            <th>Sisa Hutang</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($rs && $rs->num_rows): $no=1; while($row=$rs->fetch_assoc()): ?>
          <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['no_invoice']) ?></td>
            <td><?= htmlspecialchars($row['nama_supplier']) ?></td>
            <td class="text-center"><?= htmlspecialchars($row['jatuh_tempo']) ?></td>
            <td class="text-end"><?= formatRupiah($row['total']) ?></td>
            <td class="text-end"><?= formatRupiah($row['sisa_hutang']) ?></td>
            <td class="text-center">
              <?php if ($row['status']=="Lunas"): ?>
                <span class="badge badge-success">Lunas</span>
              <?php else: ?>
                <span class="badge badge-danger">Belum Lunas</span>
              <?php endif; ?>
            </td>
            <td class="td-aksi">
              <a href="edit_hutang.php?id=<?= $row['id'] ?>" class="btn-aksi btn-aksi-warning">
                <i class="fa fa-edit"></i> Edit
              </a>
              <a href="bayar_hutang.php?id=<?= $row['id'] ?>" class="btn-aksi btn-aksi-success">
                <i class="fa fa-money-bill"></i> Bayar
              </a>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="8" class="text-center">Belum ada data hutang.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../../footer.php"; ?>
