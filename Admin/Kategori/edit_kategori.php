<?php
include "../../config.php";
include "../../header.php";

$id = (int)$_GET['id'];
$result = $conn->query("SELECT * FROM kategori_produk WHERE id=$id");
$data = $result->fetch_assoc();
?>

  <!-- Header -->
  <div class="page-header">
    <h2><i class="fa fa-edit"></i> Edit Kategori</h2>
    <a href="kategori.php" class="btn btn-outline">
      <i class="fa fa-arrow-left"></i> Kembali
    </a>
  </div>

  <!-- Form -->
  <div class="card-dark">
    <form action="update_kategori.php" method="post">
      <input type="hidden" name="id" value="<?= $data['id']; ?>">

      <label for="nama_kategori">Nama Kategori</label>
      <input type="text" name="nama_kategori" id="nama_kategori" 
             class="form-control" 
             value="<?= htmlspecialchars($data['nama_kategori']); ?>" required>

      <label for="deskripsi">Deskripsi</label>
      <textarea name="deskripsi" id="deskripsi" 
                class="form-control" rows="3"><?= htmlspecialchars($data['deskripsi']); ?></textarea>

      <button type="submit" class="btn btn-warning mt-2">
        <i class="fa fa-save"></i> Update
      </button>
    </form>
  </div>
</div>

<?php include "../../footer.php"; ?>
