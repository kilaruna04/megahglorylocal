<?php
include "../../config.php";
include "../../header.php";

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return "Rp " . number_format((float)$angka, 0, ",", ".");
    }
}

$id = intval($_GET['id'] ?? 0);
$hutang = $conn->query("
    SELECT h.*, p.no_invoice, s.nama_supplier, p.total, p.tanggal 
    FROM hutang h
    LEFT JOIN pembelian p ON h.pembelian_id = p.id
    LEFT JOIN supplier s ON p.supplier_id = s.id
    WHERE h.id = $id
")->fetch_assoc();

if (!$hutang) {
    echo "<div class='alert alert-danger'>Data hutang tidak ditemukan.</div>";
    include "../../footer.php";
    exit;
}
?>

<div class="container-fluid">
  <!-- Header -->
  <div class="page-header d-flex justify-content-between align-items-center">
    <h2 class="fw-bold text-gradient mb-0">
      <i class="fa fa-eye"></i> Detail Hutang
    </h2>
    <a href="laporan_hutang.php" class="btn-add">
      <i class="fa fa-arrow-left"></i> Kembali
    </a>
  </div>

  <!-- Info Hutang -->
  <div class="card-dark p-3 mb-3">
    <p><strong>No Invoice:</strong> <?= htmlspecialchars($hutang['no_invoice']) ?></p>
    <p><strong>Supplier:</strong> <?= htmlspecialchars($hutang['nama_supplier']) ?></p>
    <p><strong>Tanggal Pembelian:</strong> <?= date("d-m-Y", strtotime($hutang['tanggal'])) ?></p>
    <p><strong>Total Hutang:</strong> <?= formatRupiah($hutang['total']) ?></p>
    <p><strong>Sisa Hutang:</strong> <?= formatRupiah($hutang['sisa_hutang']) ?></p>
    <p><strong>Jatuh Tempo:</strong> <?= date("d-m-Y", strtotime($hutang['jatuh_tempo'])) ?></p>
    <p><strong>Status:</strong> 
      <?= $hutang['status']=="Lunas" 
          ? '<span class="badge bg-success">Lunas</span>' 
          : '<span class="badge bg-danger">Belum Lunas</span>' ?>
    </p>
  </div>

  <!-- Riwayat Pembayaran -->
  <?php
  $riwayat = $conn->query("
    SELECT * FROM hutang_pembayaran 
    WHERE hutang_id = $id 
    ORDER BY tanggal DESC
  ");
  ?>
  <div class="card-dark p-3">
    <h5><i class="fa fa-history"></i> Riwayat Pembayaran</h5>
    <div class="table-responsive mt-2">
      <table class="table table-modern align-middle mb-0">
        <thead>
          <tr>
            <th class="text-center">No</th>
            <th class="text-center">Tanggal</th>
            <th class="text-right">Jumlah</th>
            <th class="text-center">Metode</th>
            <th class="text-center">Bank</th>
            <th class="text-center">Bukti</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($riwayat && $riwayat->num_rows > 0): $no=1; ?>
            <?php while($row = $riwayat->fetch_assoc()): ?>
              <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-center"><?= date("d-m-Y", strtotime($row['tanggal'])) ?></td>
                <td class="text-right"><?= formatRupiah($row['jumlah']) ?></td>
                <td class="text-center"><?= htmlspecialchars($row['metode'] ?? '-') ?></td>
                <td class="text-center"><?= htmlspecialchars($row['nama_bank'] ?? '-') ?></td>
                <td class="text-center">
                  <?php if(!empty($row['bukti_bayar'])): ?>
                    <a href="../../uploads/bukti_hutang/<?= htmlspecialchars($row['bukti_bayar']) ?>" target="_blank" class="btn-aksi btn-aksi-info">
                      <i class="fa fa-file"></i> Lihat
                    </a>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center">Belum ada pembayaran.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../../footer.php"; ?>
