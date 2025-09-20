<?php
include "../../config.php";
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role']!="teknisi") {
    header("Location: ../login.php?role=teknisi");
    exit;
}
$username = $_SESSION['username'];
$user = $conn->query("SELECT * FROM users WHERE username='$username'")->fetch_assoc();

$sql = "SELECT s.*, p.no_invoice, c.nama_customer 
        FROM service s
        JOIN penjualan p ON s.penjualan_id=p.id
        JOIN customer c ON p.customer_id=c.id
        WHERE s.teknisi_id={$user['id']}
        ORDER BY s.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Teknisi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h2 class="fw-bold mb-3">🛠 Dashboard Teknisi</h2>
<p>Selamat datang, <?=$user['nama_lengkap'];?>!</p>

<table class="table table-bordered">
  <thead class="table-dark">
    <tr>
      <th>No Service</th>
      <th>No Invoice</th>
      <th>Customer</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row=$result->fetch_assoc()): ?>
    <tr>
      <td><?=$row['no_service'];?></td>
      <td><?=$row['no_invoice'];?></td>
      <td><?=$row['nama_customer'];?></td>
      <td><?=$row['status_teknisi'];?></td>
      <td>
        <a href="update_status.php?id=<?=$row['id'];?>" class="btn btn-primary btn-sm">🔄 Update</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</body>
</html>
