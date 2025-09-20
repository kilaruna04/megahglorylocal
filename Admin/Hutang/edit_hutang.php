<?php
include "../../config.php";
include "../../header.php";

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return "Rp " . number_format((float)$angka, 0, ",", ".");
    }
}

$id = intval($_GET['id'] ?? 0);
$hutang = $conn->query("SELECT h.*, p.no_invoice, s.nama_supplier, p.total
                        FROM hutang h
                        LEFT JOIN pembelian p ON h.pembelian_id=p.id
                        LEFT JOIN supplier s ON p.supplier_id=s.id
                        WHERE h.id=$id")->fetch_assoc();
if (!$hutang) { header("Location: hutang.php"); exit; }

$err = "";
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $jatuh_tempo = $_POST['jatuh_tempo'] ?? $hutang['jatuh_tempo'];
    $status      = $_POST['status'] ?? $hutang['status'];

    $stmt = $conn->prepare("UPDATE hutang SET jatuh_tempo=?, status=? WHERE id=?");
    $stmt->bind_param("ssi", $jatuh_tempo, $status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: hutang.php?msg=updated");
    exit;
}
?>
<div class="container-fluid">
  <h2 class="fw-bold text-primary mb-3"><i class="fa fa-edit"></i> Edit Hutang</h2>

  <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>

  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <p><strong>No Invoice:</strong> <?=htmlspecialchars($hutang['no_invoice'])?></p>
      <p><strong>Supplier:</strong> <?=htmlspecialchars($hutang['nama_supplier'])?></p>
      <p><strong>Total:</strong> <?=formatRupiah($hutang['total'])?></p>
      <p><strong>Sisa Hutang:</strong> <?=formatRupiah($hutang['sisa_hutang'])?></p>
    </div>
  </div>

  <form method="post" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Jatuh Tempo</label>
      <input type="date" name="jatuh_tempo" class="form-control" value="<?=$hutang['jatuh_tempo']?>" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="Belum Lunas" <?=$hutang['status']=='Belum Lunas'?'selected':''?>>Belum Lunas</option>
        <option value="Lunas" <?=$hutang['status']=='Lunas'?'selected':''?>>Lunas</option>
      </select>
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-primary">💾 Simpan</button>
      <a href="hutang.php" class="btn btn-secondary">⬅ Kembali</a>
    </div>
  </form>
</div>
<?php include "../../footer.php"; ?>
