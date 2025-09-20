<?php
include "../../config.php";
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role']!="teknisi") {
    header("Location: ../login.php?role=teknisi");
    exit;
}
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = intval($_GET['id']);

$service = $conn->query("SELECT * FROM service WHERE id=$id")->fetch_assoc();
if (!$service) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD']=="POST") {
    $status = $_POST['status_teknisi'];
    $catatan = $_POST['catatan_teknisi'];

    $stmt=$conn->prepare("UPDATE service SET status_teknisi=?, status_admin=?, catatan_teknisi=? WHERE id=?");
    $stmt->bind_param("sssi",$status,$status,$catatan,$id);
    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Update Status Service</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h2 class="fw-bold">🔄 Update Status Service</h2>

<form method="post">
  <div class="mb-3">
    <label>Status</label>
    <select name="status_teknisi" class="form-control">
      <option value="Open" <?=$service['status_teknisi']=="Open"?"selected":"";?>>Open</option>
      <option value="Proses" <?=$service['status_teknisi']=="Proses"?"selected":"";?>>Proses</option>
      <option value="Pending" <?=$service['status_teknisi']=="Pending"?"selected":"";?>>Pending</option>
      <option value="Selesai" <?=$service['status_teknisi']=="Selesai"?"selected":"";?>>Selesai</option>
    </select>
  </div>
  <div class="mb-3">
    <label>Catatan Teknisi</label>
    <textarea name="catatan_teknisi" class="form-control"><?=$service['catatan_teknisi'];?></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Update</button>
  <a href="index.php" class="btn btn-secondary">⬅ Kembali</a>
</form>
</body>
</html>
