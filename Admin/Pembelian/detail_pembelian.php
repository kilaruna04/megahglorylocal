<?php
include "../../config.php";
include "../../header.php";

if (!isset($_GET['id'])) {
    header("Location: pembelian.php");
    exit;
}

$id = intval($_GET['id']);
$pembelian = $conn->query("SELECT p.*, s.nama_supplier 
                           FROM pembelian p
                           LEFT JOIN supplier s ON p.supplier_id=s.id
                           WHERE p.id=$id")->fetch_assoc();

if (!$pembelian) {
    header("Location: pembelian.php");
    exit;
}

// Ambil detail barang
$detail = $conn->query("SELECT d.*, pr.nama_produk 
                        FROM pembelian_detail d
                        LEFT JOIN produk pr ON d.produk_id=pr.id
                        WHERE d.pembelian_id=$id");

// Ambil data hutang jika kredit
$jt = $conn->query("SELECT jatuh_tempo, status, sisa_hutang 
                    FROM hutang 
                    WHERE pembelian_id = {$id}")->fetch_assoc();
?>

<div class="container-fluid">

  <!-- Header -->
  <div class="page-header">
    <h2><i class="fa fa-file-invoice"></i> Detail Pembelian</h2>
    <a href="pembelian.php" class="btn btn-outline">
      <i class="fa fa-arrow-left"></i> Kembali
    </a>
  </div>

  <!-- Informasi Pembelian -->
  <div class="card-dark mb-4">
    <p><strong>No. Invoice:</strong> <?= htmlspecialchars($pembelian['no_invoice']); ?></p>
    <p><strong>Tanggal:</strong> <?= htmlspecialchars($pembelian['tanggal']); ?></p>
    <p><strong>Supplier:</strong> <?= htmlspecialchars($pembelian['nama_supplier']); ?></p>
    <p><strong>Metode Bayar:</strong> <?= htmlspecialchars($pembelian['metode_bayar']); ?></p>
    <p><strong>Total:</strong> <span class="text-success">Rp <?= number_format($pembelian['total'],0,",","."); ?></span></p>

    <?php if (!empty($pembelian['keterangan'])): ?>
      <p><strong>Keterangan:</strong> <?= nl2br(htmlspecialchars($pembelian['keterangan'])); ?></p>
    <?php endif; ?>

    <?php if (!empty($pembelian['nota_file'])): ?>
      <p><strong>Nota Pembelian:</strong>
        <a href="../../uploads/nota_pembelian/<?= htmlspecialchars($pembelian['nota_file']); ?>" target="_blank" class="btn-aksi btn-aksi-info">
          <i class="fa fa-file"></i> Lihat Nota
        </a>
      </p>
    <?php endif; ?>
  </div>

  <!-- Informasi Hutang (Jika Kredit) -->
  <?php if (isset($pembelian['metode_bayar']) && $pembelian['metode_bayar'] === 'Kredit' && $jt): ?>
    <div class="alert alert-info fw-bold">
      <i class="fa fa-clock"></i> 
      Jatuh Tempo: <?= htmlspecialchars($jt['jatuh_tempo']); ?> &nbsp; | &nbsp;
      <i class="fa fa-exclamation-circle"></i> Status Hutang: 
      <?php if ($jt['status'] === "Lunas"): ?>
        <span class="badge badge-success">Lunas</span>
      <?php else: ?>
        <span class="badge badge-danger"><?= htmlspecialchars($jt['status']); ?></span>
      <?php endif; ?>
      &nbsp; | &nbsp;
      <i class="fa fa-wallet"></i> Sisa: Rp <?= number_format($jt['sisa_hutang'],0,",","."); ?>
    </div>
  <?php endif; ?>

  <!-- Detail Produk -->
  <div class="card-dark">
    <h5><i class="fa fa-box"></i> Produk Dibeli</h5>
    <div class="table-responsive mt-3">
      <table class="table-modern align-middle">
        <thead>
          <tr>
            <th>No</th>
            <th>Produk</th>
            <th>Qty</th>
            <th>Harga Beli</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($detail && $detail->num_rows > 0): $no=1; while($row=$detail->fetch_assoc()): ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama_produk']); ?></td>
            <td><?= htmlspecialchars($row['qty']); ?></td>
            <td>Rp <?= number_format($row['harga'],0,",","."); ?></td>
            <td>Rp <?= number_format($row['subtotal'],0,",","."); ?></td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="5" class="text-center">Belum ada detail produk</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../../footer.php"; ?>
