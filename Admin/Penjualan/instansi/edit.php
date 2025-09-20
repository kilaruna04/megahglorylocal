<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php?role=admin");
    exit;
}

include "../../../config.php";
include "../../../header.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) die("ID tidak ditemukan");
$id = intval($_GET['id']);

// --- Ambil data header ---
$stmt = $conn->prepare("SELECT * FROM penjualan_instansi WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) die("Data tidak ditemukan");

// --- Ambil detail produk ---
$details = $conn->query("SELECT * FROM penjualan_instansi_detail WHERE penjualan_id=$id");

// --- Ambil customer instansi ---
$customers = $conn->query("SELECT id,nama_customer FROM customer WHERE jenis='Instansi' ORDER BY nama_customer");

// --- Update data ---
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $tanggal        = $_POST['tanggal'];
    $no_invoice     = $_POST['no_invoice'];
    $customer_id    = $_POST['customer_id'];
    $jenis_penjualan= $_POST['jenis_penjualan'];
    $keterangan     = $_POST['keterangan'];
    $total          = str_replace('.','',$_POST['total']);
    $dpp            = str_replace('.','',$_POST['dpp']);
    $ppn            = str_replace('.','',$_POST['ppn']);
    $pph            = str_replace('.','',$_POST['pph']);
    $biaya_adm      = str_replace('.','',$_POST['biaya_adm']);
    $total_masuk    = str_replace('.','',$_POST['total_masuk']);
    $metode_bayar   = $_POST['metode_bayar'];

    $status_instansi = ($metode_bayar=="Cash")?"Lunas":"Belum Lunas";

    // Update header
    $stmt = $conn->prepare("UPDATE penjualan_instansi SET 
        tanggal=?, no_invoice=?, customer_id=?, jenis_penjualan=?, keterangan=?, 
        total=?, dpp=?, ppn=?, pph=?, biaya_adm=?, total_masuk=?, status=? 
        WHERE id=?");
    $stmt->bind_param("ssissddddddsi", 
        $tanggal, $no_invoice, $customer_id, $jenis_penjualan, $keterangan,
        $total, $dpp, $ppn, $pph, $biaya_adm, $total_masuk, $status_instansi, $id
    );
    if (!$stmt->execute()) die("Error update header: ".$stmt->error);
    $stmt->close();

    // Hapus detail lama
    $conn->query("DELETE FROM penjualan_instansi_detail WHERE penjualan_id=$id");

    // Insert detail baru
    foreach ($_POST['produk'] as $i => $nama_produk) {
        $qty       = $_POST['qty'][$i];
        $harga     = str_replace('.','',$_POST['harga'][$i]);
        $subtotal  = str_replace('.','',$_POST['subtotal'][$i]);
        $jenis_item= $_POST['jenis_item'][$i];
        $produk_id = ($_POST['produk_id'][$i]!="")?$_POST['produk_id'][$i]:null;

        $stmt = $conn->prepare("INSERT INTO penjualan_instansi_detail 
            (penjualan_id, produk_id, nama_produk, qty, harga, subtotal, jenis_item) 
            VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("iisddds",$id,$produk_id,$nama_produk,$qty,$harga,$subtotal,$jenis_item);
        if (!$stmt->execute()) die("Error simpan detail: ".$stmt->error);
        $stmt->close();
    }

    // Update piutang
    if ($metode_bayar=="Kredit") {
        $jatuh_tempo = date('Y-m-d',strtotime($tanggal.' +30 days'));
        $cek = $conn->query("SELECT id FROM piutang_instansi WHERE penjualan_id=$id");
        if ($cek->num_rows>0) {
            $conn->query("UPDATE piutang_instansi SET jatuh_tempo='$jatuh_tempo', sisa_piutang='$total', status='Belum Lunas' WHERE penjualan_id=$id");
        } else {
            $conn->query("INSERT INTO piutang_instansi (penjualan_id,jatuh_tempo,sisa_piutang,status) VALUES ($id,'$jatuh_tempo',$total,'Belum Lunas')");
        }
    } else {
        $conn->query("DELETE FROM piutang_instansi WHERE penjualan_id=$id");
    }

    echo "<script>alert('Data berhasil diperbarui');window.location='index.php';</script>";
    exit;
}
?>

<div class="container-fluid">
  <h4><i class="fa fa-edit"></i> Edit Penjualan Instansi</h4>
  <form method="post">
    <div class="card p-3 mb-3">
      <div class="row g-3">
        <div class="col-md-3">
          <label>Tanggal</label>
          <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label>No Invoice</label>
          <input type="text" name="no_invoice" value="<?= $data['no_invoice'] ?>" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label>Customer</label>
          <select name="customer_id" class="form-control" required>
            <?php while($c=$customers->fetch_assoc()): ?>
              <option value="<?= $c['id'] ?>" <?= $c['id']==$data['customer_id']?'selected':'' ?>>
                <?= htmlspecialchars($c['nama_customer']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label>Metode Bayar</label>
          <select name="metode_bayar" class="form-control">
            <option value="Cash" <?= ($data['status']=='Lunas')?'selected':'' ?>>Cash</option>
            <option value="Kredit" <?= ($data['status']=='Belum Lunas')?'selected':'' ?>>Kredit</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Ringkasan -->
    <div class="card p-3 mb-3">
      <div class="row g-3">
        <div class="col-md-3"><label>Total</label><input type="text" id="total" name="total" class="form-control" value="<?= $data['total'] ?>"></div>
        <div class="col-md-3"><label>DPP</label><input type="text" id="dpp" name="dpp" class="form-control" value="<?= $data['dpp'] ?>"></div>
        <div class="col-md-3"><label>PPN</label><input type="text" id="ppn" name="ppn" class="form-control" value="<?= $data['ppn'] ?>"></div>
        <div class="col-md-3"><label>PPh</label><input type="text" id="pph" name="pph" class="form-control" value="<?= $data['pph'] ?>"></div>
        <div class="col-md-3"><label>Biaya ADM</label><input type="text" id="biaya_adm" name="biaya_adm" class="form-control" value="<?= $data['biaya_adm'] ?>"></div>
        <div class="col-md-3"><label>Total Masuk</label><input type="text" id="total_masuk" name="total_masuk" class="form-control" value="<?= $data['total_masuk'] ?>"></div>
      </div>
    </div>

    <!-- Detail Produk -->
    <div class="card p-3 mb-3">
      <h6>Detail Produk</h6>
      <table class="table table-bordered" id="produkTable">
        <thead class="table-primary text-center">
          <tr>
            <th>Nama Produk</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Subtotal</th>
            <th>Jenis Item</th>
          </tr>
        </thead>
        <tbody>
          <?php while($d=$details->fetch_assoc()): ?>
          <tr>
            <td><input type="text" name="produk[]" value="<?= $d['nama_produk'] ?>" class="form-control"></td>
            <td><input type="number" name="qty[]" value="<?= $d['qty'] ?>" class="form-control qty"></td>
            <td><input type="text" name="harga[]" value="<?= $d['harga'] ?>" class="form-control harga"></td>
            <td><input type="text" name="subtotal[]" value="<?= $d['subtotal'] ?>" class="form-control subtotal" readonly></td>
            <td>
              <select name="jenis_item[]" class="form-control">
                <option value="SPJ" <?= $d['jenis_item']=='SPJ'?'selected':'' ?>>SPJ</option>
                <option value="Real" <?= $d['jenis_item']=='Real'?'selected':'' ?>>Real</option>
              </select>
              <input type="hidden" name="produk_id[]" value="<?= $d['produk_id'] ?>">
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan Perubahan</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
  </form>
</div>

<script>
function formatRupiah(angka) {
  if (!angka) return "0";
  return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function updateTotals() {
  let total = 0;
  document.querySelectorAll("#produkTable tbody tr").forEach(function(row){
    let qty = parseFloat(row.querySelector(".qty").value || "0");
    let harga = parseFloat((row.querySelector(".harga").value || "0").replace(/\./g,'')) || 0;
    let subtotal = qty * harga;
    row.querySelector(".subtotal").value = formatRupiah(Math.round(subtotal));
    total += subtotal;
  });

  let dpp = total * 100 / 111;
  let ppn = total - dpp;
  let pph = dpp * 0.5 / 100;
  let adm = parseFloat((document.getElementById("biaya_adm").value || "0").replace(/\./g,'')) || 0;
  let total_masuk = total - ppn - pph - adm;

  document.getElementById("total").value = formatRupiah(Math.round(total));
  document.getElementById("dpp").value = formatRupiah(Math.round(dpp));
  document.getElementById("ppn").value = formatRupiah(Math.round(ppn));
  document.getElementById("pph").value = formatRupiah(Math.round(pph));
  document.getElementById("total_masuk").value = formatRupiah(Math.round(total_masuk));
}

document.addEventListener("input", function(e){
  if (e.target.classList.contains("qty") || e.target.classList.contains("harga") || e.target.id=="biaya_adm") {
    updateTotals();
  }
});

updateTotals();
</script>

<?php include "../../../footer.php"; ?>
