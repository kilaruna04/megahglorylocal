<?php
include "../../config.php";
include "../../header.php";

// Ambil ID customer
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: customer.php");
    exit;
}

// Ambil data customer lama
$stmt = $conn->prepare("SELECT * FROM customer WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    echo '<div class="alert alert-danger">Data customer tidak ditemukan.</div>';
    include "../../footer.php";
    exit;
}

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
        $stmt = $conn->prepare("UPDATE customer 
                                SET nama_customer=?, jenis=?, alamat=?, no_telp=?, email=? 
                                WHERE id=?");
        $stmt->bind_param("sssssi", $nama, $jenis, $alamat, $telp, $email, $id);
        if ($stmt->execute()) {
            header("Location: customer.php?msg=updated");
            exit;
        } else {
            $err = "Gagal mengupdate data: " . $conn->error;
        }
    }
}
?>

<!-- Header -->
<div class="page-header">
  <h3><i class="fa fa-edit"></i> Edit Customer</h3>
</div>

<!-- Alert Error -->
<?php if ($err): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
<?php endif; ?>

<!-- Form Edit Customer -->
<form method="post">
  <label>Nama Customer</label>
  <input type="text" name="nama_customer" class="form-control" 
         value="<?= htmlspecialchars($customer['nama_customer']); ?>" required>

  <label>Jenis</label>
  <select name="jenis" class="form-control" required>
    <option value="Toko" <?= $customer['jenis'] === 'Toko' ? 'selected' : '' ?>>Toko</option>
    <option value="Instansi" <?= $customer['jenis'] === 'Instansi' ? 'selected' : '' ?>>Instansi</option>
  </select>

  <label>Alamat</label>
  <textarea name="alamat" class="form-control"><?= htmlspecialchars($customer['alamat']); ?></textarea>

  <label>No. Telp</label>
  <input type="text" name="no_telp" class="form-control" 
         value="<?= htmlspecialchars($customer['no_telp']); ?>">

  <label>Email</label>
  <input type="email" name="email" class="form-control" 
         value="<?= htmlspecialchars($customer['email']); ?>">

  <div class="mt-3 d-flex gap-2">
    <button type="submit" class="btn btn-warning">
      <i class="fa fa-save"></i> Update
    </button>
    <a href="customer.php" class="btn btn-danger">
      <i class="fa fa-times"></i> Batal
    </a>
  </div>
</form>

<?php include "../../footer.php"; ?>
