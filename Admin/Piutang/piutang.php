<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

include "../../config.php";
include "../../header.php";

// --- FILTER JENIS ---
$filter = $_GET['jenis'] ?? '';
$allowed = ['instansi','toko','service'];
if ($filter != '' && !in_array($filter, $allowed)) {
    $filter = ''; // reset kalau nilai tidak valid
}

// --- QUERY by jenis ---
if ($filter == 'instansi') {
    $sql = "SELECT pi.*, p.no_invoice, p.tanggal, c.nama_customer 
            FROM piutang pi
            LEFT JOIN penjualan_instansi p ON pi.penjualan_id=p.id
            LEFT JOIN customer c ON p.customer_id=c.id
            WHERE pi.jenis='instansi'
            ORDER BY p.tanggal DESC";
} elseif ($filter == 'toko') {
    $sql = "SELECT pi.*, p.no_invoice, p.tanggal, c.nama_customer 
            FROM piutang pi
            LEFT JOIN penjualan_toko p ON pi.penjualan_id=p.id
            LEFT JOIN customer c ON p.customer_id=c.id
            WHERE pi.jenis='toko'
            ORDER BY p.tanggal DESC";
} elseif ($filter == 'service') {
    $sql = "SELECT pi.*, p.no_invoice, p.tanggal, c.nama_customer 
            FROM piutang pi
            LEFT JOIN penjualan_service p ON pi.penjualan_id=p.id
            LEFT JOIN customer c ON p.customer_id=c.id
            WHERE pi.jenis='service'
            ORDER BY p.tanggal DESC";
} else {
    // gabungan semua jenis
    $sql = "
        SELECT pi.*, p.no_invoice, p.tanggal, c.nama_customer, 'instansi' as jenis_asal
        FROM piutang pi
        LEFT JOIN penjualan_instansi p ON pi.penjualan_id=p.id
        LEFT JOIN customer c ON p.customer_id=c.id
        WHERE pi.jenis='instansi'
        UNION ALL
        SELECT pi.*, p.no_invoice, p.tanggal, c.nama_customer, 'toko' as jenis_asal
        FROM piutang pi
        LEFT JOIN penjualan_toko p ON pi.penjualan_id=p.id
        LEFT JOIN customer c ON p.customer_id=c.id
        WHERE pi.jenis='toko'
        UNION ALL
        SELECT pi.*, p.no_invoice, p.tanggal, c.nama_customer, 'service' as jenis_asal
        FROM piutang pi
        LEFT JOIN penjualan_service p ON pi.penjualan_id=p.id
        LEFT JOIN customer c ON p.customer_id=c.id
        WHERE pi.jenis='service'
        ORDER BY tanggal DESC
    ";
}

$result = $conn->query($sql);
if(!$result) {
    logMessage("Query Piutang gagal: " . $conn->error, "ERROR");
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold text-primary">
    <i class="fa-solid fa-hand-holding-dollar me-2"></i> Data Piutang
  </h4>
  <form method="get" class="d-flex align-items-center">
    <label class="me-2 fw-semibold">Filter:</label>
    <select name="jenis" onchange="this.form.submit()" class="form-select form-select-sm">
      <option value="" <?=($filter==''?'selected':'')?>>Semua</option>
      <option value="instansi" <?=($filter=='instansi'?'selected':'')?>>Instansi</option>
      <option value="toko" <?=($filter=='toko'?'selected':'')?>>Toko</option>
      <option value="service" <?=($filter=='service'?'selected':'')?>>Service</option>
    </select>
  </form>
</div>

<div class="card-dark">
  <div class="table-responsive">
    <table class="table table-bordered align-middle mb-0">
      <thead class="table-primary text-center">
        <tr>
          <th style="width:50px">No</th>
          <th>No. Invoice</th>
          <th>Customer</th>
          <th>Tanggal</th>
          <th>Jatuh Tempo</th>
          <th>Total</th>
          <th>Sisa Piutang</th>
          <th>Jenis</th>
          <th>Status</th>
          <th style="width:120px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if($result && $result->num_rows>0): $no=1; while($row=$result->fetch_assoc()): ?>
        <tr>
          <td class="text-center"><?= $no++; ?></td>
          <td><?= htmlspecialchars($row['no_invoice']); ?></td>
          <td><?= htmlspecialchars($row['nama_customer']); ?></td>
          <td><?= date("d/m/Y", strtotime($row['tanggal'])); ?></td>
          <td><?= date("d/m/Y", strtotime($row['jatuh_tempo'])); ?></td>
          <td class="text-end">Rp <?= number_format($row['total'],0,",","."); ?></td>
          <td class="text-end fw-bold text-primary">Rp <?= number_format($row['sisa_piutang'],0,",","."); ?></td>
          <td class="text-capitalize text-center">
            <?= htmlspecialchars(isset($row['jenis_asal']) ? $row['jenis_asal'] : $row['jenis']); ?>
          </td>
          <td class="text-center">
            <?php if($row['status']=='Lunas'): ?>
              <span class="badge bg-success">Lunas</span>
            <?php else: ?>
              <span class="badge bg-danger">Belum Lunas</span>
            <?php endif; ?>
          </td>
          <td class="text-center">
            <div class="btn-group">
              <a href="edit_piutang.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm" title="Edit">
                <i class="fa-solid fa-pen-to-square"></i>
              </a>
              <?php if($row['status']!='Lunas'): ?>
                <a href="bayar_piutang.php?id=<?= $row['id']; ?>" class="btn btn-success btn-sm" title="Bayar">
                  <i class="fa-solid fa-money-bill-wave"></i>
                </a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="10" class="text-center">Tidak ada data piutang</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include "../../footer.php"; ?>
