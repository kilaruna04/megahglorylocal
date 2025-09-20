<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php?role=admin");
    exit;
}
include "../config.php";
include "header.php";

if (!isset($_GET['id'])) {
    echo "<div class='alert-danger-custom alert-custom'>ID User tidak ditemukan!</div>";
    include "footer.php";
    exit;
}

$id = intval($_GET['id']);
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
if (!$user) {
    echo "<div class='alert-danger-custom alert-custom'>User tidak ditemukan!</div>";
    include "footer.php";
    exit;
}

// Update user
if (isset($_POST['update'])) {
    $nama = $_POST['nama_lengkap'];
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $sql = "UPDATE users SET nama_lengkap='$nama', role='$role', password='$pass' WHERE id=$id";
    } else {
        $sql = "UPDATE users SET nama_lengkap='$nama', role='$role' WHERE id=$id";
    }

    if ($conn->query($sql)) {
        echo "<div class='alert-success-custom alert-custom'>User berhasil diupdate. <a href='users.php'>Kembali</a></div>";
    } else {
        echo "<div class='alert-danger-custom alert-custom'>Gagal update user!</div>";
    }
}
?>

<h3><i class="fa fa-user-edit"></i> Edit User</h3>
<hr>

<div class="card" style="max-width:600px;">
  <div class="card-body">
    <form method="POST">
      <div class="mb-3">
        <label>Username (tidak bisa diubah)</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" readonly>
      </div>
      <div class="mb-3">
        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']); ?>" required>
      </div>
      <div class="mb-3">
        <label>Password Baru (opsional)</label>
        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin ubah">
      </div>
      <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select" required>
          <option value="admin" <?= $user['role']=='admin'?'selected':''; ?>>Admin</option>
          <option value="teknisi" <?= $user['role']=='teknisi'?'selected':''; ?>>Teknisi</option>
        </select>
      </div>
      <button type="submit" name="update" class="btn btn-primary-custom">Simpan Perubahan</button>
      <a href="users.php" class="btn btn-secondary">Kembali</a>
    </form>
  </div>
</div>

<?php include "footer.php"; ?>
