<?php
include "../header.php";
include "../../config.php";

if(!isset($_GET['id'])){ header("Location: piutang.php"); exit; }
$id = intval($_GET['id']);

$piutang = $conn->query("SELECT * FROM piutang WHERE id=$id")->fetch_assoc();
if(!$piutang){ die("Data tidak ditemukan"); }

if(isset($_POST['update'])){
    $jatuh_tempo = $_POST['jatuh_tempo'];
    $sisa = $_POST['sisa_piutang'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE piutang SET jatuh_tempo=?, sisa_piutang=?, status=? WHERE id=?");
    $stmt->bind_param("sdsi",$jatuh_tempo,$sisa,$status,$id);
    $stmt->execute();

    header("Location: piutang.php");
    exit;
}
?>
<h2 class="fw-bold text-primary mb-4">✏ Edit Piutang</h2>

<form method="post">
  <div class="mb-3">
    <label class="form-label">Jatuh Tempo</label>
    <input type="date" name="jatuh_tempo" class="form-control" value="<?=$piutang['jatuh_tempo'];?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Sisa Piutang</label>
    <input type="number" step="0.01" name="sisa_piutang" class="form-control" value="<?=$piutang['sisa_piutang'];?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-control">
      <option value="Lunas" <?=$piutang['status']=='Lunas'?'selected':'';?>>Lunas</option>
      <option value="Belum Lunas" <?=$piutang['status']=='Belum Lunas'?'selected':'';?>>Belum Lunas</option>
    </select>
  </div>
  <button type="submit" name="update" class="btn btn-primary">💾 Update</button>
  <a href="piutang.php" class="btn btn-secondary">⬅ Kembali</a>
</form>

<?php include "../footer.php"; ?>
