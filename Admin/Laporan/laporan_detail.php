<?php
include "../header.php";
include "../../config.php";

$awal = $_GET['awal'] ?? date("Y-m-01");
$akhir = $_GET['akhir'] ?? date("Y-m-t");

$penjualan = $conn->query("SELECT p.*, c.nama_customer 
                           FROM penjualan p
                           LEFT JOIN customer c ON p.customer_id=c.id
                           WHERE p.tanggal BETWEEN '$awal' AND '$akhir'
                           ORDER BY p.tanggal");
$pembelian = $conn->query("SELECT p.*, s.nama_supplier 
                           FROM pembelian p
                           LEFT JOIN supplier s ON p.supplier_id=s.id
                           WHERE p.tanggal BETWEEN '$awal' AND '$akhir'
                           ORDER BY p.tanggal");
?>
<h2 class="fw-bold text-primary mb-4">📑 Laporan Detail</h2>

<form method="get" class="row mb-3">
  <div class="col-md-3">
    <input type="date" name="awal" value="<?=$awal;?>" class="form-control">
  </div>
  <div class="col-md-3">
    <input type="date" name="akhir" value="<?=$akhir;?>" class="form-control">
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-primary">Filter</button>
  </div>
</form>

<h5 class="fw-bold">📈 Penjualan</h5>
<div class="table-responsive mb-4">
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
      <tr>
        <th>No</th>
        <th>No Invoice</th>
        <th>Tanggal</th>
        <th>Customer</th>
        <th>Jenis</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php if($penjualan->num_rows>0): $no=1; while($row=$penjualan->fetch_assoc()): ?>
      <tr>
        <td><?=$no++;?></td>
        <td><?=$row['no_invoice'];?></td>
        <td><?=$row['tanggal'];?></td>
        <td><?=$row['nama_customer'];?></td>
        <td><?=$row['jenis'];?></td>
        <td>Rp <?=number_format($row['total'],0,",",".");?></td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="6" class="text-center">Tidak ada penjualan</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<h5 class="fw-bold">📉 Pembelian</h5>
<div class="table-responsive">
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
      <tr>
        <th>No</th>
        <th>No Invoice</th>
        <th>Tanggal</th>
        <th>Supplier</th>
        <th>Metode Bayar</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php if($pembelian->num_rows>0): $no=1; while($row=$pembelian->fetch_assoc()): ?>
      <tr>
        <td><?=$no++;?></td>
        <td><?=$row['no_invoice'];?></td>
        <td><?=$row['tanggal'];?></td>
        <td><?=$row['nama_supplier'];?></td>
        <td><?=$row['metode_bayar'];?></td>
        <td>Rp <?=number_format($row['total'],0,",",".");?></td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="6" class="text-center">Tidak ada pembelian</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<a href="export_laporan_gabungan_excel.php?awal=<?=$awal;?>&akhir=<?=$akhir;?>" class="btn btn-success">📊 Export Excel</a>
<a href="export_laporan_gabungan_pdf.php?awal=<?=$awal;?>&akhir=<?=$akhir;?>" class="btn btn-danger">📄 Export PDF</a>

<?php include "../footer.php"; ?>
