<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

include "../../config.php";
include "../../header.php";

// ambil ID piutang
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "<script>alert('ID piutang tidak valid');window.location='piutang.php';</script>";
    exit;
}

// ambil data piutang
$sql = "SELECT pi.*, 
        CASE pi.jenis 
          WHEN 'instansi' THEN (SELECT no_invoice FROM penjualan_instansi WHERE id=pi.penjualan_id) 
          WHEN 'toko' THEN (SELECT no_invoice FROM penjualan_toko WHERE id=pi.penjualan_id) 
          WHEN 'service' THEN (SELECT no_invoice FROM penjualan_service WHERE id=pi.penjualan_id) 
        END as no_invoice,
        c.nama_customer
        FROM piutang pi
        LEFT JOIN customer c ON (
          (pi.jenis='instansi' AND c.id=(SELECT customer_id FROM penjualan_instansi WHERE id=pi.penjualan_id)) OR
          (pi.jenis='toko' AND c.id=(SELECT customer_id FROM penjualan_toko WHERE id=pi.penjualan_id)) OR
          (pi.jenis='service' AND c.id=(SELECT customer_id FROM penjualan_service WHERE id=pi.penjualan_id))
        )
        WHERE pi.id=$id";
$piutang = $conn->query($sql)->fetch_assoc();
if (!$piutang) {
    echo "<script>alert('Data piutang tidak ditemukan');window.location='piutang.php';</script>";
    exit;
}

// jika submit pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal'];
    $jumlah = str_replace(".", "", $_POST['jumlah']); // hilangkan format ribuan
    $keterangan = $_POST['keterangan'];

    // insert ke piutang_pembayaran
    $stmt = $conn->prepare("INSERT INTO piutang_pembayaran (piutang_id, tanggal, jumlah, keterangan) VALUES (?,?,?,?)");
    $stmt->bind_param("isds", $id, $tanggal, $jumlah, $keterangan);
    $stmt->execute();
    $stmt->close();

    // update sisa piutang
    $sisa = $piutang['sisa_piutang'] - $jumlah;
    if ($sisa <= 0) {
        $status = "Lunas";
        $sisa = 0;
    } else {
        $status = "Belum Lunas";
    }
    $stmt = $conn->prepare("UPDATE piutang SET sisa_piutang=?, status=? WHERE id=?");
    $stmt->bind_param("dsi", $sisa, $status, $id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Pembayaran berhasil disimpan');window.location='piutang.php';</script>";
    exit;
}
?>

<div class="container-fluid">
  <h4><i class="fa-solid fa-money-bill-wave"></i> Bayar Piutang</h4>
  
  <div class="card-dark p-3 mb-3">
    <p><b>No Invoice:</b> <?= $piutang['no_invoice']; ?></p>
    <p><b>Customer:</b> <?= $piutang['nama_customer']; ?></p>
    <p><b>Total:</b> Rp <?= number_format($piutang['total'],0,",","."); ?></p>
    <p><b>Sisa Piutang:</b> <span class="text-primary fw-bold">Rp <?= number_format($piutang['sisa_piutang'],0,",","."); ?></span></p>
    <p><b>Status:</b> <?= $piutang['status']; ?></p>
  </div>

  <form method="post">
    <div class="card-dark p-3 mb-3">
      <div class="row g-3">
        <div class="col-md-3">
          <label>Tanggal Bayar</label>
          <input type="date" name="tanggal" class="form-control" value="<?=date('Y-m-d');?>" required>
        </div>
        <div class="col-md-3">
          <label>Jumlah Bayar</label>
          <input type="text" name="jumlah" id="jumlah" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label>Keterangan</label>
          <input type="text" name="keterangan" class="form-control">
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan Pembayaran</button>
    <a href="piutang.php" class="btn btn-secondary">Kembali</a>
  </form>
</div>

<script>
// auto format rupiah di input jumlah
function formatRupiah(angka) {
  return angka.replace(/\D/g, "")
              .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
document.getElementById("jumlah").addEventListener("input", function(e) {
  this.value = formatRupiah(this.value);
});
</script>

<?php include "../../footer.php"; ?>
