<?php
if (!isset($_SESSION)) { session_start(); }
include "../../config.php";
include "../../header.php";
require_once __DIR__ . "/hutang_helpers.php";

if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$today = date('Y-m-d');
$err = "";

// --- Simpan pembelian ---
if (isset($_POST['simpan'])) {
    $no_invoice  = trim($_POST['no_invoice']);
    $supplier_id = (int)$_POST['supplier_id'];
    $tanggal     = $_POST['tanggal'];
    $metode      = $_POST['metode_bayar'];
    $keterangan  = $_POST['keterangan'];
    $nota_file   = null;

    // Upload Nota
    if (!empty($_FILES['nota_file']['name'])) {
        $uploadDir = "../../uploads/nota_pembelian/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $ext = pathinfo($_FILES['nota_file']['name'], PATHINFO_EXTENSION);
        $newName = "nota_" . date("YmdHis") . "." . strtolower($ext);
        $target = $uploadDir . $newName;

        if (move_uploaded_file($_FILES['nota_file']['tmp_name'], $target)) {
            $nota_file = $newName;
        }
    }

    if ($no_invoice === '') {
        $err = "No Invoice wajib diisi.";
    } elseif ($supplier_id <= 0) {
        $err = "Supplier wajib dipilih.";
    } elseif (empty($_SESSION['cart_beli'])) {
        $err = "Detail pembelian masih kosong.";
    } else {
        $total = 0;
        foreach ($_SESSION['cart_beli'] as $item) {
            $total += $item['harga'] * $item['qty'];
        }

        $stmt = $conn->prepare("INSERT INTO pembelian 
            (no_invoice, supplier_id, tanggal, metode_bayar, total, status_hutang, keterangan, nota_file) 
            VALUES (?,?,?,?,?,?,?,?)");
        $status_hutang = ($metode == "Kredit") ? "Belum Lunas" : "Lunas";
        $stmt->bind_param("sississs", $no_invoice, $supplier_id, $tanggal, $metode, $total, $status_hutang, $keterangan, $nota_file);
        if ($stmt->execute()) {
            $pembelian_id = $stmt->insert_id;

            $stmt2 = $conn->prepare("INSERT INTO pembelian_detail (pembelian_id, produk_id, qty, harga) VALUES (?,?,?,?)");
            foreach ($_SESSION['cart_beli'] as $item) {
                $pid   = (int)$item['produk_id'];
                $qty   = (int)$item['qty'];
                $harga = (float)$item['harga'];
                $stmt2->bind_param("iiid", $pembelian_id, $pid, $qty, $harga);
                $stmt2->execute();
            }

            create_or_update_hutang($conn, $pembelian_id, $tanggal, $metode, $total);

            unset($_SESSION['cart_beli']);
            header("Location: pembelian.php?msg=saved");
            exit;
        } else {
            $err = "Gagal menyimpan pembelian: " . $conn->error;
        }
    }
}
?>

<div class="page-header">
  <h3><i class="fa fa-plus"></i> Tambah Pembelian</h3>
</div>

<?php if ($err): ?>
  <div class="alert alert-danger"><?= e($err) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <!-- Data Utama -->
  <div class="card card-dark mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label>No Invoice</label>
          <input type="text" name="no_invoice" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label>Supplier</label>
          <select name="supplier_id" class="form-control" required>
            <option value="">-- Pilih Supplier --</option>
            <?php
            $sup = $conn->query("SELECT id, nama_supplier FROM supplier ORDER BY nama_supplier ASC");
            while ($s = $sup->fetch_assoc()):
            ?>
              <option value="<?= $s['id']; ?>"><?= e($s['nama_supplier']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label>Tanggal</label>
          <input type="date" name="tanggal" class="form-control" value="<?= $today; ?>" required>
        </div>
        <div class="col-md-2">
          <label>Metode Bayar</label>
          <select name="metode_bayar" class="form-control" required>
            <option value="Cash">Cash</option>
            <option value="Kredit">Kredit</option>
          </select>
        </div>
        <div class="col-md-4">
          <label>Upload Nota</label>
          <input type="file" name="nota_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        </div>
      </div>
      <div class="mt-3">
        <label>Keterangan</label>
        <textarea name="keterangan" class="form-control"></textarea>
      </div>
    </div>
  </div>

  <!-- Tambah Produk -->
  <div class="card card-dark mb-4 has-cari-produk">
    <div class="card-body position-relative">
      <h5 class="fw-bold mb-3"><i class="fa fa-box"></i> Tambah Item</h5>
      <input type="text" id="cariProduk" class="form-control" placeholder="Ketik nama / kode produk... lalu Enter">
      <div id="hasilCari"></div>
    </div>
  </div>

  <!-- Keranjang -->
  <div class="card card-dark mb-4">
    <div class="card-body">
      <h5 class="fw-bold mb-3"><i class="fa fa-shopping-cart"></i> Keranjang</h5>
      <div id="cartArea"></div>
    </div>
  </div>

  <div class="d-flex justify-content-between">
    <a href="pembelian.php" class="btn btn-secondary">
      <i class="fa fa-arrow-left"></i> Kembali
    </a>
    <button type="submit" name="simpan" class="btn btn-primary">
      <i class="fa fa-save"></i> Simpan Pembelian
    </button>
  </div>
</form>

<?php include "../../footer.php"; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const hasilCari = document.getElementById("hasilCari");
  const cartArea  = document.getElementById("cartArea");
  const inputCari = document.getElementById("cariProduk");

  refreshCart();

  // Pencarian produk live
  inputCari.addEventListener("keyup", function() {
    const q = this.value.trim();
    if (q.length < 2) { hasilCari.innerHTML = ""; return; }
    fetch("cari_produk.php?q=" + encodeURIComponent(q))
      .then(res => res.text())
      .then(html => { hasilCari.innerHTML = html; });
  });

  // Tekan Enter -> tambah produk pertama
  inputCari.addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
      e.preventDefault();
      const firstBtn = hasilCari.querySelector(".btnAdd");
      if (firstBtn) firstBtn.click();
    }
  });

  // Klik tombol Tambah
  hasilCari.addEventListener("click", function(e) {
    if (e.target.classList.contains("btnAdd")) {
      const id  = e.target.dataset.id;
      const qty = e.target.dataset.qty || 1;
      addToCart(id, qty);
      inputCari.value = "";
      hasilCari.innerHTML = "";
    }
  });

  // Klik baris tabel
  hasilCari.addEventListener("click", function(e) {
    const row = e.target.closest(".rowProduk");
    if (row && !e.target.classList.contains("btnAdd")) {
      const id = row.dataset.id;
      addToCart(id, 1);
      inputCari.value = "";
      hasilCari.innerHTML = "";
    }
  });

  // Hapus item
  cartArea.addEventListener("click", function(e) {
    if (e.target.closest(".btnRemove")) {
      const id = e.target.closest(".btnRemove").dataset.id;
      removeFromCart(id);
    }
  });

  // Update Qty
  cartArea.addEventListener("change", function(e) {
    if (e.target.classList.contains("inputQty")) {
      const id = e.target.dataset.id;
      const qty = e.target.value;
      const harga = e.target.closest("tr").querySelector(".inputHarga").value;
      updateCart(id, qty, harga);
    }
  });

  // Format & Update Harga
  cartArea.addEventListener("input", function(e) {
    if (e.target.classList.contains("inputHarga")) {
      let val = e.target.value.replace(/\./g, "");
      e.target.value = val ? parseInt(val).toLocaleString("id-ID") : "";
    }
  });
  cartArea.addEventListener("change", function(e) {
    if (e.target.classList.contains("inputHarga")) {
      const id = e.target.dataset.id;
      const harga = e.target.value;
      const qty = e.target.closest("tr").querySelector(".inputQty").value;
      updateCart(id, qty, harga);
    }
  });

  function addToCart(id, qty) {
    fetch("cart_api.php", {
      method: "POST",
      headers: {"Content-Type":"application/x-www-form-urlencoded"},
      body: "action=add&produk_id=" + id + "&qty=" + qty
    }).then(res => res.text()).then(html => { cartArea.innerHTML = html; });
  }
  function removeFromCart(id) {
    fetch("cart_api.php", {
      method: "POST",
      headers: {"Content-Type":"application/x-www-form-urlencoded"},
      body: "action=remove&produk_id=" + id
    }).then(res => res.text()).then(html => { cartArea.innerHTML = html; });
  }
  function updateCart(id, qty, harga) {
    fetch("cart_api.php", {
      method: "POST",
      headers: {"Content-Type":"application/x-www-form-urlencoded"},
      body: "action=update&produk_id=" + id + "&qty=" + qty + "&harga=" + encodeURIComponent(harga)
    }).then(res => res.text()).then(html => { cartArea.innerHTML = html; });
  }
  function refreshCart() {
    fetch("cart_api.php", {
      method: "POST",
      headers: {"Content-Type":"application/x-www-form-urlencoded"},
      body: "action=view"
    }).then(res => res.text()).then(html => { cartArea.innerHTML = html; });
  }
});
</script>
