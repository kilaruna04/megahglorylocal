<?php
if (!isset($_SESSION)) { session_start(); }
include "../../../config.php";
include "../../../header.php";

$id = intval($_GET['id']);
$piutang = $conn->query("SELECT pi.*, p.no_invoice, c.nama_customer 
                         FROM piutang_instansi pi
                         JOIN penjualan_instansi p ON pi.penjualan_id=p.id
                         LEFT JOIN customer c ON p.customer_id=c.id
                         WHERE pi.id=$id")->fetch_assoc();

if($_SERVER['REQUEST_METHOD']=='POST'){
    $tanggal = $_POST['tanggal'];
    $jumlah  = str_replace('.','',$_POST['jumlah']);
    $conn->query("INSERT INTO piutang_instansi_pembayaran (piutang_id,tanggal,jumlah) 
                  VALUES ($id,'$tanggal',$jumlah)");
    // update sisa
    $sisa = $piutang['sisa_piutang'] - $jumlah;
    $status = $sisa<=0?'Lunas':'Belum Lunas';
    $conn->query("UPDATE piutang_instansi SET sisa_piutang=$sisa, status='$status' WHERE id=$id");
    echo "<script>alert('Pembayaran berhasil dicatat');window.location='index.php';</script>";
    exit;
}
?>

<div class="container">
  <h4>Pembayaran Piutang Instansi</h4>
  <p><b>Invoice:</b> <?=$piutang['no_invoice']?> | <b>Customer:</b> <?=$piutang['nama_customer']?></p>
  <p><b>Sisa Piutang:</b> Rp <?=number_format($piutang['sisa_piutang'],0,',','.')?></p>

  <form method="post">
    <div class="mb-3">
      <label>Tanggal</label>
      <input type="date" name="tanggal" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Jumlah Bayar</label>
      <input type="text" name="jumlah" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Simpan Pembayaran</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
  </form>
</div>

<?php include "../../../footer.php"; ?>
