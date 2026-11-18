<?php
require_once __DIR__ . '/../fungsi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?p=reservasi');
    exit;
}

$id      = (int)($_POST['id'] ?? 0);
$aksi    = $_POST['aksi'] ?? '';
$kembali = 'index.php?p=reservasi-detail&id=' . $id;

if (!in_array($aksi, ['konfirmasi', 'checkin', 'checkout', 'batal'], true)) {
    pesan_lalu_ke('Aksi tidak valid.', $kembali);
}

mysqli_begin_transaction($db);

// Pernyataan pertama: kunci baris reservasi supaya tidak diproses ganda
$stmt = mysqli_prepare($db, "SELECT * FROM reservasi WHERE idreservasi = ? FOR UPDATE");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$r) {
    gagal_transaksi($db, 'Reservasi tidak ditemukan.', 'index.php?p=reservasi');
}

$sekarang = date('Y-m-d H:i:s');
$hari_ini = date('Y-m-d');

if ($aksi === 'konfirmasi') {
    if ($r['status'] !== 'Menunggu') {
        gagal_transaksi($db, 'Hanya reservasi berstatus Menunggu yang bisa dikonfirmasi.', $kembali);
    }

    // Kunci juga baris kamar agar tidak berebut dengan reservasi baru untuk kamar yang sama
    $stmt = mysqli_prepare($db, "SELECT idkamar FROM kamar WHERE idkamar = ? FOR UPDATE");
    mysqli_stmt_bind_param($stmt, 'i', $r['idkamar']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $dibayar = total_dibayar($db, $id);
    $dp      = dp_minimal($r['total']);
    if ($dibayar < $dp) {
        gagal_transaksi($db, 'DP minimal ' . rupiah($dp) . ' belum terpenuhi (baru ' . rupiah($dibayar) . ').', $kembali);
    }
    // Reservasi yang kedaluwarsa mungkin kamarnya sudah diambil orang lain
    if (kamar_bentrok($db, (int)$r['idkamar'], $r['check_in'], $r['check_out'], $id) > 0) {
        gagal_transaksi($db, 'Kamar ini sudah dipakai reservasi lain pada tanggal tersebut. Batalkan reservasi ini dan kembalikan DP.', $kembali);
    }

    $stmt = mysqli_prepare($db, "UPDATE reservasi SET status = 'Dikonfirmasi' WHERE idreservasi = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

} elseif ($aksi === 'checkin') {
    if ($r['status'] !== 'Dikonfirmasi') {
        gagal_transaksi($db, 'Hanya reservasi berstatus Dikonfirmasi yang bisa check-in.', $kembali);
    }
    if ($hari_ini < $r['check_in']) {
        gagal_transaksi($db, 'Belum waktunya check-in (mulai ' . tanggal_indo($r['check_in']) . ').', $kembali);
    }
    if ($hari_ini >= $r['check_out']) {
        gagal_transaksi($db, 'Tanggal menginap sudah lewat. Batalkan reservasi ini.', $kembali);
    }

    $stmt = mysqli_prepare($db, "UPDATE reservasi SET status = 'Check-in', checkin_at = ? WHERE idreservasi = ?");
    mysqli_stmt_bind_param($stmt, 'si', $sekarang, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

} elseif ($aksi === 'checkout') {
    if ($r['status'] !== 'Check-in') {
        gagal_transaksi($db, 'Hanya tamu yang sedang check-in yang bisa check-out.', $kembali);
    }
    $sisa = (int)$r['total'] - total_dibayar($db, $id);
    if ($sisa > 0) {
        gagal_transaksi($db, 'Pembayaran belum lunas. Sisa ' . rupiah($sisa) . ' harus dilunasi sebelum check-out.', $kembali);
    }

    $stmt = mysqli_prepare($db, "UPDATE reservasi SET status = 'Check-out', checkout_at = ? WHERE idreservasi = ?");
    mysqli_stmt_bind_param($stmt, 'si', $sekarang, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

} else { // batal
    if (!in_array($r['status'], ['Menunggu', 'Dikonfirmasi'], true)) {
        gagal_transaksi($db, 'Reservasi ini tidak bisa dibatalkan (sudah check-in/selesai/batal).', $kembali);
    }
    $stmt = mysqli_prepare($db, "UPDATE reservasi SET status = 'Batal' WHERE idreservasi = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

mysqli_commit($db);
header('Location: ' . $kembali);
exit;
