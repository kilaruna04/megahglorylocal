-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 18, 2025 at 11:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `megw6638_servicedesk`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `nama_customer` varchar(100) NOT NULL,
  `jenis` enum('Toko','Instansi') DEFAULT 'Toko',
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `nama_customer`, `jenis`, `alamat`, `no_telp`, `email`) VALUES
(2, 'Megah Glory', 'Toko', 'Jl. Raya Tim. No.51, Cipakat, Kec. Singaparna, Kabupaten Tasikmalaya', '085283030048', 'admin@megahglory.com'),
(4, 'SMAN 1 Manonjaya', 'Instansi', 'manonjaya', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `hutang`
--

CREATE TABLE `hutang` (
  `id` int(11) NOT NULL,
  `pembelian_id` int(11) DEFAULT NULL,
  `jatuh_tempo` date DEFAULT NULL,
  `sisa_hutang` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('Belum Lunas','Lunas') NOT NULL DEFAULT 'Belum Lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hutang`
--

INSERT INTO `hutang` (`id`, `pembelian_id`, `jatuh_tempo`, `sisa_hutang`, `status`) VALUES
(7, 15, '2025-10-11', 0.00, 'Lunas'),
(8, 16, '2025-10-11', 0.00, 'Lunas');

-- --------------------------------------------------------

--
-- Table structure for table `hutang_pembayaran`
--

CREATE TABLE `hutang_pembayaran` (
  `id` int(11) NOT NULL,
  `hutang_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hutang_pembayaran`
--

INSERT INTO `hutang_pembayaran` (`id`, `hutang_id`, `tanggal`, `jumlah`, `created_at`) VALUES
(3, 7, '2025-09-11', 6000000.00, '2025-09-11 14:08:21'),
(4, 8, '2025-09-11', 10500.00, '2025-09-11 14:10:39');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_produk`
--

CREATE TABLE `kategori_produk` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kategori_produk`
--

INSERT INTO `kategori_produk` (`id`, `nama_kategori`, `gambar`, `deskripsi`) VALUES
(1, 'Laptop', NULL, NULL),
(3, 'Komputer', NULL, NULL),
(4, 'Projektor', NULL, NULL),
(6, 'Sparepart Printer', NULL, NULL),
(7, 'Sparepart Laptop', NULL, NULL),
(8, 'Sparepart Komputer', NULL, NULL),
(10, 'Printer', NULL, NULL),
(11, 'Jasa Service', '', NULL),
(12, 'Peralatan Jaringan', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `id` int(11) NOT NULL,
  `no_invoice` varchar(50) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `metode_bayar` enum('Cash','Kredit') DEFAULT 'Cash',
  `total` decimal(15,2) DEFAULT 0.00,
  `status_hutang` enum('Lunas','Belum Lunas') DEFAULT 'Lunas',
  `keterangan` text DEFAULT NULL,
  `nota_file` varchar(255) DEFAULT NULL,
  `jenis` enum('Barang','SPJ') DEFAULT 'Barang',
  `no_spj` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`id`, `no_invoice`, `supplier_id`, `tanggal`, `metode_bayar`, `total`, `status_hutang`, `keterangan`, `nota_file`, `jenis`, `no_spj`) VALUES
(13, 'MGJS-0001', 3, '2025-09-11', 'Cash', 3750000.00, 'Lunas', '', NULL, 'Barang', NULL),
(15, 'MGJS-0002', 3, '2025-09-11', 'Kredit', 6000000.00, 'Lunas', 'win 11', NULL, 'Barang', NULL),
(16, 'MGJS-0003', 3, '2025-09-11', 'Kredit', 10500.00, 'Lunas', 'azzzz', 'nota_20250911160955.pdf', 'Barang', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pembelian_detail`
--

CREATE TABLE `pembelian_detail` (
  `id` int(11) NOT NULL,
  `pembelian_id` int(11) DEFAULT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `harga` decimal(15,2) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian_detail`
--

INSERT INTO `pembelian_detail` (`id`, `pembelian_id`, `produk_id`, `qty`, `harga`, `subtotal`) VALUES
(18, 13, 4, 50, 75000.00, 3750000.00),
(19, 15, 6, 50, 120000.00, 6000000.00),
(20, 16, 6, 7, 1500.00, 10500.00);

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_instansi`
--

CREATE TABLE `penjualan_instansi` (
  `id` int(11) NOT NULL,
  `no_invoice` varchar(30) NOT NULL,
  `tanggal` date NOT NULL,
  `customer_id` int(11) NOT NULL,
  `jenis_penjualan` enum('SPJ','Real','SPJ+Real') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `total` decimal(15,2) DEFAULT 0.00,
  `dpp` decimal(15,2) DEFAULT 0.00,
  `ppn` decimal(15,2) DEFAULT 0.00,
  `pph` decimal(15,2) DEFAULT 0.00,
  `biaya_adm` decimal(15,2) DEFAULT 0.00,
  `total_masuk` decimal(15,2) DEFAULT 0.00,
  `status` enum('Lunas','Belum Lunas') DEFAULT 'Belum Lunas',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan_instansi`
--

INSERT INTO `penjualan_instansi` (`id`, `no_invoice`, `tanggal`, `customer_id`, `jenis_penjualan`, `keterangan`, `total`, `dpp`, `ppn`, `pph`, `biaya_adm`, `total_masuk`, `status`, `created_at`, `updated_at`) VALUES
(24, 'MGI-00001', '2025-09-13', 4, 'SPJ+Real', '', 27075000.00, 24391892.00, 2683108.00, 121959.00, 10000.00, 24259932.00, 'Belum Lunas', '2025-09-13 06:33:44', '2025-09-13 06:33:44');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_instansi_detail`
--

CREATE TABLE `penjualan_instansi_detail` (
  `id` int(11) NOT NULL,
  `penjualan_id` int(11) NOT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `jenis_item` enum('SPJ','Real') NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan_instansi_detail`
--

INSERT INTO `penjualan_instansi_detail` (`id`, `penjualan_id`, `produk_id`, `nama_produk`, `qty`, `harga`, `subtotal`, `jenis_item`, `keterangan`) VALUES
(23, 24, NULL, 'PO6870B0BF5A312', 1.00, 27000000.00, 27000000.00, 'SPJ', ''),
(24, 24, 4, 'Install Ulang Win 10', 1.00, 75000.00, 75000.00, 'Real', '');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_service`
--

CREATE TABLE `penjualan_service` (
  `id` int(11) NOT NULL,
  `no_invoice` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `total` double DEFAULT 0,
  `dpp` double DEFAULT 0,
  `ppn` double DEFAULT 0,
  `pph` double DEFAULT 0,
  `biaya_adm` double DEFAULT 0,
  `total_masuk` double DEFAULT 0,
  `status` varchar(20) DEFAULT 'Belum Lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_service_detail`
--

CREATE TABLE `penjualan_service_detail` (
  `id` int(11) NOT NULL,
  `penjualan_id` int(11) NOT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `nama_produk` varchar(200) DEFAULT NULL,
  `qty` int(11) DEFAULT 0,
  `harga` double DEFAULT 0,
  `subtotal` double DEFAULT 0,
  `jenis_item` enum('SPJ','Real') DEFAULT 'SPJ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_toko`
--

CREATE TABLE `penjualan_toko` (
  `id` int(11) NOT NULL,
  `no_invoice` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `total` double DEFAULT 0,
  `dpp` double DEFAULT 0,
  `ppn` double DEFAULT 0,
  `pph` double DEFAULT 0,
  `biaya_adm` double DEFAULT 0,
  `total_masuk` double DEFAULT 0,
  `status` varchar(20) DEFAULT 'Belum Lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_toko_detail`
--

CREATE TABLE `penjualan_toko_detail` (
  `id` int(11) NOT NULL,
  `penjualan_id` int(11) NOT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `nama_produk` varchar(200) DEFAULT NULL,
  `qty` int(11) DEFAULT 0,
  `harga` double DEFAULT 0,
  `subtotal` double DEFAULT 0,
  `jenis_item` enum('SPJ','Real') DEFAULT 'SPJ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `piutang_instansi`
--

CREATE TABLE `piutang_instansi` (
  `id` int(11) NOT NULL,
  `penjualan_id` int(11) NOT NULL,
  `jatuh_tempo` date DEFAULT NULL,
  `sisa_piutang` decimal(15,2) DEFAULT 0.00,
  `status` enum('Lunas','Belum Lunas') DEFAULT 'Belum Lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `piutang_instansi`
--

INSERT INTO `piutang_instansi` (`id`, `penjualan_id`, `jatuh_tempo`, `sisa_piutang`, `status`) VALUES
(10, 24, '2025-10-13', 27075000.00, 'Belum Lunas');

-- --------------------------------------------------------

--
-- Table structure for table `piutang_instansi_pembayaran`
--

CREATE TABLE `piutang_instansi_pembayaran` (
  `id` int(11) NOT NULL,
  `piutang_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `piutang_service`
--

CREATE TABLE `piutang_service` (
  `id` int(11) NOT NULL,
  `penjualan_id` int(11) NOT NULL,
  `jatuh_tempo` date DEFAULT NULL,
  `sisa_piutang` decimal(15,2) DEFAULT 0.00,
  `status` enum('Lunas','Belum Lunas') DEFAULT 'Belum Lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `piutang_service_pembayaran`
--

CREATE TABLE `piutang_service_pembayaran` (
  `id` int(11) NOT NULL,
  `piutang_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `piutang_toko`
--

CREATE TABLE `piutang_toko` (
  `id` int(11) NOT NULL,
  `penjualan_id` int(11) NOT NULL,
  `jatuh_tempo` date DEFAULT NULL,
  `sisa_piutang` decimal(15,2) DEFAULT 0.00,
  `status` enum('Lunas','Belum Lunas') DEFAULT 'Belum Lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `piutang_toko_pembayaran`
--

CREATE TABLE `piutang_toko_pembayaran` (
  `id` int(11) NOT NULL,
  `piutang_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `kode_barcode` varchar(100) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `harga_beli` decimal(15,2) DEFAULT 0.00,
  `harga_jual` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `kode_produk` varchar(50) DEFAULT NULL,
  `harga_jual_online` decimal(15,2) DEFAULT 0.00,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `serial_number` text DEFAULT NULL,
  `link_siplah` varchar(255) DEFAULT NULL,
  `link_inaproc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `kode_barcode`, `kategori_id`, `satuan`, `stok`, `harga_beli`, `harga_jual`, `created_at`, `kode_produk`, `harga_jual_online`, `deskripsi`, `gambar`, `serial_number`, `link_siplah`, `link_inaproc`) VALUES
(4, 'Install Ulang Win 10', 'Ins10', 11, NULL, 50, 0.00, 75000.00, '2025-09-08 09:51:27', NULL, 0.00, 'Win 10 Plus Office', '1758021586_Service_png', '', '', ''),
(6, 'Install Windows 11 Home / Pro', 'Ins11', 11, NULL, 57, 0.00, 120000.00, '2025-09-11 09:05:17', NULL, 0.00, 'Install Win 11 dan Office ', '1758021595_Service_png', '', '', ''),
(7, 'RJ45 and RJ11 Network Cable Tester Putih', '2993500197807', 8, NULL, 4, 24750.00, 45000.00, '2025-09-13 04:07:52', NULL, 75000.00, '', '1757752004_RJ45 and RJ11 Network Cable Tester Putih.jpg', '', '', ''),
(8, 'Printer Epson L121', 'C11CD76502', 10, NULL, 1, 1475000.00, 1750000.00, '2025-09-13 05:06:02', NULL, 1750000.00, 'Printing Technology: Piezoelectric Printhead\r\nPrinter Type: Print\r\nMaximum Print Resolution: 720 x 720 dpi\r\nPrint Speed: A4 Simplex (Black / Colour) Up to 9.0 ipm / 4.8 ipm\r\nEpson EcoTank L121', '1757752077_PRINTER EPSON L121.jpg', 'X9LU680013\r\n\r\n', '', ''),
(9, 'Printer Epson L5290 INDO', 'C11CJ65504', 10, NULL, 1, 3800000.00, 4500000.00, '2025-09-13 05:09:53', NULL, 4500000.00, 'Print; Scan; Copy; Fax with ADF\r\nPrint Speed: Up to 33 ppm (Black)/15 ppm (Color)\r\nMaximum Paper Size : 215.9 x 1200 mm\r\nNetwork: Ethernet; Wi-Fi IEEE 802.11b/g/n; Wi-Fi Direct\r\nCompact integrated tank design\r\nEPSON EcoTank L5290\r\nEPSON Original Tinta', '1757752096_PRINTER EPSON L5290 INDO.webp', 'X8H4166965\r\n', '', ''),
(10, 'Printer Epson L3210', 'C11CJ68503', 10, NULL, 6, 2000000.00, 2350000.00, '2025-09-13 05:13:48', NULL, 2350000.00, 'Printer Type: Print/Scan/Copy\r\nPrint Speed : Up to 33.0 ppm (Black)/ 15.0 ppm (Color)\r\nMaximum Paper Size: 215.9 x 1200 mm\r\nPrinting Maximum Resolution: 5760 x 1440 dpi\r\nInterface: USB 2.0\r\nUnit Utama', '1757752123_PRINTER EPSON L3210 INDO.jpg', 'XAGKF41665, XAGKF40985, XAGKF45575, XAGF33327, XAGKF51054, XAGKF33370', '', ''),
(11, 'Printer Epson L3211 TKDN', 'C11CJ68504', 10, NULL, 1, 2100000.00, 2500000.00, '2025-09-13 05:18:23', NULL, 2500000.00, 'Fungsi : Print, Scan, Copy\r\nTeknologi Printing : On-demand inkjet (Piezoelectric)\r\nResolusi : 5760 x 1440 dpi\r\nKecepatan Cetak : Hingga 33 ppm/15 ppm (Draft Hitam/Warna), Hingga 10 ipm/5,0 ipm (ISO)\r\nWaktu Keluar Halaman Pertama Dari Mode Siap (Hitam/Warna) : Sekitar 10 detik/16 detik\r\nKapasitas Kertas Input : Hingga 20 lembar-Kertas Foto Glossy Premium, 10 lembar-Amplop, 30 lembar-Kartu Pos, Hingga 100 lembar-A4/Letter (80 gram)\r\nKapasitas Kertas Output : Up to 30 sheets, A4\r\nJumlah Baki Kertas: 1\r\nUkuran Kertas Maksimum: 215.9 x 1200 mm (8.5 x 47.24″)\r\nUkuran Kertas Cetak : Legal, Indian-Legal (215 x 345 mm), 8,5 x 13″, Letter, A4, 16K (195 x 270 mm), B5, A5, B6, A6, Hagaki (100 x 148 mm), 5 x 7″, 4 x 6″, Amplop: #10, DL, C6\r\nInterface: USB 2.0\r\nGaransi Resmi Epson 3 Tahun', '1757752158_PRINTER EPSON L3211 INDO TKDN.jpg', 'XE4Q001235', '', ''),
(12, 'Projektor EPSON EB-E600+Tas Projektor', 'V11H981055', 4, NULL, 3, 5350000.00, 6000000.00, '2025-09-13 05:33:47', NULL, 6000000.00, 'Projection Technology: RGB liquid crystal shutter projection system (3LCD)\r\nResolution: XGA (1024 x 768)\r\nBrightness: 3400 Lumens\r\nContrast Ratio: 15,000:1\r\nConnectivity: USB, D-Sub, HDMI:\r\nUnit utama', '1757752188_PROJEPSON EB-E600+TAS PROJ.jpg', 'XDCG4Y01838, XDCG4Y01831, XDCG4Y01841', '', ''),
(13, 'Projektor EPSON EB-X600+Tas Projektor', 'V11H982055', 4, NULL, 2, 5600000.00, 6000000.00, '2025-09-13 05:35:19', NULL, 6000000.00, '', '1757752206_PROJ EPSON EB-X600+TAS PROJ', 'XDCJ4900551, XDCJ4901705', '', ''),
(14, 'Micro HDMI To VGA Socket Video Cable (NYK Cable)', '6922108900812', 8, NULL, 1, 17000.00, 25000.00, '2025-09-13 07:42:46', NULL, 25000.00, '', '1757752287_Micro HDMI To VGA Socket Video Cable (NYK Cable).jpg', '', '', ''),
(15, 'USB Hub Rexus RXH-320', '6392477409764', 8, NULL, 1, 65000.00, 105000.00, '2025-09-13 07:45:19', NULL, 105000.00, 'USB HUB REXUS 4PORT RXH-320 – V.3.0 1PORT + V.2.0 3PORT\r\n\r\nFitur :\r\n– 1 (satu) masukan dan 4 keluaran USB port\r\n– Kompatibel penuh dengan USB versi 3.0.\r\n– Kecepatan transfer hingga 5 Gbps.\r\n– Kompatibel Penuh dengan USB versi 2.0 hingga up to 480Mbps.\r\n– Desain yang menarik dengan bahan alumunium.', NULL, '', '', ''),
(16, 'Converter fiber optic TO lan HTB 3100 (NetLink)', 'SIX001600501999888', 12, NULL, 3, 80000.00, 110000.00, '2025-09-13 07:48:57', NULL, 110000.00, 'NetLINK Series 10/100M Singlemode Single Fiber Converter is the conversion equipment of Ethernet optical-electronic signals between 10/100M UTP interface (TX) and 100M Fiber interface (FX).\r\nThe traditional 10/100M Fast Ethernet can be extended to the distance of 90km through Single Optical Fiber link.\r\nThe performance and quality of the products are excellent because of adopting latest IC from Taiwan.\r\nIt must be used in couples, because the transmitted optical differs from the receive optical in wavelength.\r\n6 Group LED indicated lights could fully monitor the working conditions of Converters.\r\nIt is easy for end-users to observe the conditions of network.\r\nThe Series product with reasonable price is especially designed for network end-users.', NULL, '', '', ''),
(17, 'Flashdisk Sandisk 16Gb', '619659000431', 8, NULL, 10, 51000.00, 65000.00, '2025-09-13 07:53:16', NULL, 65000.00, '', NULL, '', '', ''),
(18, 'Webcam C270 Hd Logitec', '097855070753', 7, NULL, 1, 260000.00, 300000.00, '2025-09-13 08:01:27', NULL, 300000.00, 'C270 HD Webcam menghadirkan panggilan konferensi yang tajam dan mulus dalam format widescreen. Automatic light correction menampilkan Anda dalam warna yang nyata dan alami.\r\nResolusi Maks.: 720p/30fps\r\nKamera mega pixel: 0.9\r\nJenis fokus: fixed focus\r\nJenis lensa: plastik\r\nMikrofon internal: Mono\r\nJangkauan mikrofon: Maksimal 1 m\r\nBidang pandang diagonal (dFoV): 55°\r\nUniversal mounting clip cocok dengan berbagai laptop, LCD, atau layar', NULL, '2331AP03XK79\r\n', '', ''),
(19, 'Advance USB Dongle Wifi Wireless Adapter Receiver WF-01 (802.IIN)', '8699258564291', 12, NULL, 1, 42000.00, 70000.00, '2025-09-13 08:05:02', NULL, 70000.00, '', '1758021726_Advance_USB_Dongle_Wifi_Wireless_Adapter_Receiver_WF_01_150mbps_png', '', '', ''),
(20, 'KABEL AUDIO VENTION 3,5MM 1MTR/V-BBZBF', '6922794751163', 12, NULL, 5, 16000.00, 25000.00, '2025-09-13 08:06:28', NULL, 25000.00, '* Jenis Kabel: Audio\r\n* Konektor: Male to Male\r\n* Ukuran Konektor: 3,5 mm\r\n* Panjang Kabel: 1 Meter\r\n* Bahan Kabel: TPE (Thermoplastic Elastomer)\r\n* Bahan Konektor: Berlapis Emas\r\n* Resistensi: * Rentang Frekuensi: 20Hz - 20KHz\r\n* Tegangan Terukur: 30V\r\n* Arus Terukur: 0,5A\r\n* Dilengkapi dengan pelindung interferensi kualitas tinggi untuk koneksi yang stabil dan andal\r\n* Kompatibel dengan sebagian besar perangkat audio dengan jack 3,5 mm, seperti smartphone, tablet, pemutar MP3, speaker, dan headphone', NULL, '', '', ''),
(21, 'Per Pengait Encoder Line Panjang Epson L Series (L110 L3210)', '8999990036156', 6, NULL, 5, 15000.00, 35000.00, '2025-09-13 08:12:21', NULL, 35000.00, '', NULL, '', '', ''),
(22, 'Timing Belt Epson L120/L3110 ', '4509990009353', 6, NULL, 3, 10000.00, 25000.00, '2025-09-13 08:15:04', NULL, 25000.00, '', NULL, '', '', ''),
(23, 'Tinta Epson Original 664 Magenta ', '8885007024103', 6, NULL, 5, 82000.00, 95000.00, '2025-09-13 08:18:40', NULL, 95000.00, '', NULL, '', '', ''),
(24, 'Tinta Epson Original 664 Black', '8885007024080', 6, NULL, 5, 82000.00, 95000.00, '2025-09-13 08:20:30', NULL, 95000.00, '', NULL, '', '', ''),
(25, 'Tinta Epson Original 664 Cyan ', '8885007024097', 6, NULL, 5, 82000.00, 95000.00, '2025-09-13 08:22:33', NULL, 95000.00, '', NULL, '', '', ''),
(26, 'Tinta Epson Original 664 Yellow', '8885007024110', 6, NULL, 5, 82000.00, 95000.00, '2025-09-13 08:23:01', NULL, 95000.00, '', NULL, '', '', ''),
(27, 'TINTA BROTHER BTD60BK BLACK 106ML', '4977766786324', 6, NULL, 1, 96000.00, 120000.00, '2025-09-13 08:24:56', NULL, 120000.00, '', '1757751924_TINTA BROTHER BTD60BK BLACK 106ML.jpg', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `no_service` varchar(50) DEFAULT NULL,
  `penjualan_id` int(11) DEFAULT NULL,
  `teknisi_id` int(11) DEFAULT NULL,
  `status_admin` enum('Draft','Open','Proses','Pending','Selesai') DEFAULT 'Draft',
  `status_teknisi` enum('Open','Proses','Pending','Selesai') DEFAULT 'Open',
  `catatan_admin` text DEFAULT NULL,
  `catatan_teknisi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_sparepart`
--

CREATE TABLE `service_sparepart` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga` decimal(15,2) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `nama_supplier`, `alamat`, `no_telp`, `email`) VALUES
(3, 'Megah Glory', 'Jl. Raya Tim. No.51, Cipakat, Kec. Singaparna, Kabupaten Tasikmalaya', '', 'admin@megahglory.com');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `role` enum('admin','teknisi') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'Riza', '$2y$12$/ro284yHHVpndj1bb0sxbOEsAVvspImikgE7NSDYhlv.m9ssCeH1e', 'Riza Rinzana', 'admin', '2025-09-05 09:04:59'),
(2, 'Rizal', '$2y$10$8MEylAaS6jN9cYT4rtspFOI30974afOsEs31wO8sqEe0RWKHDY1Na', 'Rizal Ghany Arrasyid', 'teknisi', '2025-09-07 15:15:46'),
(3, 'Rita', '$2y$10$gcflFHq6rzMD1may8KfSb.L3amiNuh5gelWZEeX3cgBA5oDb9z5Iq', 'Rita Wulan Purnama', 'admin', '2025-09-07 15:19:16'),
(4, 'Anitia', '$2y$10$V.buYkGFW26LJHFCNSbd3.pPSIlBrRDVUNPbYBPt.U/Mj3p5tJh9K', 'Anitia Rahma', 'admin', '2025-09-07 15:19:38'),
(5, 'Ariza', '$2y$10$EWn1tdG94lxz2LufA.HLt.aGSlQHl7Gkl4yUSX09UxHiMgG3RzXVm', 'Riza Rinzana', 'teknisi', '2025-09-07 15:20:44'),
(6, 'Fazrin', '$2y$10$FqPv4dmpI4r0yVT4qPUOb..rCfAg/r18lf2sepJqZkT6jbOZut3/6', 'Fazrin Nurohman', 'admin', '2025-09-07 15:21:56'),
(7, 'pkl', '$2y$10$NtM5LOt6oxTSs89jVexBXeu35VZ.t9tF6szcAyczepaVZglWC7KNa', 'PKL Puspahiang dan IWU', 'admin', '2025-09-07 15:21:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hutang`
--
ALTER TABLE `hutang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_hutang_pembelian_id` (`pembelian_id`);

--
-- Indexes for table `hutang_pembayaran`
--
ALTER TABLE `hutang_pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hutang_id` (`hutang_id`);

--
-- Indexes for table `kategori_produk`
--
ALTER TABLE `kategori_produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_invoice` (`no_invoice`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `pembelian_detail`
--
ALTER TABLE `pembelian_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembelian_id` (`pembelian_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `penjualan_instansi`
--
ALTER TABLE `penjualan_instansi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_invoice` (`no_invoice`);

--
-- Indexes for table `penjualan_instansi_detail`
--
ALTER TABLE `penjualan_instansi_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detail_instansi` (`penjualan_id`);

--
-- Indexes for table `penjualan_service`
--
ALTER TABLE `penjualan_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `penjualan_service_detail`
--
ALTER TABLE `penjualan_service_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjualan_id` (`penjualan_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `penjualan_toko`
--
ALTER TABLE `penjualan_toko`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `penjualan_toko_detail`
--
ALTER TABLE `penjualan_toko_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjualan_id` (`penjualan_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `piutang_instansi`
--
ALTER TABLE `piutang_instansi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_piutang_instansi` (`penjualan_id`);

--
-- Indexes for table `piutang_instansi_pembayaran`
--
ALTER TABLE `piutang_instansi_pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `piutang_id` (`piutang_id`);

--
-- Indexes for table `piutang_service`
--
ALTER TABLE `piutang_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjualan_id` (`penjualan_id`);

--
-- Indexes for table `piutang_service_pembayaran`
--
ALTER TABLE `piutang_service_pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `piutang_id` (`piutang_id`);

--
-- Indexes for table `piutang_toko`
--
ALTER TABLE `piutang_toko`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjualan_id` (`penjualan_id`);

--
-- Indexes for table `piutang_toko_pembayaran`
--
ALTER TABLE `piutang_toko_pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `piutang_id` (`piutang_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `idx_kode_barcode` (`kode_barcode`),
  ADD KEY `idx_kode_produk` (`kode_produk`),
  ADD KEY `idx_nama_produk` (`nama_produk`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_service` (`no_service`),
  ADD KEY `penjualan_id` (`penjualan_id`),
  ADD KEY `teknisi_id` (`teknisi_id`);

--
-- Indexes for table `service_sparepart`
--
ALTER TABLE `service_sparepart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hutang`
--
ALTER TABLE `hutang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `hutang_pembayaran`
--
ALTER TABLE `hutang_pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kategori_produk`
--
ALTER TABLE `kategori_produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `pembelian_detail`
--
ALTER TABLE `pembelian_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `penjualan_instansi`
--
ALTER TABLE `penjualan_instansi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `penjualan_instansi_detail`
--
ALTER TABLE `penjualan_instansi_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `penjualan_service`
--
ALTER TABLE `penjualan_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penjualan_service_detail`
--
ALTER TABLE `penjualan_service_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penjualan_toko`
--
ALTER TABLE `penjualan_toko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penjualan_toko_detail`
--
ALTER TABLE `penjualan_toko_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `piutang_instansi`
--
ALTER TABLE `piutang_instansi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `piutang_instansi_pembayaran`
--
ALTER TABLE `piutang_instansi_pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `piutang_service`
--
ALTER TABLE `piutang_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `piutang_service_pembayaran`
--
ALTER TABLE `piutang_service_pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `piutang_toko`
--
ALTER TABLE `piutang_toko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `piutang_toko_pembayaran`
--
ALTER TABLE `piutang_toko_pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_sparepart`
--
ALTER TABLE `service_sparepart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hutang`
--
ALTER TABLE `hutang`
  ADD CONSTRAINT `fk_hutang_pembelian` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelian` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hutang_ibfk_1` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelian` (`id`);

--
-- Constraints for table `hutang_pembayaran`
--
ALTER TABLE `hutang_pembayaran`
  ADD CONSTRAINT `hutang_pembayaran_ibfk_1` FOREIGN KEY (`hutang_id`) REFERENCES `hutang` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `pembelian_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`);

--
-- Constraints for table `pembelian_detail`
--
ALTER TABLE `pembelian_detail`
  ADD CONSTRAINT `pembelian_detail_ibfk_1` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelian` (`id`),
  ADD CONSTRAINT `pembelian_detail_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `penjualan_instansi_detail`
--
ALTER TABLE `penjualan_instansi_detail`
  ADD CONSTRAINT `fk_detail_instansi` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan_instansi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penjualan_service`
--
ALTER TABLE `penjualan_service`
  ADD CONSTRAINT `penjualan_service_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `penjualan_service_detail`
--
ALTER TABLE `penjualan_service_detail`
  ADD CONSTRAINT `penjualan_service_detail_ibfk_1` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan_service` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penjualan_service_detail_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `penjualan_toko`
--
ALTER TABLE `penjualan_toko`
  ADD CONSTRAINT `penjualan_toko_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `penjualan_toko_detail`
--
ALTER TABLE `penjualan_toko_detail`
  ADD CONSTRAINT `penjualan_toko_detail_ibfk_1` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan_toko` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penjualan_toko_detail_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `piutang_instansi`
--
ALTER TABLE `piutang_instansi`
  ADD CONSTRAINT `fk_piutang_instansi` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan_instansi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `piutang_instansi_pembayaran`
--
ALTER TABLE `piutang_instansi_pembayaran`
  ADD CONSTRAINT `piutang_instansi_pembayaran_ibfk_1` FOREIGN KEY (`piutang_id`) REFERENCES `piutang_instansi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `piutang_service`
--
ALTER TABLE `piutang_service`
  ADD CONSTRAINT `piutang_service_ibfk_1` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan_service` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `piutang_service_pembayaran`
--
ALTER TABLE `piutang_service_pembayaran`
  ADD CONSTRAINT `piutang_service_pembayaran_ibfk_1` FOREIGN KEY (`piutang_id`) REFERENCES `piutang_service` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `piutang_toko`
--
ALTER TABLE `piutang_toko`
  ADD CONSTRAINT `piutang_toko_ibfk_1` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan_toko` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `piutang_toko_pembayaran`
--
ALTER TABLE `piutang_toko_pembayaran`
  ADD CONSTRAINT `piutang_toko_pembayaran_ibfk_1` FOREIGN KEY (`piutang_id`) REFERENCES `piutang_toko` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_produk` (`id`);

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_ibfk_2` FOREIGN KEY (`teknisi_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `service_sparepart`
--
ALTER TABLE `service_sparepart`
  ADD CONSTRAINT `service_sparepart_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`),
  ADD CONSTRAINT `service_sparepart_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
