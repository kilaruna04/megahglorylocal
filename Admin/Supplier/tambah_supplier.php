<?php
include "../../config.php";
include "../../header.php";

$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama_supplier'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $telp   = trim($_POST['no_telp'] ?? '');
    $email  = trim($_POST['email'] ?? '');

    if ($nama === '') {
        $err = "Nama supplier wajib diisi.";
    } else {
        $stmt = $conn->prepare("INSERT INTO supplier (nama_supplier, alamat, no_telp, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $alamat, $telp, $email);
        if ($stmt->execute()) {
            header("Location: supplier.php?msg=saved");
            exit;
        } else {
            $err = "Gagal menyimpan data: " . $conn->error;
        }
    }
}
?>

<!-- Header -->
<div class="page-header">
  <h3><i class="fa fa-plus"></i> Tambah Supplier</h3>
</div>

<!-- Alert Error -->
<?php if ($err): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
<?php endif; ?>

<!-- Form Tambah Supplier -->
<form method="post">
  <label>Nama Supplier</label>
  <input type="text" name="nama_supplier" class="form-control" required>

  <label>Alamat</label>
  <textarea name="alamat" class="form-control"></textarea>

  <label>No. Telp</label>
  <input type="text" name="no_telp" class="form-control">

  <label>Email</label>
  <input type="email" name="email" class="form-control">

  <div class="mt-3 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
      <i class="fa fa-save"></i> Simpan
    </button>
    <a href="supplier.php" class="btn btn-danger">
      <i class="fa fa-times"></i> Batal
    </a>
  </div>
</form>

<?php include "../../footer.php"; ?>
