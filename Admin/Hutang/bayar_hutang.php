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
if ($_SERVER['REQUEST_METHOD']==='POST' && $hutang['status'] !== "Lunas") {
    $jumlah    = (float) str_replace('.', '', $_POST['jumlah'] ?? 0);
    $tanggal   = $_POST['tanggal'] ?? date('Y-m-d');
    $metode    = $_POST['metode'] ?? 'Cash';
    $nama_bank = $_POST['nama_bank'] ?? null;

    // Upload bukti bayar
    $bukti = null;
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . "/../../uploads/bukti_hutang/";
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        $origName = basename($_FILES["bukti"]["name"]);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $newName = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $origName);
        if (move_uploaded_file($_FILES["bukti"]["tmp_name"], $uploadDir . $newName)) {
            $bukti = $newName;
        }
    }

    if ($metode === 'Transfer' && empty($nama_bank)) {
        $err = "Silakan pilih nama bank untuk metode transfer.";
    } elseif ($jumlah <= 0) {
        $err = "Jumlah bayar harus lebih dari 0";
    } elseif ($jumlah > $hutang['sisa_hutang']) {
        $err = "Jumlah bayar melebihi sisa hutang!";
    } else {
        // Simpan pembayaran
        $stmt = $conn->prepare("INSERT INTO hutang_pembayaran 
            (hutang_id, tanggal, jumlah, metode, nama_bank, bukti_bayar) 
            VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("isdsss", $id, $tanggal, $jumlah, $metode, $nama_bank, $bukti);
        $stmt->execute();

        // Update hutang
        $conn->query("UPDATE hutang 
                      SET sisa_hutang = sisa_hutang - $jumlah, 
                          status = IF(sisa_hutang - $jumlah <= 0, 'Lunas', 'Belum Lunas')
                      WHERE id=$id");

        // Sinkron ke pembelian
        $cek = $conn->query("SELECT pembelian_id, status FROM hutang WHERE id=$id")->fetch_assoc();
        if ($cek) {
            $pembelian_id = $cek['pembelian_id'];
            $status       = $cek['status'];
            $conn->query("UPDATE pembelian SET status_hutang='$status' WHERE id=$pembelian_id");
        }

        header("Location: hutang.php?msg=paid");
        exit;
    }
}
?>

<div class="container-fluid">
  <div class="page-header">
    <h2 class="fw-bold text-gradient mb-0">
      <i class="fa fa-money-bill"></i> Bayar Hutang
    </h2>
    <a href="hutang.php" class="btn-add"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>

  <!-- Info Hutang -->
  <div class="card-dark mb-3 p-3">
    <p><strong>No Invoice:</strong> <?=htmlspecialchars($hutang['no_invoice'])?></p>
    <p><strong>Supplier:</strong> <?=htmlspecialchars($hutang['nama_supplier'])?></p>
    <p><strong>Total:</strong> <?=formatRupiah($hutang['total'])?></p>
    <p><strong>Sisa Hutang:</strong> <?=formatRupiah($hutang['sisa_hutang'])?></p>
    <p><strong>Status:</strong> 
      <?= $hutang['status']=="Lunas" 
            ? '<span class="badge badge-success">Lunas</span>' 
            : '<span class="badge badge-danger">Belum Lunas</span>' ?>
    </p>
  </div>

  <!-- Form Bayar (hanya kalau Belum Lunas) -->
  <?php if ($hutang['status'] !== "Lunas"): ?>
  <div class="card-dark p-3 mb-4">
    <form method="post" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-bold">Tanggal Bayar</label>
        <input type="date" name="tanggal" class="form-control" value="<?=date('Y-m-d')?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-bold">Jumlah Bayar</label>
        <input type="text" name="jumlah" class="form-control rupiah" required>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-bold">Metode Pembayaran</label>
        <select name="metode" id="metode" class="form-select" required>
          <option value="Cash">Cash</option>
          <option value="Transfer">Transfer Bank</option>
        </select>
      </div>

      <div class="col-md-6" id="bankField" style="display:none;">
        <label class="form-label fw-bold">Nama Bank</label>
        <select name="nama_bank" class="form-select">
          <option value="">-- Pilih Bank --</option>
          <option value="Mandiri 007">Bank Mandiri 007</option>
          <option value="Mandiri 710">Bank Mandiri 710</option>
          <option value="BRI">Bank BRI</option>
          <option value="BJB">Bank BJB</option>
          <option value="BNI">Bank BNI</option>
        </select>
      </div>

      <div class="col-md-12">
        <label class="form-label fw-bold">Upload Bukti Bayar</label>
        <input type="file" name="bukti" class="form-control" accept="image/*,application/pdf">
      </div>

      <div class="col-12 d-flex justify-content-end gap-2">
        <button type="submit" class="btn-add"><i class="fa fa-save"></i> Simpan</button>
      </div>
    </form>
  </div>
  <?php endif; ?>

  <!-- Riwayat Pembayaran -->
  <?php
  $riwayat = $conn->query("
    SELECT * FROM hutang_pembayaran 
    WHERE hutang_id = $id 
    ORDER BY tanggal DESC, id DESC
  ");
  ?>
  <div class="card-dark p-3">
    <h5><i class="fa fa-history"></i> Riwayat Pembayaran</h5>
    <div class="table-responsive mt-2">
      <table class="table table-modern align-middle mb-0">
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jumlah</th>
            <th>Metode</th>
            <th>Bank</th>
            <th>Bukti</th>
          </tr>
        </thead>
        <tbody>
          <?php if($riwayat && $riwayat->num_rows > 0): $no=1; ?>
            <?php while($row = $riwayat->fetch_assoc()): ?>
              <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-center"><?= htmlspecialchars($row['tanggal']) ?></td>
                <td class="text-end"><?= formatRupiah($row['jumlah']) ?></td>
                <td class="text-center"><?= htmlspecialchars($row['metode']) ?></td>
                <td class="text-center"><?= htmlspecialchars($row['nama_bank'] ?? '-') ?></td>
                <td class="text-center">
                  <?php if(!empty($row['bukti_bayar'])): ?>
                    <a href="../../uploads/bukti_hutang/<?= htmlspecialchars($row['bukti_bayar']) ?>" target="_blank" class="btn-aksi btn-aksi-info">
                      <i class="fa fa-file"></i> Lihat
                    </a>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center">Belum ada pembayaran.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../../footer.php"; ?>

<!-- JS: toggle bank field + format rupiah -->
<script>
document.getElementById('metode')?.addEventListener('change', function(){
  document.getElementById('bankField').style.display = 
    (this.value === 'Transfer') ? 'block' : 'none';
});

// Format input rupiah
document.querySelectorAll('.rupiah').forEach(function(el){
  el.addEventListener('input', function(){
    let val = this.value.replace(/\D/g, '');
    this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  });
});
</script>
