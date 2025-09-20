<?php
if (!isset($_SESSION)) { session_start(); }
include "../../config.php";

function e($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

if (!isset($_SESSION['cart_beli'])) $_SESSION['cart_beli'] = [];

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $produk_id = (int)$_POST['produk_id'];
    $qty       = (float)($_POST['qty'] ?? 1);

    if ($produk_id && $qty > 0) {
        $prod = $conn->query("SELECT nama_produk, harga_beli FROM produk WHERE id=$produk_id")->fetch_assoc();
        if ($prod) {
            $key = (string)$produk_id;
            if (!isset($_SESSION['cart_beli'][$key])) {
                $_SESSION['cart_beli'][$key] = [
                    'produk_id' => $produk_id,
                    'nama'      => $prod['nama_produk'],
                    'harga'     => $prod['harga_beli'],
                    'qty'       => $qty,
                ];
            } else {
                $_SESSION['cart_beli'][$key]['qty'] += $qty;
            }
        }
    }
}
elseif ($action === 'remove') {
    $rid = (string)$_POST['produk_id'];
    unset($_SESSION['cart_beli'][$rid]);
}
elseif ($action === 'update') {
    $id    = (string)$_POST['produk_id'];
    $qty   = (float)($_POST['qty'] ?? 1);
    $harga = (float)str_replace('.', '', $_POST['harga'] ?? 0);

    if (isset($_SESSION['cart_beli'][$id])) {
        $_SESSION['cart_beli'][$id]['qty']   = $qty;
        $_SESSION['cart_beli'][$id]['harga'] = $harga;
    }
}

// --- Render ulang keranjang ---
$total = 0;
?>
<table class="table table-modern align-middle mb-0">
  <thead>
    <tr>
      <th>No</th>
      <th>Produk</th>
      <th style="width:80px;">Qty</th>
      <th style="width:150px;">Harga</th>
      <th>Subtotal</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($_SESSION['cart_beli'])): $no=1; ?>
      <?php foreach ($_SESSION['cart_beli'] as $key=>$item): 
        $sub = $item['harga'] * $item['qty'];
        $total += $sub;
      ?>
        <tr>
          <td class="text-center"><?= $no++; ?></td>
          <td><?= e($item['nama']); ?></td>
          <td class="text-center">
            <input type="number" class="form-control form-control-sm inputQty" 
                   data-id="<?= $key; ?>" value="<?= $item['qty']; ?>" min="1">
          </td>
          <td class="text-right">
            <input type="text" class="form-control form-control-sm inputHarga" 
                   data-id="<?= $key; ?>" value="<?= number_format($item['harga'],0,',','.'); ?>">
          </td>
          <td class="text-right">Rp <?= number_format($sub,0,',','.'); ?></td>
          <td class="text-center">
            <button class="btn-aksi btn-aksi-danger btnRemove" data-id="<?= $key; ?>">
              <i class="fa fa-trash"></i>
            </button>
          </td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="4" class="text-right fw-bold">Total</td>
        <td class="text-right fw-bold">Rp <?= number_format($total,0,',','.'); ?></td>
        <td></td>
      </tr>
    <?php else: ?>
      <tr>
        <td colspan="6" class="text-center text-muted">Belum ada item.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
