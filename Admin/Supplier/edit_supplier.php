<?php
include "../../config.php";
include "../../header.php";

// Ambil ID supplier
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: supplier.php");
    exit;
}

// Ambil data supplier lama
$stmt = $conn->prepare("SELECT * FROM supplier WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$supplier = $result->fetch_assoc();

if (!$supplier) {
    echo '<div class="alert alert-danger">Data supplier tidak ditemukan.</div>';
    include "../../footer.php";
    exit;
}

$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama_supplier'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $telp   = trim($_POST['no_telp'] ?? '');
    $email  = trim($_POST['email'] ?? '');

    if ($nama === '') {
        $err = "Nama supplier wajib diisi.";
    } else {
        $stmt = $conn->prepare("UPDATE supplier SET nama_supplier=?, alamat=?, no_telp=?, email=? WHERE id=?");
        $stmt->bind_param("ssssi", $nama, $alamat, $telp, $email, $id);
        if ($stmt->execute()) {
            header("Location: supplier.php?msg=updated");
            exit;
        } else {
            $err = "Gagal mengupdate data: " . $conn->error;
        }
    }
}
?>

<!-- Header -->
<div class="page-header">
  <h3><i class="fa fa-edit"></i> Edit Supplier</h3>
</div>

<!-- Alert Error -->
<?php if ($err): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
<?php endif; ?>

<!-- Form Edit Supplier -->
<form method="post">
  <label>Nama Supplier</label>
  <input type="text" name="nama_supplier" class="form-control" 
         value="<?= htmlspecialchars($supplier['nama_supplier']); ?>" required>

  <label>Alamat</label>
  <textarea name="alamat" class="form-control"><?= htmlspecialchars($supplier['alamat']); ?></textarea>

  <label>No. Telp</label>
  <input type="text" name="no_telp" class="form-control" 
         value="<?= htmlspecialchars($supplier['no_telp']); ?>">

  <label>Email</label>
  <input type="email" name="email" class="form-control" 
         value="<?= htmlspecialchars($supplier['email']); ?>">

  <div class="mt-3 d-flex gap-2">
    <button type="submit" class="btn btn-warning">
      <i class="fa fa-save"></i> Update
    </button>
    <a href="supplier.php" class="btn btn-danger">
      <i class="fa fa-times"></i> Batal
    </a>
  </div>
</form>

<?php include "../../footer.php"; ?>
