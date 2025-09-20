<?php
include "../../config.php";
include "../../header.php";

// Helper untuk output aman
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// ==========================
// Filter pencarian & kategori
// ==========================
$where = [];
$q = $_GET['q'] ?? '';
if (!empty($q)) {
    $qEsc = $conn->real_escape_string($q);
    $where[] = "(p.nama_produk LIKE '%$qEsc%' OR p.kode_barcode LIKE '%$qEsc%')";
}
if (!empty($_GET['kategori'])) {
    $kat = (int)$_GET['kategori'];
    $where[] = "p.kategori_id = $kat";
}
$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

// ==========================
// Order by default
// ==========================
$orderBy = "p.nama_produk ASC";

// ==========================
// Pagination
// ==========================
$limit  = 50;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$totalRow = $conn->query("SELECT COUNT(*) AS jml FROM produk p $whereSql")->fetch_assoc();
$total    = $totalRow['jml'] ?? 0;
$pages    = ceil($total / $limit);

$result = $conn->query("
    SELECT p.id, p.kode_barcode, p.nama_produk, k.nama_kategori AS kategori, 
           p.harga_beli, p.harga_jual, p.harga_jual_online, p.stok, p.gambar
    FROM produk p
    LEFT JOIN kategori_produk k ON p.kategori_id = k.id
    $whereSql
    ORDER BY $orderBy
    LIMIT $limit OFFSET $offset
");
?>

<div class="page-header">
  <h2 class="fw-bold text-gradient mb-0">
    <i class="fa fa-box"></i> Daftar Produk
  </h2>

  <form method="get" id="filterForm" class="d-flex align-center gap-2 mb-0">
    <!-- Search -->
    <input type="text" name="q" id="searchBox" 
           class="form-control"
           placeholder="Cari nama / barcode..."
           value="<?= e($q) ?>" style="width:200px">

    <!-- Kategori -->
    <select name="kategori" class="form-select" style="width:180px" onchange="this.form.submit()">
      <option value="">Semua Kategori</option>
      <?php
      $kat = $conn->query("SELECT * FROM kategori_produk ORDER BY nama_kategori ASC");
      while($k = $kat->fetch_assoc()):
        $selected = (isset($_GET['kategori']) && $_GET['kategori']==$k['id']) ? 'selected' : '';
        echo "<option value='{$k['id']}' $selected>".e($k['nama_kategori'])."</option>";
      endwhile;
      ?>
    </select>

    <!-- Tombol Aksi -->
    <a href="../Kategori/kategori.php" class="btn-add">
      <i class="fa fa-tags"></i> Kategori
    </a>
    <a href="tambah_produk.php" class="btn-add">
      <i class="fa fa-plus"></i> Tambah Produk
    </a>
  </form>
</div>



<!-- Grid Produk -->
<div id="produkGrid" class="produk-grid">
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): 
      $nama = e($row['nama_produk']);
      $kode = e($row['kode_barcode']);
      // Highlight jika ada pencarian
      if ($q) {
        $pattern = "/(" . preg_quote($q, '/') . ")/i";
        $nama = preg_replace($pattern, '<mark>$1</mark>', $nama);
        $kode = preg_replace($pattern, '<mark>$1</mark>', $kode);
      }
    ?>
      <div class="produk-card-modern">
        <!-- Gambar -->
        <?php if (!empty($row['gambar']) && file_exists("../../uploads/produk/" . $row['gambar'])): ?>
          <img src="../../uploads/produk/<?= e($row['gambar']) ?>" alt="<?= e($row['nama_produk']) ?>">
        <?php else: ?>
          <div class="no-image"><i class="fa fa-image"></i> Tidak ada gambar</div>
        <?php endif; ?>

        <!-- Detail Produk -->
        <div class="produk-info">
          <div class="kode"><?= $kode ?></div>
          <div class="nama"><?= $nama ?></div>
          <div class="kategori"><?= e($row['kategori']) ?></div>
          <div class="harga-pokok">Beli: Rp <?= number_format((float)$row['harga_beli'], 0, ',', '.') ?></div>
          <div class="harga-jual">Jual: Rp <?= number_format((float)$row['harga_jual'], 0, ',', '.') ?></div>
          <div class="harga-online">Online: Rp <?= number_format((float)$row['harga_jual_online'], 0, ',', '.') ?></div>
          <div class="stok">Stok: <?= (int)$row['stok'] ?></div>
        </div>

        <!-- Tombol Aksi -->
        <div class="aksi">
  <a href="edit_produk.php?id=<?= e($row['id']) ?>" class="btn-add">
    <i class="fa fa-edit"></i> Edit
  </a>
  <a href="hapus_produk.php?id=<?= e($row['id']) ?>" 
     onclick="return confirm('Yakin ingin menghapus produk ini?')" 
     class="btn-add">
    <i class="fa fa-trash"></i> Hapus
  </a>
</div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-center text-muted">Tidak ada produk ditemukan</p>
  <?php endif; ?>
</div>

<!-- Pagination -->
<?php if($pages > 1): ?>
<nav class="mt-3">
  <ul class="pagination">
    <?php for($i=1; $i<=$pages; $i++): 
      $active = ($i==$page) ? "active" : "";
      $qEnc   = urlencode($_GET['q'] ?? '');
      $katEnc = urlencode($_GET['kategori'] ?? '');
    ?>
      <li class="page-item <?= $active ?>">
        <a class="page-link" href="?q=<?= $qEnc ?>&kategori=<?= $katEnc ?>&page=<?= $i ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>

<?php include "../../footer.php"; ?>

<!-- Live Search Script -->
<script>
const searchBox = document.getElementById("searchBox");
searchBox.addEventListener("keyup", function() {
  const url = new URL(window.location.href);
  url.searchParams.set("q", searchBox.value);
  url.searchParams.set("page", 1); // reset ke page 1
  fetch(url)
    .then(res => res.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, "text/html");
      const newGrid = doc.querySelector("#produkGrid").innerHTML;
      document.querySelector("#produkGrid").innerHTML = newGrid;
    });
});
</script>
