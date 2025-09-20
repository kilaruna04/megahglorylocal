<?php
// --- SESSION ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- CEK LOGIN ---
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// --- VARIABEL AKTIF ---
$current_page = basename($_SERVER['PHP_SELF'] ?? '');
$current_uri  = $_SERVER['REQUEST_URI'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Megah Glory Admin</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Theme CSS -->
  <link href="/assets/css/theme.css" rel="stylesheet">

  <!-- Script Sidebar -->
  <script>
    function toggleSubmenu(el) {
      const submenu = el.nextElementSibling;
      submenu.style.display = (submenu.style.display === "block") ? "none" : "block";
    }
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('collapsed');
    }
  </script>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <h4><i class="fa fa-gear"></i> Admin Panel</h4>
    <a href="/Admin/index.php" class="<?= ($current_page === 'index.php') ? 'active' : '' ?>">
      <i class="fa fa-home"></i> Dashboard
    </a>
    <a href="/Admin/Produk/produk.php" class="<?= ($current_page === 'produk.php') ? 'active' : '' ?>">
      <i class="fa fa-box"></i> Produk
    </a>
    <a href="/Admin/Customer/customer.php" class="<?= ($current_page === 'customer.php') ? 'active' : '' ?>">
      <i class="fa fa-users"></i> Customer
    </a>
    <a href="/Admin/Supplier/supplier.php" class="<?= ($current_page === 'supplier.php') ? 'active' : '' ?>">
      <i class="fa fa-truck"></i> Supplier
    </a>
    <a href="/Admin/Pembelian/pembelian.php" class="<?= ($current_page === 'pembelian.php') ? 'active' : '' ?>">
      <i class="fa fa-cart-shopping"></i> Pembelian
    </a>

    <!-- Dropdown Penjualan -->
    <?php $penjualan_active = str_contains($current_uri, '/Penjualan/'); ?>
    <div class="dropdown-sidebar">
      <a href="javascript:void(0);" class="dropdown-toggle <?= $penjualan_active ? 'active' : '' ?>" onclick="toggleSubmenu(this)">
        <i class="fa fa-cash-register"></i> <span>Penjualan</span>
        <i class="fa fa-angle-down ms-auto"></i>
      </a>
      <div class="submenu" style="display: <?= $penjualan_active ? 'block' : 'none' ?>;">
        <a href="/Admin/Penjualan/toko/index.php" class="<?= str_contains($current_uri, '/Penjualan/toko/') ? 'active' : '' ?>">
          <i class="fa fa-store"></i> Penjualan Toko
        </a>
        <a href="/Admin/Penjualan/instansi/index.php" class="<?= str_contains($current_uri, '/Penjualan/instansi/') ? 'active' : '' ?>">
          <i class="fa fa-building"></i> Penjualan Instansi
        </a>
        <a href="/Admin/Penjualan/service/index.php" class="<?= str_contains($current_uri, '/Penjualan/service/') ? 'active' : '' ?>">
          <i class="fa fa-screwdriver-wrench"></i> Penjualan Service
        </a>
      </div>
    </div>

    <!-- Dropdown Piutang -->
    <?php $piutang_active = str_contains($current_uri, '/Piutang/'); ?>
    <div class="dropdown-sidebar">
      <a href="javascript:void(0);" class="dropdown-toggle <?= $piutang_active ? 'active' : '' ?>" onclick="toggleSubmenu(this)">
        <i class="fa fa-hand-holding-dollar"></i> <span>Piutang</span>
        <i class="fa fa-angle-down ms-auto"></i>
      </a>
      <div class="submenu" style="display: <?= $piutang_active ? 'block' : 'none' ?>;">
        <a href="/Admin/Piutang/instansi/index.php" class="<?= str_contains($current_uri, '/Piutang/instansi/') ? 'active' : '' ?>">
          <i class="fa fa-building"></i> Piutang Instansi
        </a>
        <a href="/Admin/Piutang/toko/index.php" class="<?= str_contains($current_uri, '/Piutang/toko/') ? 'active' : '' ?>">
          <i class="fa fa-store"></i> Piutang Toko
        </a>
        <a href="/Admin/Piutang/service/index.php" class="<?= str_contains($current_uri, '/Piutang/service/') ? 'active' : '' ?>">
          <i class="fa fa-screwdriver-wrench"></i> Piutang Service
        </a>
      </div>
    </div>

    <a href="/Admin/Hutang/hutang.php" class="<?= ($current_page === 'hutang.php') ? 'active' : '' ?>">
      <i class="fa fa-wallet"></i> Hutang
    </a>
    <a href="/Admin/Service/service.php" class="<?= ($current_page === 'service.php') ? 'active' : '' ?>">
      <i class="fa fa-screwdriver-wrench"></i> Service
    </a>
    <a href="/Admin/Laporan/laporan.php" class="<?= ($current_page === 'laporan.php') ? 'active' : '' ?>">
      <i class="fa fa-chart-line"></i> Laporan
    </a>
    <a href="/logout.php">
      <i class="fa fa-sign-out-alt"></i> Logout
    </a>
  </div>

  <!-- Wrapper -->
  <div class="wrapper">
    <!-- Content -->
    <div class="content">
      <!-- Topbar -->
      <div class="topbar">
        <h5>📊 Dashboard Admin</h5>
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
      </div>
