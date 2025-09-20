<?php
include "../../config.php";
include "../../header.php";

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return "Rp " . number_format((float)$angka, 0, ",", ".");
    }
}

$sql = "SELECT h.*, p.no_invoice, s.nama_supplier, p.total 
        FROM hutang h
        LEFT JOIN pembelian p ON h.pembelian_id=p.id
        LEFT JOIN supplier s ON p.supplier_id=s.id
        ORDER BY h.jatuh_tempo ASC";
$rs = $conn->query($sql);

// summary
$totalHutang = 0;
$totalSisa   = 0;
$countLunas  = 0;
$countBelum  = 0;
?>

<div class="container-fluid">
  <!-- Header -->
  <div class="page-header d-flex justify-content-between align-items-center">
    <h2 class="fw-bold text-gradient mb-0">
      <i class="fa fa-file-alt"></i> Laporan Hutang
    </h2>
    <div class="d-flex gap-2">
      <input type="text" id="searchInput" class="form-control" placeholder="Cari No Invoice / Supplier..." style="width:220px;">
      <a href="hutang.php" class="btn-add">
        <i class="fa fa-arrow-left"></i> Kembali
      </a>
    </div>
  </div>

  <!-- Card Tabel -->
  <div class="card-dark p-3">
    <div class="table-responsive">
      <table class="table table-modern align-middle mb-0" id="hutangTable">
        <thead>
          <tr>
            <th class="text-center">No</th>
            <th class="text-center">No. Invoice</th>
            <th>Supplier</th>
            <th class="text-center">Tgl Jatuh Tempo</th>
            <th class="text-center">Sisa Hari</th>
            <th class="text-right">Total</th>
            <th class="text-right">Sisa</th>
            <th class="text-center">Status</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($rs && $rs->num_rows): $no=1; ?>
            <?php while($row=$rs->fetch_assoc()): ?>
              <?php
                $jatuhTempo = strtotime($row['jatuh_tempo']);
                $hariSisa   = floor(($jatuhTempo - time()) / 86400);
                $class = "";

                if ($hariSisa < 0) {
                  $class = "table-danger"; // sudah jatuh tempo
                } elseif ($hariSisa <= 5) {
                  $class = "table-warning"; // mendekati jatuh tempo
                }

                // summary
                $totalHutang += $row['total'];
                $totalSisa   += $row['sisa_hutang'];
                if ($row['status']=="Lunas") {
                  $countLunas++;
                } else {
                  $countBelum++;
                }
              ?>
              <tr class="<?= $class ?>">
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-center invoice"><?= htmlspecialchars($row['no_invoice']) ?></td>
                <td class="supplier"><?= htmlspecialchars($row['nama_supplier']) ?></td>
                <td class="text-center"><?= date("d-m-Y", strtotime($row['jatuh_tempo'])) ?></td>
                <td class="text-center">
                  <?php if ($hariSisa < 0): ?>
                    Lewat <?= abs($hariSisa) ?> hari
                  <?php else: ?>
                    <?= $hariSisa ?> hari
                  <?php endif; ?>
                </td>
                <td class="text-right"><?= formatRupiah($row['total']) ?></td>
                <td class="text-right"><?= formatRupiah($row['sisa_hutang']) ?></td>
                <td class="text-center">
                  <?= $row['status']=="Lunas" 
                      ? '<span class="badge bg-success">Lunas</span>' 
                      : '<span class="badge bg-danger">Belum Lunas</span>' ?>
                </td>
                <td class="text-center">
                  <a href="detail_hutang.php?id=<?= (int)$row['id'] ?>" class="btn-aksi btn-aksi-info">
                    <i class="fa fa-eye"></i> Detail
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="9" class="text-center">Belum ada data hutang.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Summary -->
  <div class="card-dark p-3 mt-3">
    <h5 class="fw-bold"><i class="fa fa-chart-bar"></i> Ringkasan Laporan</h5>
    <div class="row mt-2">
      <div class="col-md-3"><strong>Total Hutang:</strong> <?= formatRupiah($totalHutang) ?></div>
      <div class="col-md-3"><strong>Total Sisa Hutang:</strong> <?= formatRupiah($totalSisa) ?></div>
      <div class="col-md-3"><strong>Jumlah Lunas:</strong> <?= $countLunas ?> transaksi</div>
      <div class="col-md-3"><strong>Jumlah Belum Lunas:</strong> <?= $countBelum ?> transaksi</div>
    </div>
  </div>
</div>

<?php include "../../footer.php"; ?>

<!-- Live Search JS -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById("searchInput");
  const rows = document.querySelectorAll("#hutangTable tbody tr");

  searchInput.addEventListener("keyup", function() {
    const q = this.value.toLowerCase();
    rows.forEach(row => {
      const invoice  = row.querySelector(".invoice")?.textContent.toLowerCase() || "";
      const supplier = row.querySelector(".supplier")?.textContent.toLowerCase() || "";
      row.style.display = (invoice.includes(q) || supplier.includes(q)) ? "" : "none";
    });
  });
});
</script>
