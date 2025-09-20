<?php
include "../header.php";
include "../../config.php";

$produk = $conn->query("SELECT * FROM produk ORDER BY nama_produk");
?>
<h2 class="fw-bold text-primary mb-4">📦 Laporan Stok Produk</h2>

<a href="../export_stok_excel.php" class="btn btn-success mb-3">📊 Export Excel</a>
<a href="../export_stok_pdf.php" class="btn btn-danger mb-3">📄 Export PDF</a>

<div class="table-responsive">
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
      <tr>
        <th>No</th>
        <th>Nama Produk</th>
        <th>Kategori</th>
        <th>Satuan</th>
        <th>Stok</th>
        <th>Harga Beli</th>
        <th>Harga Jual</th>
      </tr>
    </thead>
    <tbody>
      <?php if($produk->num_rows>0): $no=1; while($row=$produk->fetch_assoc()): ?>
      <tr class="<?=$row['stok'] <= 5 ? 'table-danger' : '';?>">
        <td><?=$no++;?></td>
        <td><?=$row['nama_produk'];?></td>
        <td><?=$row['kategori'];?></td>
        <td><?=$row['satuan'];?></td>
        <td><?=$row['stok'];?></td>
        <td>Rp <?=number_format($row['harga_beli'],0,",",".");?></td>
        <td>Rp <?=number_format($row['harga_jual'],0,",",".");?></td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="7" class="text-center">Tidak ada data produk</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include "../footer.php"; ?>
