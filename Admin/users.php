<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php?role=admin");
    exit;
}
include "../config.php"; 
include "header.php";

// Tambah user
if (isset($_POST['tambah'])) {
    $username = $_POST['username'];
    $nama = $_POST['nama_lengkap'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $cek = $conn->prepare("SELECT id FROM users WHERE username=?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        echo "<div class='alert-danger-custom alert-custom'>Username sudah ada!</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username,password,nama_lengkap,role) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $username,$password,$nama,$role);
        if ($stmt->execute()) {
            echo "<div class='alert-success-custom alert-custom'>User berhasil ditambahkan</div>";
        } else {
            echo "<div class='alert-danger-custom alert-custom'>Gagal menambah user</div>";
        }
    }
}

// Hapus user
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM users WHERE id=$id");
    echo "<div class='alert-success-custom alert-custom'>User berhasil dihapus</div>";
}

// Ambil semua user
$users = $conn->query("SELECT * FROM users ORDER BY id ASC");
?>

<h3><i class="fa fa-user-cog"></i> Kelola User</h3>
<hr>

<!-- Form Tambah User -->
<div class="card mb-4" style="max-width:600px;">
  <div class="card-header bg-primary text-white">Tambah User</div>
  <div class="card-body">
    <form method="POST">
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select" required>
          <option value="admin">Admin</option>
          <option value="teknisi">Teknisi</option>
        </select>
      </div>
      <button type="submit" name="tambah" class="btn btn-primary-custom">Tambah</button>
    </form>
  </div>
</div>

<!-- Daftar User -->
<div class="card">
  <div class="card-header bg-secondary text-white">Daftar User</div>
  <div class="card-body">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Username</th>
          <th>Nama Lengkap</th>
          <th>Role</th>
          <th>Dibuat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while($row = $users->fetch_assoc()): ?>
        <tr>
          <td class="text-center"><?= $no++; ?></td>
          <td><?= htmlspecialchars($row['username']); ?></td>
          <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
          <td><?= ucfirst($row['role']); ?></td>
          <td><?= date("d-m-Y H:i", strtotime($row['created_at'])); ?></td>
          <td>
            <a href="edit_user.php?id=<?= $row['id']; ?>" class="btn-edit">✏️ Edit</a>
            <a href="users.php?hapus=<?= $row['id']; ?>" class="btn-hapus" onclick="return confirm('Yakin hapus user ini?')">🗑 Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include "footer.php"; ?>
