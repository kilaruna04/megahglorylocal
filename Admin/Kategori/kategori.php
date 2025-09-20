<?php
include "../../config.php";
include "../../header.php";

// Ambil data kategori
$result = $conn->query("SELECT * FROM kategori_produk ORDER BY id DESC");
?>

  <!-- Header Halaman -->
<div class="page-header">
  <h2><i class="fa fa-tags"></i> Daftar Kategori</h2>
  <a href="tambah_kategori.php" 
  class="btn btn-success">
    <i class="fa fa-plus"></i> Tambah Kategori
  </a>
</div>

  <!-- Card Tabel -->
  <div class="card-dark">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th width="5%">No</th>
            <th>Nama Kategori</th>
            <th>Deskripsi</th>
            <th width="20%">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): 
              $no = 1;
              while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $no++; ?></td>
              <td><?= htmlspecialchars($row['nama_kategori']); ?></td>
              <td><?= htmlspecialchars($row['deskripsi'] ?? ''); ?></td>
              <td>
                <a href="edit_kategori.php?id=<?= $row['id']; ?>" 
                   class="btn btn-warning btn-sm">
                  <i class="fa fa-edit"></i> Edit
                </a>
                <a href="hapus_kategori.php?id=<?= $row['id']; ?>" 
                   class="btn btn-danger btn-sm" 
                   onclick="return confirm('Yakin hapus kategori ini?');">
                  <i class="fa fa-trash"></i> Hapus
                </a>
              </td>
            </tr>
          <?php endwhile; else: ?>
            <tr>
              <td colspan="4" class="text-center text-muted">
                <i>Tidak ada kategori ditemukan</i>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>


<?php include "../../footer.php"; ?>
