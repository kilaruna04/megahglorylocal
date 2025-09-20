<?php
include "../../config.php";
include "../../header.php";

// Helper aman
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Ambil data pembelian + join supplier
$sql = "
    SELECT p.id, p.no_invoice, p.tanggal, p.metode_bayar, 
           p.total, p.status_hutang, s.nama_supplier
    FROM pembelian p
    LEFT JOIN supplier s ON p.supplier_id = s.id
    ORDER BY p.tanggal DESC
";
$result = $conn->query($sql);
?>

<!-- Header + Search -->
<div class="page-header">
  <h2 class="fw-bold text-gradient mb-0">
    <i class="fa fa-shopping-cart"></i> Daftar Pembelian
  </h2>

  <form class="d-flex gap-2 align-items-center" onsubmit="return false;">
    <input type="text" id="searchInput" class="form-control" 
           placeholder="Cari invoice / supplier..." style="width:220px;">
    <input type="date" id="tglAwal" class="form-control" style="width:150px;">
    <input type="date" id="tglAkhir" class="form-control" style="width:150px;">
    <a href="tambah_pembelian.php" class="btn-add">
      <i class="fa fa-plus"></i> Tambah
    </a>
    <button type="button" id="btnExport" class="btn btn-primary btn-sm">
      <i class="fa fa-file-pdf"></i> Export PDF
    </button>
  </form>
</div>

<!-- Alert -->
<?php if (!empty($_GET['msg'])): ?>
  <?php if ($_GET['msg'] == "deleted"): ?>
    <div class="alert alert-success">✅ Pembelian berhasil dihapus.</div>
  <?php elseif ($_GET['msg'] == "saved"): ?>
    <div class="alert alert-success">✅ Pembelian berhasil ditambahkan.</div>
  <?php elseif ($_GET['msg'] == "updated"): ?>
    <div class="alert alert-success">✅ Pembelian berhasil diperbarui.</div>
  <?php elseif ($_GET['msg'] == "error"): ?>
    <div class="alert alert-danger">❌ Terjadi kesalahan saat memproses data.</div>
  <?php endif; ?>
<?php endif; ?>

<!-- Card Tabel -->
<div class="table-card">
  <div class="table-responsive">
    <table class="table table-modern align-middle mb-0" id="pembelianTable">
      <thead>
        <tr>
          <th>No</th>
          <th>No Invoice</th>
          <th>Tanggal</th>
          <th>Supplier</th>
          <th>Metode Bayar</th>
          <th>Total</th>
          <th>Status Hutang</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
  <?php if ($result && $result->num_rows > 0): $no=1; ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td class="text-center"><?= $no++; ?></td>
        <td class="text-center invoice"><?= e($row['no_invoice']); ?></td>
        <td class="text-center"><?= e($row['tanggal']); ?></td>
        <td class="text-center supplier"><?= e($row['nama_supplier']); ?></td>
        <td class="text-center">
          <?php if ($row['metode_bayar'] === 'Cash'): ?>
            <span class="badge badge-success">Cash</span>
          <?php else: ?>
            <span class="badge badge-warning">Kredit</span>
          <?php endif; ?>
        </td>
        <td class="text-right">Rp <?= number_format($row['total'],0,',','.'); ?></td>
        <td class="text-center">
          <?php if ($row['status_hutang'] === 'Lunas'): ?>
            <span class="badge badge-success">Lunas</span>
          <?php else: ?>
            <span class="badge badge-danger">Belum Lunas</span>
          <?php endif; ?>
        </td>
        <td class="text-center td-aksi">
          <a href="detail_pembelian.php?id=<?= (int)$row['id']; ?>" class="btn-aksi btn-aksi-success">
            <i class="fa fa-eye"></i> Detail
          </a>
          <a href="edit_pembelian.php?id=<?= (int)$row['id']; ?>" class="btn-aksi btn-aksi-warning">
            <i class="fa fa-edit"></i> Edit
          </a>
          <a href="hapus_pembelian.php?id=<?= (int)$row['id']; ?>" 
             onclick="return confirm('Yakin ingin menghapus pembelian ini?')"
             class="btn-aksi btn-aksi-danger">
            <i class="fa fa-trash"></i> Hapus
          </a>
        </td>
      </tr>
    <?php endwhile; ?>
  <?php else: ?>
    <tr>
      <td colspan="8" class="text-center">Belum ada data pembelian.</td>
    </tr>
  <?php endif; ?>
</tbody>
    </table>
  </div>
</div>

<?php include "../../footer.php"; ?>

<!-- Live Search + Export JS -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById("searchInput");
  const rows = document.querySelectorAll("#pembelianTable tbody tr");
  const btnExport = document.getElementById("btnExport");
  const tglAwal = document.getElementById("tglAwal");
  const tglAkhir = document.getElementById("tglAkhir");

  // Live Search (Invoice + Supplier)
  searchInput.addEventListener("keyup", function() {
    const query = this.value.toLowerCase();
    rows.forEach(row => {
      const invoice = row.querySelector(".invoice").textContent.toLowerCase();
      const supplier = row.querySelector(".supplier").textContent.toLowerCase();
      row.style.display = (invoice.includes(query) || supplier.includes(query)) ? "" : "none";
    });
  });

  // Export PDF
  btnExport.addEventListener("click", function() {
    const query = encodeURIComponent(searchInput.value);
    const awal = encodeURIComponent(tglAwal.value);
    const akhir = encodeURIComponent(tglAkhir.value);
    window.open("export_pembelian.php?q=" + query + "&awal=" + awal + "&akhir=" + akhir, "_blank");
  });
});
</script>
