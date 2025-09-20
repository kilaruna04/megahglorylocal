<?php
// Service/Admin/Pembelian/hutang_helpers.php
// ==========================================
// KUMPULAN FUNGSI HUTANG untuk modul Pembelian
// Menggunakan tabel: hutang & hutang_pembayaran

if (!function_exists('create_or_update_hutang')) {
    /**
     * Buat / Update hutang sesuai metode bayar.
     * - Jika Kredit → insert baru jika belum ada, update kalau sudah ada.
     * - Jika Cash   → hapus hutang terkait & tandai lunas di pembelian.
     */
    function create_or_update_hutang($conn, $pembelian_id, $tanggal, $metode, $total, $jatuh_tempo_input = null) {
        if (strtolower($metode) === 'kredit') {
            // default jatuh tempo +30 hari jika kosong
            $jatuh_tempo = $jatuh_tempo_input ?: date('Y-m-d', strtotime($tanggal.' +30 days'));

            // cek apakah hutang sudah ada
            $cek = $conn->query("SELECT id FROM hutang WHERE pembelian_id=".(int)$pembelian_id." LIMIT 1");
            if ($cek && $cek->num_rows > 0) {
                $row = $cek->fetch_assoc();
                $hid = (int)$row['id'];

                // update hutang
                $stmt = $conn->prepare("UPDATE hutang 
                    SET jatuh_tempo=?, sisa_hutang=?, status='Belum Lunas' 
                    WHERE id=?");
                $stmt->bind_param("sdi", $jatuh_tempo, $total, $hid);
                $stmt->execute();
                $stmt->close();
            } else {
                // insert hutang baru
                $stmt = $conn->prepare("INSERT INTO hutang (pembelian_id, jatuh_tempo, sisa_hutang, status) 
                                        VALUES (?,?,?, 'Belum Lunas')");
                $stmt->bind_param("isd", $pembelian_id, $jatuh_tempo, $total);
                $stmt->execute();
                $stmt->close();
            }

            // update ringkasan di pembelian
            $conn->query("UPDATE pembelian SET status_hutang='Belum Lunas' WHERE id=".(int)$pembelian_id);
        } else {
            // metode Cash → hapus hutang + tandai Lunas
            $conn->query("DELETE FROM hutang WHERE pembelian_id=".(int)$pembelian_id);
            $conn->query("UPDATE pembelian SET status_hutang='Lunas' WHERE id=".(int)$pembelian_id);
        }
    }
}

if (!function_exists('remove_hutang_for_pembelian')) {
    /**
     * Hapus hutang & semua riwayat pembayaran untuk pembelian tertentu.
     */
    function remove_hutang_for_pembelian($conn, $pembelian_id) {
        // hapus riwayat pembayaran hutang
        $conn->query("DELETE hp FROM hutang_pembayaran hp
                      JOIN hutang h ON hp.hutang_id=h.id
                      WHERE h.pembelian_id=".(int)$pembelian_id);

        // hapus hutang utama
        $conn->query("DELETE FROM hutang WHERE pembelian_id=".(int)$pembelian_id);
    }
}
