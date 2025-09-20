<?php
session_start();
include "../../config.php";

if (!isset($_SESSION['username']) || $_SESSION['role']!='teknisi') {
    header("Location: ../../login.php?role=teknisi");
    exit;
}

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$service = $conn->query("SELECT s.*, p.no_invoice, c.nama_customer, c.no_telp 
                         FROM service s
                         JOIN penjualan p ON s.penjualan_id=p.id
                         JOIN customer c ON p.customer_id=c.id
                         WHERE s.id=$id")->fetch_assoc();

if(!$service) die("Service tidak ditemukan");

// Update status teknisi
if(isset($_POST['update_status'])){
    $status = $_POST['status_teknisi'];
    $catatan = $_POST['catatan_teknisi'];
    $stmt = $conn->prepare("UPDATE service 
                            SET status_teknisi=?, status_admin=?, catatan_teknisi=? 
                            WHERE id=?");
    $stmt->bind_param("sssi",$status,$status,$catatan,$id);
    $stmt->execute();
    header("Location: service_detail.php?id=$id");
    exit;
}

// Tambah sparepart
if(isset($_POST['tambah_sparepart'])){
    $produk_id = intval($_POST['produk_id']);
    $qty = intval($_POST['qty']);
    $harga = floatval($_POST['harga']);
    $subtotal = $qty * $harga;

    $stmt = $conn->prepare("INSERT INTO service_sparepart (service_id,produk_id,qty,harga,subtotal) 
                            VALUES (?,?,?,?,?)");
    $stmt->bind_param("iiidd",$id,$produk_id,$qty,$harga,$subtotal);
    $stmt->execute();

    // kurangi stok produk
    $conn->query("UPDATE produk SET stok = stok - $qty WHERE id=$produk_id");

    header("Location: service_detail.php?id=$id");
    exit;
}

$spareparts = $conn->query("SELECT ss.*, pr.nama_produk 
                            FROM service_sparepart ss
                            JOIN produk pr ON ss.produk_id=pr.id
                            WHERE ss.service_id=$id");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Service (Teknisi)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">
  <div class="card p-4">
    <h3 class="fw-bold text-primary">🔧 Detail Service</h3>
    <p><b>No Service:</b> <?=$service['no_service'];?> <br>
       <b>No Invoice:</b> <?=$service['no_invoice'];?> <br>
       <b>Customer:</b> <?=$service['nama_customer'];?> (<?=$service['no_telp'];?>)</p>

    <!-- Update Status -->
    <form method="post" class="mb-4">
      <label class="form-label">Status Pekerjaan</label>
      <select name="status_teknisi" class="form-select">
        <option value="Open" <?=$service['status_teknisi']=="Open"?"selected":"";?>>Open</option>
        <option value="Proses" <?=$service['status_teknisi']=="Proses"?"selected":"";?>>Proses</option>
        <option value="Pending" <?=$service['status_teknisi']=="Pending"?"selected":"";?>>Pending</option>
        <option value="Selesai" <?=$service['status_teknisi']=="Selesai"?"selected":"";?>>Selesai</option>
      </select>
      <textarea name="catatan_teknisi" class="form-control mt-2" placeholder="Catatan teknisi"><?=$service['catatan_teknisi'];?></textarea>
      <button type="submit" name="update_status" class="btn btn-primary mt-2">💾 Simpan</button>
    </form>

    <!-- Sparepart -->
    <h5 class="fw-bold">Sparepart Digunakan</h5>
    <form method="post" class="row g-2 mb-3">
      <div class="col-md-5">
        <select name="produk_id" class="form-select">
          <?php
          $prods=$conn->query("SELECT * FROM produk WHERE stok>0 ORDER BY nama_produk ASC");
          while($p=$prods->fetch_assoc()): ?>
          <option value="<?=$p['id'];?>"><?=$p['nama_produk'];?> (Stok: <?=$p['stok'];?>)</option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-2">
        <input type="number" name="qty" class="form-control" placeholder="Qty" required>
      </div>
      <div class="col-md-3">
        <input type="number" step="0.01" name="harga" class="form-control" placeholder="Harga" required>
      </div>
      <div class="col-md-2">
        <button type="submit" name="tambah_sparepart" class="btn btn-success w-100">➕ Tambah</button>
      </div>
    </form>

    <table class="table table-bordered">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Sparepart</th>
          <th>Qty</th>
          <th>Harga</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php if($spareparts->num_rows>0): $no=1; while($s=$spareparts->fetch_assoc()): ?>
        <tr>
          <td><?=$no++;?></td>
          <td><?=$s['nama_produk'];?></td>
          <td><?=$s['qty'];?></td>
          <td>Rp <?=number_format($s['harga'],0,",",".");?></td>
          <td>Rp <?=number_format($s['subtotal'],0,",",".");?></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="5" class="text-center">Belum ada sparepart</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
