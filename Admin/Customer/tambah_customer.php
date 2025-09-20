<?php
include "../../config.php";
include "../../header.php";

$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama_customer'] ?? '');
    $jenis  = $_POST['jenis'] ?? 'Toko';
    $alamat = trim($_POST['alamat'] ?? '');
    $telp   = trim($_POST['no_telp'] ?? '');
    $email  = trim($_POST['email'] ?? '');

    if ($nama === '') {
        $err = "Nama customer wajib diisi.";
    } elseif (!in_array($jenis, ['Toko','Instansi'])) {
        $err = "Jenis customer tidak valid.";
    } else {
        $stmt = $conn->prepare("INSERT INTO customer (nama_customer, jenis, alamat, no_telp, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $jenis, $alamat, $telp, $email);
        if ($stmt->execute()) {
            header("Location: customer.php?msg=saved");
            exit;
        } else {
            $err = "Gagal menyimpan data: " . $conn->error;
        }
    }
}
?>

<!-- Header -->
<div class="page-header">
  <h3><i class="fa fa-plus"></i> Tambah Customer</h3>
</div>

<!-- Alert Error -->
<?php if ($err): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
<?php endif; ?>

<!-- Form Tambah Customer -->
<form method="post">
  <label>Nama Customer</label>
  <input type="text" name="nama_customer" class="form-control" required>

  <label>Jenis</label>
  <select name="jenis" class="form-control" required>
    <option value="Toko">Toko</option>
    <option value="Instansi">Instansi</option>
  </select>

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
    <a href="customer.php" class="btn btn-danger">
      <i class="fa fa-times"></i> Batal
    </a>
  </div>
</form>

<?php include "../../footer.php"; ?>
