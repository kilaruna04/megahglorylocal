<?php
include "../../config.php";
include "../../header.php";

// Helper aman
function e($str) {
  return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Ambil semua supplier
$result = $conn->query("SELECT * FROM supplier ORDER BY nama_supplier ASC");
?>

<!-- Page Header -->
<div class="page-header">
  <h2><i class="fa fa-truck"></i> Daftar Supplier</h2>
  <form class="d-flex" onsubmit="return false;">
    <input type="text" id="searchSupplier" class="form-control" placeholder="Cari supplier..." style="width:200px;">
    <a href="tambah_supplier.php" class="btn-add"><i class="fa fa-plus"></i> Tambah</a>
  </form>
</div>

<!-- Alert -->
<?php if (!empty($_GET['msg'])): ?>
  <?php if ($_GET['msg'] == "deleted"): ?>
    <div class="alert alert-success">✅ Supplier berhasil dihapus.</div>
  <?php elseif ($_GET['msg'] == "used"): ?>
    <div class="alert alert-warning">⚠️ Supplier tidak bisa dihapus karena sudah dipakai di pembelian.</div>
  <?php elseif ($_GET['msg'] == "saved"): ?>
    <div class="alert alert-success">✅ Supplier berhasil ditambahkan.</div>
  <?php elseif ($_GET['msg'] == "updated"): ?>
    <div class="alert alert-success">✅ Supplier berhasil diperbarui.</div>
  <?php endif; ?>
<?php endif; ?>

<!-- Card Tabel -->
<div class="table-card">
  <div class="table-responsive">
    <table class="table table-modern align-middle mb-0" id="supplierTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Supplier</th>
          <th>Alamat</th>
          <th>No. Telp</th>
          <th>Email</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): $no=1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $no++; ?></td>
              <td class="nama"><?= e($row['nama_supplier']); ?></td>
              <td><?= e($row['alamat']); ?></td>
              <td><?= e($row['no_telp']); ?></td>
              <td><?= e($row['email']); ?></td>
              <td class="td-aksi">
                <a href="edit_supplier.php?id=<?= (int)$row['id']; ?>" class="btn-aksi btn-aksi-warning">
                  <i class="fa fa-edit"></i> Edit
                </a>
                <a href="hapus_supplier.php?id=<?= (int)$row['id']; ?>" 
                   onclick="return confirm('Yakin ingin menghapus supplier ini?')"
                   class="btn-aksi btn-aksi-danger">
                  <i class="fa fa-trash"></i> Hapus
                </a>
                <a href="history_supplier.php?id=<?= (int)$row['id']; ?>" class="btn-aksi btn-aksi-info">
                  <i class="fa fa-history"></i> History
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center">Belum ada data supplier.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include "../../footer.php"; ?>

<!-- Live Search -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById("searchSupplier");
  const rows = document.querySelectorAll("#supplierTable tbody tr");

  searchInput.addEventListener("keyup", function() {
    const query = this.value.toLowerCase();
    rows.forEach(row => {
      const nama = row.querySelector(".nama").textContent.toLowerCase();
      row.style.display = nama.includes(query) ? "" : "none";
    });
  });
});
</script>
