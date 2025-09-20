<?php
include "../../config.php";
include "../header.php";

$sql = "SELECT s.*, p.no_invoice, c.nama_customer, u.nama_lengkap AS teknisi
        FROM service s
        JOIN penjualan p ON s.penjualan_id=p.id
        JOIN customer c ON p.customer_id=c.id
        LEFT JOIN users u ON s.teknisi_id=u.id
        ORDER BY s.created_at DESC";
$result = $conn->query($sql);
?>
<h2 class="fw-bold text-primary mb-4">🛠 Daftar Service</h2>

<div class="table-responsive">
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
      <tr>
        <th>No Service</th>
        <th>No Invoice</th>
        <th>Customer</th>
        <th>Teknisi</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows>0): while($row=$result->fetch_assoc()): ?>
      <tr>
        <td><?=$row['no_service'];?></td>
        <td><?=$row['no_invoice'];?></td>
        <td><?=$row['nama_customer'];?></td>
        <td><?=$row['teknisi'];?></td>
        <td>
          <span class="badge bg-info"><?=$row['status_admin'];?></span>
        </td>
        <td>
          <a href="detail_service.php?id=<?=$row['id'];?>" class="btn btn-info btn-sm">📜 Detail</a>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="6" class="text-center">Belum ada data</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include "../../footer.php"; ?>
