<?php
include "../../config.php";
include "../../header.php";
?>

  <!-- Header -->
  <div class="page-header">
    <h2><i class="fa fa-plus"></i> Tambah Kategori</h2>
    <a href="kategori.php" class="btn btn-outline">
      <i class="fa fa-arrow-left"></i> Kembali
    </a>
  </div>

  <!-- Form Tambah Kategori -->
  <div class="card-dark">
    <form action="simpan_kategori.php" method="post">
      <label for="nama_kategori">Nama Kategori</label>
      <input type="text" name="nama_kategori" id="nama_kategori" 
             class="form-control" required>

      <label for="deskripsi">Deskripsi</label>
      <textarea name="deskripsi" id="deskripsi" 
                class="form-control" rows="3"></textarea>

      <button type="submit" class="btn btn-success mt-2">
        <i class="fa fa-save"></i> Simpan
      </button>
    </form>
  </div>

<?php include "../../footer.php"; ?>
