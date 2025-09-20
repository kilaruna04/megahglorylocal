<?php
if (!isset($_SESSION)) { session_start(); }
include "../../../config.php";
include "../../../header.php";

$id = intval($_GET['id']);
$piutang = $conn->query("SELECT pi.*, p.no_invoice, c.nama_customer 
                         FROM piutang_toko pi
                         JOIN penjualan_toko p ON pi.penjualan_id=p.id
                         LEFT JOIN customer c ON p.customer_id=c.id
                         WHERE pi.id=$id")->fetch_assoc();

$bayar = $conn->query("SELECT * FROM piutang_toko_pembayaran WHERE piutang_id=$id ORDER BY tanggal ASC");
?>

<div class="container">
  <h4>Riwayat Pembayaran - <?=$piutang['no_invoice']?></h4>
  <p><b>Customer:</b> <?=$piutang['nama_customer']?></p>
  <p><b>Sisa Piutang:</b> Rp <?=number_format($piutang['sisa_piutang'],0,',','.')?> | <b>Status:</b> <?=$piutang['status']?></p>

  <table class="table table-bordered">
    <thead class="table-dark">
      <tr><th>No</th><th>Tanggal</th><th>Jumlah</th></tr>
    </thead>
    <tbody>
      <?php if($bayar->num_rows>0): $no=1; while($row=$bayar->fetch_assoc()): ?>
      <tr>
        <td><?=$no++?></td>
        <td><?=date("d-m-Y",strtotime($row['tanggal']))?></td>
        <td class="text-end"><?=number_format($row['jumlah'],0,',','.')?></td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="3" class="text-center">Belum ada pembayaran</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include "../../../footer.php"; ?>
