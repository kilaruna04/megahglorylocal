<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php?role=admin");
    exit;
}
include "../../config.php";
include "../../header.php"; 
?>

<div class="container-fluid">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-gradient mb-0">
      <i class="fa fa-plus-circle"></i> Tambah Produk
    </h2>
    <a href="produk.php" class="btn-add">
      <i class="fa fa-arrow-left"></i> Kembali
    </a>
  </div>

  <!-- Card Form -->
  <div class="card-dark p-4 rounded shadow-sm">
    <form action="simpan_produk.php" method="post" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Nama Produk</label>
          <input type="text" name="nama_produk" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Kategori Produk</label>
          <select name="kategori_id" class="form-select" required>
            <option value="">-- Pilih Kategori --</option>
            <?php
            $q = $conn->query("SELECT * FROM kategori_produk ORDER BY nama_kategori ASC");
            while ($row = $q->fetch_assoc()) {
              echo "<option value='{$row['id']}'>".htmlspecialchars($row['nama_kategori'])."</option>";
            }
            ?>
          </select>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label fw-bold">Harga Beli</label>
          <input type="text" name="harga_beli" class="form-control rupiah" required>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label fw-bold">Harga Jual</label>
          <input type="text" name="harga_jual" class="form-control rupiah" required>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label fw-bold">Harga Jual Online</label>
          <input type="text" name="harga_jual_online" class="form-control rupiah">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Kode Produk (Barcode)</label>
          <input type="text" name="kode_barcode" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Jumlah Stok</label>
          <input type="number" name="stok" class="form-control" value="0">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Gambar Produk</label>
        <input type="file" name="gambar" class="form-control" accept="image/*">
        <small class="text-muted">
          Jika tidak upload, otomatis akan menggunakan gambar default <b>Belum_ada_gambar_barang-removebg-preview.png</b>
        </small>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Serial Number</label>
        <textarea name="serial_number" class="form-control" rows="2"></textarea>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Link Siplah</label>
          <input type="url" name="link_siplah" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Link Inaproc</label>
          <input type="url" name="link_inaproc" class="form-control">
        </div>
      </div>

      <!-- Tombol aksi -->
      <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn-add me-2">
          <i class="fa fa-save"></i> Simpan
        </button>
        <a href="produk.php" class="btn-add">
          <i class="fa fa-times"></i> Batal
        </a>
      </div>
    </form>
  </div>
</div>

<?php include "../../footer.php"; ?>

<!-- JS format rupiah -->
<script>
document.querySelectorAll('.rupiah').forEach(function(el){
  el.addEventListener('input', function(){
    let val = this.value.replace(/\D/g, ''); // hanya angka
    this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  });
});
</script>
