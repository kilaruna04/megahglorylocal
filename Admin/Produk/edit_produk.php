<?php
include "../../config.php";
session_start();

// --- Ambil ID Produk ---
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$q = $conn->query("SELECT * FROM produk WHERE id=$id");
if(!$q || $q->num_rows == 0){
  die("<div class='alert alert-danger'>Produk tidak ditemukan.</div>");
}
$data = $q->fetch_assoc();

include "../../header.php";
?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-gradient mb-0">
      <i class="fa fa-edit"></i> Edit Produk
    </h2>
    <a href="produk.php" class="btn-add">
      <i class="fa fa-arrow-left"></i> Kembali
    </a>
  </div>

  <div class="card-dark p-3">
    <form action="update_produk.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $data['id'] ?>">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Nama Produk</label>
          <input type="text" name="nama_produk" class="form-control" 
                 value="<?= htmlspecialchars($data['nama_produk']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Kategori Produk</label>
          <select name="kategori_id" class="form-select" required>
            <?php
            $q2 = $conn->query("SELECT * FROM kategori_produk ORDER BY nama_kategori ASC");
            while ($row = $q2->fetch_assoc()) {
              $sel = ($row['id'] == $data['kategori_id']) ? "selected" : "";
              echo "<option value='{$row['id']}' $sel>".htmlspecialchars($row['nama_kategori'])."</option>";
            }
            ?>
          </select>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label fw-bold">Harga Beli</label>
          <input type="text" name="harga_beli" class="form-control rupiah" 
                 value="<?= number_format((float)$data['harga_beli'], 0, ',', '.') ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label fw-bold">Harga Jual</label>
          <input type="text" name="harga_jual" class="form-control rupiah" 
                 value="<?= number_format((float)$data['harga_jual'], 0, ',', '.') ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label fw-bold">Harga Jual Online</label>
          <input type="text" name="harga_jual_online" class="form-control rupiah" 
                 value="<?= number_format((float)$data['harga_jual_online'], 0, ',', '.') ?>">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Kode Produk (Barcode)</label>
          <input type="text" name="kode_barcode" class="form-control" 
                 value="<?= htmlspecialchars($data['kode_barcode']) ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Jumlah Stok</label>
          <input type="number" name="stok" class="form-control" 
                 value="<?= $data['stok'] ?>">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Gambar Produk</label><br>
        <?php
          $defaultImg = "../../uploads/produk/Belum_ada_gambar_barang-removebg-preview.png";
          $gambar = (!empty($data['gambar']) && file_exists(__DIR__."/../../uploads/produk/".$data['gambar']))
                    ? "../../uploads/produk/".rawurlencode($data['gambar'])
                    : $defaultImg;
        ?>
        <img src="<?= $gambar ?>" width="120" class="mb-2 rounded border"><br>
        <input type="file" name="gambar" class="form-control" accept="image/*">
        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Serial Number</label>
        <textarea name="serial_number" class="form-control" rows="2"><?= htmlspecialchars($data['serial_number']) ?></textarea>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Link Siplah</label>
          <input type="url" name="link_siplah" class="form-control" 
                 value="<?= htmlspecialchars($data['link_siplah']) ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Link Inaproc</label>
          <input type="url" name="link_inaproc" class="form-control" 
                 value="<?= htmlspecialchars($data['link_inaproc']) ?>">
        </div>
      </div>

      <div class="d-flex justify-content-end">
        <button type="submit" class="btn-add me-2">
          <i class="fa fa-save"></i> Update
        </button>
        <a href="produk.php" class="btn-add">
          <i class="fa fa-times"></i> Batal
        </a>
      </div>
    </form>
  </div>
</div>

<?php include "../../footer.php"; ?>

<!-- JS untuk format rupiah saat input -->
<script>
document.querySelectorAll('.rupiah').forEach(function(el){
  el.addEventListener('input', function(){
    let val = this.value.replace(/\D/g, ''); // hanya angka
    this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  });
});
</script>
