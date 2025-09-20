<?php
if (!isset($_SESSION)) { session_start(); }
include "../../config.php";

// Helper aman
function e($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "<p class='text-danger'>❌ ID pembelian tidak valid.</p>";
    exit;
}

// Ambil data detail + SN
$sql = "
    SELECT d.id, d.qty, d.harga, d.serial_number, pr.nama_produk
    FROM pembelian_detail d
    LEFT JOIN produk pr ON d.produk_id = pr.id
    WHERE d.pembelian_id = {$id}
    ORDER BY d.id ASC
";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "<p class='text-muted'>Tidak ada data serial number untuk pembelian ini.</p>";
    exit;
}
?>

<div class="table-responsive">
  <table class="table table-sm table-bordered align-middle">
    <thead class="table-dark">
      <tr>
        <th style="width:50px;">No</th>
        <th>Produk</th>
        <th>Qty</th>
        <th>Harga</th>
        <th>Serial Number</th>
      </tr>
    </thead>
    <tbody>
      <?php $no=1; while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td class="text-center"><?= $no++; ?></td>
          <td><?= e($row['nama_produk']); ?></td>
          <td class="text-center"><?= e($row['qty']); ?></td>
          <td class="text-end">Rp <?= number_format($row['harga'],0,",","."); ?></td>
          <td>
            <?php if (!empty($row['serial_number'])): ?>
              <div class="small text-monospace">
                <?= nl2br(e($row['serial_number'])); ?>
              </div>
            <?php else: ?>
              <span class="text-muted">- Belum diisi -</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
