<?php
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php?role=admin");
    exit;
}

include "../../../config.php";
include "../../../header.php";

// ==== Generate Invoice ====
function generateInvoice($conn) {
    $sql = "SELECT no_invoice FROM penjualan_instansi ORDER BY id DESC LIMIT 1";
    $res = $conn->query($sql);
    $last = 0;
    if ($res && $row = $res->fetch_assoc()) {
        $last = (int) substr($row['no_invoice'], 4);
    }
    return "MGI-" . str_pad($last + 1, 5, "0", STR_PAD_LEFT);
}
$auto_invoice = generateInvoice($conn);

// ==== Ambil data customer & produk ====
$customers = $conn->query("SELECT id, nama_customer FROM customer WHERE jenis='Instansi' ORDER BY nama_customer");
$produkList = $conn->query("SELECT id, nama_produk, harga_jual FROM produk ORDER BY nama_produk");

// ==== Proses Simpan ====
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_invoice     = $_POST['no_invoice'];
    $tanggal        = $_POST['tanggal'];
    $customer_id    = $_POST['customer_id'];
    $jenis_penjualan= $_POST['jenis_penjualan'];
    $keterangan     = $_POST['keterangan'];
    $total          = str_replace('.', '', $_POST['total']);
    $dpp            = str_replace('.', '', $_POST['dpp']);
    $ppn            = str_replace('.', '', $_POST['ppn']);
    $pph            = str_replace('.', '', $_POST['pph']);
    $biaya_adm      = str_replace('.', '', $_POST['biaya_adm']);
    $total_masuk    = str_replace('.', '', $_POST['total_masuk']);
    $metode_bayar   = $_POST['metode_bayar'];

    $status_instansi = ($metode_bayar == "Cash") ? "Lunas" : "Belum Lunas";

    // Simpan header
    $stmt = $conn->prepare("INSERT INTO penjualan_instansi 
        (no_invoice, tanggal, customer_id, jenis_penjualan, keterangan, total, dpp, ppn, pph, biaya_adm, total_masuk, status) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssissdddddds",
        $no_invoice, $tanggal, $customer_id, $jenis_penjualan, $keterangan,
        $total, $dpp, $ppn, $pph, $biaya_adm, $total_masuk, $status_instansi
    );
    $stmt->execute() or die("Error header: ".$stmt->error);
    $penjualan_id = $stmt->insert_id;
    $stmt->close();

    // Simpan detail
    foreach ($_POST['jenis_item'] as $i => $jenis_item) {
        $qty        = $_POST['qty'][$i];
        $harga      = str_replace('.', '', $_POST['harga'][$i]);
        $subtotal   = str_replace('.', '', $_POST['subtotal'][$i]);
        $ket_item   = $_POST['ket_item'][$i];
        $produk_id  = ($_POST['produk_id'][$i] != "") ? $_POST['produk_id'][$i] : null;
        $nama_produk= $_POST['produk'][$i];
        $sn_id      = ($_POST['sn_id'][$i] != "") ? $_POST['sn_id'][$i] : null;

        if ($jenis_item == "Real" && $produk_id) {
            $q = $conn->prepare("SELECT nama_produk FROM produk WHERE id=?");
            $q->bind_param("i", $produk_id);
            $q->execute();
            $res = $q->get_result();
            if ($rowP = $res->fetch_assoc()) {
                $nama_produk = $rowP['nama_produk'];
            }
            $q->close();
        }

        $stmt = $conn->prepare("INSERT INTO penjualan_instansi_detail 
            (penjualan_id, produk_id, sn_id, nama_produk, qty, harga, subtotal, jenis_item, keterangan) 
            VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("iiisdddss",
            $penjualan_id, $produk_id, $sn_id, $nama_produk,
            $qty, $harga, $subtotal, $jenis_item, $ket_item
        );
        $stmt->execute() or die("Error detail: ".$stmt->error);
        $stmt->close();

        if ($sn_id) {
            $conn->query("UPDATE produk_sn SET status='terjual' WHERE id=$sn_id");
        }
    }

    // Simpan piutang jika kredit
    if ($metode_bayar == "Kredit") {
        $jatuh_tempo = date('Y-m-d', strtotime($tanggal.' +30 days'));
        $status_piutang = "Belum Lunas";
        $stmtP = $conn->prepare("INSERT INTO piutang_instansi 
            (penjualan_id, no_invoice, total_piutang, sisa_piutang, jatuh_tempo, status) 
            VALUES (?,?,?,?,?,?)");
        $stmtP->bind_param("isddss", $penjualan_id, $no_invoice, $total, $total, $jatuh_tempo, $status_piutang);
        $stmtP->execute();
        $stmtP->close();
    }

    echo "<script>alert('Penjualan berhasil disimpan');window.location='index.php';</script>";
    exit;
}
?>

<div class="container-fluid">
  <h4><i class="fa fa-building"></i> Tambah Penjualan Instansi</h4>
  <form method="post">
    <!-- Header -->
    <div class="card-dark p-3 mb-3">
      <div class="row g-3">
        <div class="col-md-3">
          <label>Tanggal</label>
          <input type="date" name="tanggal" class="form-control" value="<?=date('Y-m-d')?>" required>
        </div>
        <div class="col-md-3">
          <label>No Invoice</label>
          <input type="text" name="no_invoice" class="form-control" value="<?=$auto_invoice?>" readonly>
        </div>
        <div class="col-md-3">
          <label>Customer</label>
          <select name="customer_id" class="form-control" required>
            <option value="">-- Pilih Customer --</option>
            <?php while($c=$customers->fetch_assoc()): ?>
              <option value="<?=$c['id']?>"><?=htmlspecialchars($c['nama_customer'])?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label>Metode Bayar</label>
          <select name="metode_bayar" class="form-control" required>
            <option value="Cash">Cash</option>
            <option value="Kredit">Kredit</option>
          </select>
        </div>
        <div class="col-md-3">
          <label>Jenis Penjualan</label>
          <select name="jenis_penjualan" class="form-control" required>
            <option value="">-- Pilih Jenis --</option>
            <option value="SPJ">SPJ (Manual)</option>
            <option value="Real">Real (Produk DB)</option>
            <option value="SPJ+Real">SPJ + Real</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Produk -->
    <div class="card-dark p-3 mb-3">
      <h6><i class="fa fa-cart-shopping"></i> Produk dalam Keranjang</h6>
      <div class="mb-3">
        <label>Keterangan</label>
        <input type="text" name="keterangan" class="form-control">
      </div>

      <table class="table table-bordered" id="produkTable">
        <thead class="table-primary text-center">
          <tr>
            <th>Produk SPJ / Real</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Sub Total</th>
            <th>Jenis Item</th>
            <th>Serial Number</th>
            <th>Keterangan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <input type="text" name="produk[]" class="form-control input-spj" placeholder="Nama Produk (SPJ)">
              <select name="produk_id[]" class="form-control produk-select input-real mt-1" style="display:none;">
                <option value="">-- Pilih Produk (Real) --</option>
                <?php $produkList->data_seek(0); while($p=$produkList->fetch_assoc()): ?>
                  <option value="<?=$p['id']?>" data-harga="<?=$p['harga_jual']?>"><?=$p['nama_produk']?></option>
                <?php endwhile; ?>
              </select>
            </td>
            <td><input type="number" name="qty[]" class="form-control qty" value="1"></td>
            <td><input type="text" name="harga[]" class="form-control harga" value="0"></td>
            <td><input type="text" name="subtotal[]" class="form-control subtotal" value="0" readonly></td>
            <td>
              <select name="jenis_item[]" class="form-control jenis-item">
                <option value="SPJ" selected>SPJ</option>
                <option value="Real">Real</option>
              </select>
            </td>
            <td>
              <select name="sn_id[]" class="form-control sn-select">
                <option value="">-- Tidak ada SN --</option>
              </select>
            </td>
            <td><input type="text" name="ket_item[]" class="form-control"></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
          </tr>
        </tbody>
      </table>
      <button type="button" class="btn btn-success" id="addRow"><i class="fa fa-plus"></i> Tambah Produk</button>
    </div>

    <!-- Ringkasan -->
    <div class="card-dark p-3 mb-3">
      <div class="row g-3">
        <div class="col-md-3"><label>Total</label><input type="text" name="total" id="total" class="form-control" value="0" readonly></div>
        <div class="col-md-3"><label>DPP</label><input type="text" name="dpp" id="dpp" class="form-control" value="0"></div>
        <div class="col-md-3"><label>PPN</label><input type="text" name="ppn" id="ppn" class="form-control" value="0"></div>
        <div class="col-md-3"><label>PPh</label><input type="text" name="pph" id="pph" class="form-control" value="0"></div>
        <div class="col-md-3"><label>Biaya ADM</label><input type="text" name="biaya_adm" id="biaya_adm" class="form-control" value="0"></div>
        <div class="col-md-3"><label>Total Masuk</label><input type="text" name="total_masuk" id="total_masuk" class="form-control" value="0" readonly></div>
      </div>
    </div>

    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
  </form>
</div>

<script>
function formatRupiah(angka){angka=Math.round(angka)||0;return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g,".");}
function updateTotals(){
  let total=0;
  document.querySelectorAll("#produkTable tbody tr").forEach(function(row){
    let qty=parseFloat(row.querySelector(".qty").value)||0;
    let harga=parseFloat((row.querySelector(".harga").value||"0").replace(/\./g,''))||0;
    let subtotal=qty*harga;
    row.querySelector(".subtotal").value=formatRupiah(subtotal);
    total+=subtotal;
  });
  document.getElementById("total").value=formatRupiah(total);
}
document.addEventListener("input",function(e){
  if(e.target.classList.contains("qty")||e.target.classList.contains("harga")||e.target.id=="biaya_adm"){updateTotals();}
});
document.getElementById("addRow").addEventListener("click",function(){
  let row=document.querySelector("#produkTable tbody tr").cloneNode(true);
  row.querySelectorAll("input").forEach(i=>i.value=i.classList.contains("qty")?1:"");
  row.querySelector(".produk-select").selectedIndex=0;
  row.querySelector(".input-real").style.display="none";
  row.querySelector(".input-spj").style.display="block";
  row.querySelector(".jenis-item").value="SPJ";
  row.querySelector(".sn-select").innerHTML='<option value="">-- Tidak ada SN --</option>';
  document.querySelector("#produkTable tbody").appendChild(row);
});
document.addEventListener("click",function(e){
  if(e.target.classList.contains("remove-row")||e.target.closest(".remove-row")){
    if(document.querySelectorAll("#produkTable tbody tr").length>1){e.target.closest("tr").remove();updateTotals();}
  }
});
document.addEventListener("change",function(e){
  if(e.target.classList.contains("jenis-item")){
    let row=e.target.closest("tr");
    if(e.target.value==="SPJ"){
      row.querySelector(".input-spj").style.display="block";
      row.querySelector(".input-real").style.display="none";
      row.querySelector(".sn-select").innerHTML='<option value="">-- Tidak ada SN --</option>';
      row.querySelector(".qty").readOnly=false;
    }else{
      row.querySelector(".input-spj").style.display="none";
      row.querySelector(".input-real").style.display="block";
      row.querySelector(".qty").value=1;
      row.querySelector(".qty").readOnly=true;
    }
  }
  if(e.target.classList.contains("produk-select")){
    let row=e.target.closest("tr");
    let produkId=e.target.value;
    let harga=e.target.options[e.target.selectedIndex].getAttribute("data-harga");
    row.querySelector(".harga").value=formatRupiah(harga||0);
    updateTotals();
    if(produkId){
      fetch("sn_loader.php?id="+produkId).then(res=>res.json()).then(data=>{
        let opts='<option value="">-- Pilih SN --</option>';
        if(data.sn_list.length===0){opts='<option value="">-- Tidak ada SN --</option>';}
        else{data.sn_list.forEach(sn=>{opts+=`<option value="${sn.id}">${sn.sn}</option>`;});}
        row.querySelector(".sn-select").innerHTML=opts;
      });
    }
  }
});
updateTotals();
</script>

<?php include "../../../footer.php"; ?>
