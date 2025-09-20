<?php
include "../../config.php";
include "../../header.php";

// Ambil ID customer
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: customer.php");
    exit;
}

// Ambil data customer
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
?>

<!-- Header -->
<div class="page-header">
  <h3><i class="fa fa-eye"></i> Detail Customer</h3>
</div>

<!-- Detail Card -->
<div class="detail-card">
  <p><strong>Nama:</strong> <?= htmlspecialchars($customer['nama_customer']); ?></p>
  <p><strong>Jenis:</strong> 
    <?php if ($customer['jenis'] === 'Toko'): ?>
      <span class="badge badge-success">Toko</span>
    <?php elseif ($customer['jenis'] === 'Instansi'): ?>
      <span class="badge badge-primary">Instansi</span>
    <?php else: ?>
      <span class="badge badge-secondary"><?= htmlspecialchars($customer['jenis']); ?></span>
    <?php endif; ?>
  </p>
  <p><strong>Alamat:</strong> <?= htmlspecialchars($customer['alamat']); ?></p>
  <p><strong>No. Telp:</strong> <?= htmlspecialchars($customer['no_telp']); ?></p>
  <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']); ?></p>
</div>

<div class="mt-3 d-flex gap-2">
  <a href="edit_customer.php?id=<?= (int)$customer['id']; ?>" class="btn btn-warning">
    <i class="fa fa-edit"></i> Edit
  </a>
  <a href="customer.php" class="btn btn-secondary">
    <i class="fa fa-arrow-left"></i> Kembali
  </a>
</div>

<?php include "../../footer.php"; ?>
