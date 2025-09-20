<?php
// Service/Admin/Pembelian/edit_pembelian.php
// ==========================================
include "../../config.php";
include "../../header.php";
include "hutang_helpers.php";

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return "Rp " . number_format((float)$angka, 0, ",", ".");
    }
}

if (!isset($_GET['id'])) { header("Location: pembelian.php"); exit; }
$id = intval($_GET['id']);

$err = "";
$pembelian = $conn->query("SELECT * FROM pembelian WHERE id={$id}")->fetch_assoc();
if (!$pembelian) { die("Data pembelian tidak ditemukan."); }

// Ambil supplier untuk dropdown
$suppliers = $conn->query("SELECT id, nama_supplier FROM supplier ORDER BY nama_supplier ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_invoice   = trim($_POST['no_invoice'] ?? '');
    $tanggal      = $_POST['tanggal'] ?? $pembelian['tanggal'];
    $supplier_id  = intval($_POST['supplier_id'] ?? 0);
    $metode       = $_POST['metode_bayar'] ?? $pembelian['metode_bayar'];
    $total        = (float)($_POST['total'] ?? $pembelian['total']);
    $jatuh_tempo  = $_POST['jatuh_tempo'] ?? null;
    $keterangan   = $_POST['keterangan'] ?? $pembelian['keterangan'];

    // --- handle upload nota ---
    $nota_file = $pembelian['nota_file']; // default file lama
    if (!empty($_FILES['nota_file']['name'])) {
        $uploadDir = "../../uploads/nota_pembelian/";
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }

        $ext = pathinfo($_FILES['nota_file']['name'], PATHINFO_EXTENSION);
        $newName = "nota_" . date("YmdHis") . "." . $ext;
        $uploadFile = $uploadDir . $newName;

        if (move_uploaded_file($_FILES['nota_file']['tmp_name'], $uploadFile)) {
            // hapus file lama jika ada
            if (!empty($pembelian['nota_file']) && file_exists($uploadDir.$pembelian['nota_file'])) {
                unlink($uploadDir.$pembelian['nota_file']);
            }
            $nota_file = $newName;
        }
    }

    if ($no_invoice === '' || $supplier_id <= 0 || $total <= 0) {
        $err = "No. Invoice, Supplier, dan Total wajib diisi.";
    } else {
        $status_hutang = (strtolower($metode) === 'kredit') ? 'Belum Lunas' : 'Lunas';

        $stmt = $conn->prepare("UPDATE pembelian
                                SET no_invoice=?, tanggal=?, supplier_id=?, metode_bayar=?, total=?, status_hutang=?, keterangan=?, nota_file=?
                                WHERE id=?");
        $stmt->bind_param(
            "ssisdsssi", 
            $no_invoice,   // s
            $tanggal,      // s
            $supplier_id,  // i
            $metode,       // s
            $total,        // d
            $status_hutang,// s
            $keterangan,   // s
            $nota_file,    // s
            $id            // i
        );
        $stmt->execute();
        $stmt->close();

        // Sinkron hutang
        create_or_update_hutang($conn, $id, $tanggal, $metode, $total, $jatuh_tempo);

        header("Location: pembelian.php?msg=updated");
        exit;
    }
}
?>
<div class="container-fluid">
  <h2 class="fw-bold text-primary mb-3"><i class="fa fa-edit"></i> Edit Pembelian</h2>

  <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-4">
      <label class="form-label">No. Invoice</label>
      <input type="text" name="no_invoice" class="form-control" value="<?=htmlspecialchars($pembelian['no_invoice'])?>" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Tanggal</label>
      <input type="date" name="tanggal" class="form-control" value="<?=htmlspecialchars($pembelian['tanggal'])?>" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Supplier</label>
      <select name="supplier_id" class="form-select" required>
        <option value="">-- Pilih Supplier --</option>
        <?php while($s=$suppliers->fetch_assoc()): ?>
          <option value="<?=$s['id']?>" <?=$pembelian['supplier_id']==$s['id']?'selected':''?>>
            <?=htmlspecialchars($s['nama_supplier'])?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">Metode Bayar</label>
      <select name="metode_bayar" class="form-select" id="metodeBayar">
        <option value="Cash"   <?=$pembelian['metode_bayar']=='Cash'?'selected':''?>>Cash</option>
        <option value="Kredit" <?=$pembelian['metode_bayar']=='Kredit'?'selected':''?>>Kredit</option>
      </select>
    </div>

    <div class="col-md-4" id="wrapJatuhTempo" style="display:none;">
      <label class="form-label">Jatuh Tempo</label>
      <input type="date" name="jatuh_tempo" class="form-control" value="">
    </div>

    <div class="col-md-4">
      <label class="form-label">Total (Rp)</label>
      <input type="number" step="0.01" name="total" class="form-control" value="<?=htmlspecialchars($pembelian['total'])?>" required>
      <div class="form-text">Tersimpan: <?= formatRupiah($pembelian['total']); ?></div>
    </div>

    <div class="col-12">
      <label class="form-label">Keterangan</label>
      <textarea name="keterangan" class="form-control" rows="3"><?=htmlspecialchars($pembelian['keterangan'] ?? '')?></textarea>
    </div>

    <div class="col-12">
      <label class="form-label">Nota Pembelian</label>
      <?php if (!empty($pembelian['nota_file'])): ?>
        <p class="mb-1">File saat ini: 
          <a href="../../uploads/nota_pembelian/<?=htmlspecialchars($pembelian['nota_file'])?>" target="_blank" class="text-decoration-none">
            <i class="fa fa-file"></i> Lihat Nota
          </a>
        </p>
      <?php endif; ?>
      <input type="file" name="nota_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
      <div class="form-text">Upload baru akan mengganti file lama.</div>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-primary">💾 Update</button>
      <a href="pembelian.php" class="btn btn-secondary">⬅ Kembali</a>
    </div>
  </form>
</div>

<script>
  (function(){
    const metodeSel = document.getElementById('metodeBayar');
    const wrapJT = document.getElementById('wrapJatuhTempo');
    function toggleJT(){ wrapJT.style.display = (metodeSel.value === 'Kredit') ? '' : 'none'; }
    metodeSel.addEventListener('change', toggleJT);
    toggleJT();
  })();
</script>

<?php include "../../footer.php"; ?>
