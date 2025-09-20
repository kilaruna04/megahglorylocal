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

// =====================
// Query Laporan Piutang
// =====================
$sql = "
SELECT 
    p.id,
    p.no_invoice,
    p.tanggal,
    c.nama_customer,
    d.jenis_item AS jenis_piutang,
    SUM(d.subtotal) AS total_jenis,
    pi.jatuh_tempo,
    ROUND(
      (SUM(d.subtotal) / SUM(SUM(d.subtotal)) OVER (PARTITION BY p.id)) * pi.sisa_piutang,
      0
    ) AS sisa_per_jenis,
    pi.status
FROM penjualan_instansi p
JOIN customer c ON p.customer_id = c.id
JOIN penjualan_instansi_detail d ON p.id = d.penjualan_id
JOIN piutang_instansi pi ON pi.penjualan_id = p.id
WHERE pi.sisa_piutang > 0
GROUP BY p.id, d.jenis_item, p.no_invoice, p.tanggal, c.nama_customer, pi.jatuh_tempo, pi.sisa_piutang, pi.status
ORDER BY p.tanggal DESC, p.no_invoice;
";

$result = $conn->query($sql);
?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="fa fa-money-bill-wave"></i> Laporan Piutang (REAL & SPJ)</h4>
  </div>

  <div class="card-dark">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-primary text-center">
        <tr>
          <th>No</th>
          <th>No Invoice</th>
          <th>Tanggal</th>
          <th>Customer</th>
          <th>Jenis</th>
          <th>Jatuh Tempo</th>
          <th>Total Jenis</th>
          <th>Sisa Piutang</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php $no=1; while($row = $result->fetch_assoc()): ?>
            <tr>
              <td class="text-center"><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['no_invoice']) ?></td>
              <td><?= date("d-m-Y", strtotime($row['tanggal'])) ?></td>
              <td><?= htmlspecialchars($row['nama_customer']) ?></td>
              <td class="text-center">
                <?php if ($row['jenis_piutang']=='SPJ'): ?>
                  <span class="badge bg-warning text-dark">SPJ</span>
                <?php else: ?>
                  <span class="badge bg-success">REAL</span>
                <?php endif; ?>
              </td>
              <td class="text-center"><?= date("d-m-Y", strtotime($row['jatuh_tempo'])) ?></td>
              <td class="text-end"><?= rupiah($row['total_jenis']) ?></td>
              <td class="text-end"><?= rupiah($row['sisa_per_jenis']) ?></td>
              <td class="text-center">
                <span class="badge <?= $row['status']=='Lunas'?'bg-success':'bg-danger' ?>">
                  <?= $row['status'] ?>
                </span>
              </td>
              <td class="text-center">
                <?php if ($row['status'] == 'Belum Lunas'): ?>
                  <a href="bayar.php?id=<?= $row['id'] ?>&jenis=<?= $row['jenis_piutang'] ?>" 
                     class="btn btn-sm btn-primary">
                    <i class="fa fa-credit-card"></i> Bayar
                  </a>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="10" class="text-center">Belum ada data piutang</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include "../../../footer.php"; ?>
