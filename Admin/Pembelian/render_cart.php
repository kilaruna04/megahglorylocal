<?php
if (!function_exists('e')) {
  function e($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }
}

function render_cart() {
  $cart = $_SESSION['cart_beli'] ?? [];
  if (empty($cart)) {
    return "<p class='text-muted'>Keranjang kosong.</p>";
  }

  $html = "<div class='table-responsive'><table class='table table-bordered align-middle'>";
  $html .= "<thead>
              <tr>
                <th>Produk</th>
                <th width='80'>Qty</th>
                <th width='120'>Harga</th>
                <th width='200'>Serial Number</th>
                <th width='120'>Subtotal</th>
                <th width='50'>#</th>
              </tr>
            </thead><tbody>";

  $total = 0;
  foreach ($cart as $item) {
    $subtotal = $item['qty'] * $item['harga'];
    $total += $subtotal;
    $html .= "<tr>
      <td>".e($item['nama'])."</td>
      <td>
        <input type='number' class='form-control inputQty' 
               data-id='{$item['produk_id']}' 
               value='{$item['qty']}'>
      </td>
      <td>
        <input type='text' class='form-control inputHarga' 
               data-id='{$item['produk_id']}' 
               value='".number_format($item['harga'],0,",",".")."'>
      </td>
      <td>
        <input type='text' class='form-control inputSN' 
               data-id='{$item['produk_id']}' 
               value='".e($item['sn'] ?? '')."' 
               placeholder='Serial Number'>
      </td>
      <td>".number_format($subtotal,0,",",".")."</td>
      <td>
        <button type='button' class='btn btn-danger btn-sm btnRemove' 
                data-id='{$item['produk_id']}'>
          <i class='fa fa-trash'></i>
        </button>
      </td>
    </tr>";
  }

  $html .= "<tr>
    <th colspan='4' class='text-end'>Total</th>
    <th colspan='2'>".number_format($total,0,",",".")."</th>
  </tr>";

  $html .= "</tbody></table></div>";
  return $html;
}
