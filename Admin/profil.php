<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "../config.php"; // sesuaikan path ke config database

$username = $_SESSION['username'];

// Ambil data user dari database
$query = $conn->prepare("SELECT username, nama_lengkap, role, created_at FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
?>
<?php include "header.php"; ?> <!-- include header sesuai role -->

<h3><i class="fa fa-id-badge"></i> Profil Saya</h3>
<hr>

<div class="card" style="max-width:600px;">
  <div class="card-body">
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
    <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($user['nama_lengkap']); ?></p>
    <p><strong>Role:</strong> <?= ucfirst($user['role']); ?></p>
    <p><strong>Dibuat:</strong> <?= date("d M Y H:i", strtotime($user['created_at'])); ?></p>
  </div>
</div>

<hr>
<h4>Ubah Password</h4>
<form method="POST">
  <div class="mb-3">
    <label>Password Baru</label>
    <input type="password" name="new_password" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Konfirmasi Password</label>
    <input type="password" name="confirm_password" class="form-control" required>
  </div>
  <button type="submit" name="update_password" class="btn btn-primary-custom">Simpan</button>
</form>

<?php
if (isset($_POST['update_password'])) {
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        echo "<div class='alert-danger-custom alert-custom mt-3'>Konfirmasi password tidak cocok!</div>";
    } else {
        $hash = password_hash($new, PASSWORD_BCRYPT);
        $update = $conn->prepare("UPDATE users SET password=? WHERE username=?");
        $update->bind_param("ss", $hash, $username);
        if ($update->execute()) {
            echo "<div class='alert-success-custom alert-custom mt-3'>Password berhasil diubah!</div>";
        } else {
            echo "<div class='alert-danger-custom alert-custom mt-3'>Gagal mengubah password!</div>";
        }
    }
}
?>

<?php include "footer.php"; ?>
