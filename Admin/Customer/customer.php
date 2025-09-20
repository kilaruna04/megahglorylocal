<?php
include "../../config.php";
include "../../header.php";

// Helper aman
function e($str) {
  return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Ambil semua customer
$result = $conn->query("SELECT * FROM customer ORDER BY nama_customer ASC");
?>

<!-- Header + Search -->
<div class="page-header">
  <h2 class="fw-bold text-gradient mb-0">
    <i class="fa fa-users"></i> Daftar Customer
  </h2>

  <form class="d-flex" onsubmit="return false;">
    <input type="text" id="searchCustomer" class="form-control" placeholder="Cari customer..." style="width:200px;">
    <a href="tambah_customer.php" class="btn-add"><i class="fa fa-plus"></i> Tambah</a>
  </form>
</div>

<!-- Alert -->
<?php if (!empty($_GET['msg'])): ?>
  <?php if ($_GET['msg'] == "deleted"): ?>
    <div class="alert alert-success">✅ Customer berhasil dihapus.</div>
  <?php elseif ($_GET['msg'] == "used"): ?>
    <div class="alert alert-warning">⚠️ Customer tidak bisa dihapus karena sudah dipakai di transaksi.</div>
  <?php elseif ($_GET['msg'] == "saved"): ?>
    <div class="alert alert-success">✅ Customer berhasil ditambahkan.</div>
  <?php elseif ($_GET['msg'] == "updated"): ?>
    <div class="alert alert-success">✅ Customer berhasil diperbarui.</div>
  <?php endif; ?>
<?php endif; ?>

<!-- Card Tabel -->
<div class="table-card">
  <div class="table-responsive">
    <table class="table table-modern align-middle mb-0" id="customerTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Customer</th>
          <th>Jenis</th>
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
              <td class="nama"><?= e($row['nama_customer']); ?></td>
              <td>
  <?php if ($row['jenis'] === 'Toko'): ?>
    <span class="badge badge-success">Toko</span>
  <?php elseif ($row['jenis'] === 'Instansi'): ?>
    <span class="badge badge-primary">Instansi</span>
  <?php else: ?>
    <span class="badge badge-secondary"><?= e($row['jenis']); ?></span>
  <?php endif; ?>
</td>
              <td><?= e($row['alamat']); ?></td>
              <td><?= e($row['no_telp']); ?></td>
              <td><?= e($row['email']); ?></td>
              <td class="td-aksi">
                <a href="edit_customer.php?id=<?= (int)$row['id']; ?>" class="btn-aksi btn-aksi-warning">
                  <i class="fa fa-edit"></i> Edit
                </a>
                <a href="hapus_customer.php?id=<?= (int)$row['id']; ?>" 
                   onclick="return confirm('Yakin ingin menghapus customer ini?')"
                   class="btn-aksi btn-aksi-danger">
                  <i class="fa fa-trash"></i> Hapus
                </a>
             <a href="history_customer.php?id=<?= (int)$row['id']; ?>" class="btn-aksi btn-aksi-info">
              <i class="fa fa-history"></i> History
            </a>

              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center">Belum ada data customer.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include "../../footer.php"; ?>

<!-- Live Search JS -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById("searchCustomer");
  const rows = document.querySelectorAll("#customerTable tbody tr");

  searchInput.addEventListener("keyup", function() {
    const query = this.value.toLowerCase();
    rows.forEach(row => {
      const nama = row.querySelector(".nama").textContent.toLowerCase();
      row.style.display = nama.includes(query) ? "" : "none";
    });
  });
});
</script>
