<?php
require_once __DIR__ . '/../fungsi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?p=reservasi');
    exit;
}

$id         = (int)($_POST['id'] ?? 0);
$jumlah     = (int)($_POST['jumlah'] ?? 0);
$metode     = $_POST['metode'] ?? '';
$keterangan = mb_substr(trim($_POST['keterangan'] ?? ''), 0, 100);
$kembali    = 'index.php?p=reservasi-detail&id=' . $id;

if ($jumlah < 1) {
    pesan_lalu_ke('Jumlah pembayaran harus lebih dari 0.', $kembali);
}
if (!in_array($metode, ['Tunai', 'Transfer', 'QRIS'], true)) {
    pesan_lalu_ke('Metode pembayaran tidak valid.', $kembali);
}

mysqli_begin_transaction($db);

// Kunci baris reservasi agar dua pembayaran bersamaan tidak membuat total melebihi tagihan
$stmt = mysqli_prepare($db, "SELECT total, status FROM reservasi WHERE idreservasi = ? FOR UPDATE");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$r) {
    gagal_transaksi($db, 'Reservasi tidak ditemukan.', 'index.php?p=reservasi');
}
if (!in_array($r['status'], ['Menunggu', 'Dikonfirmasi', 'Check-in'], true)) {
    gagal_transaksi($db, 'Reservasi ini tidak bisa menerima pembayaran.', $kembali);
}

$sisa = (int)$r['total'] - total_dibayar($db, $id);
if ($jumlah > $sisa) {
    gagal_transaksi($db, 'Jumlah melebihi sisa pembayaran (' . rupiah($sisa) . ').', $kembali);
}

$tgl  = date('Y-m-d H:i:s');
$stmt = mysqli_prepare($db,
    "INSERT INTO pembayaran (idreservasi, jumlah, metode, keterangan, tgl_bayar) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'iisss', $id, $jumlah, $metode, $keterangan, $tgl);
if (!mysqli_stmt_execute($stmt)) {
    gagal_transaksi($db, 'Gagal menyimpan pembayaran.', $kembali);
}
mysqli_stmt_close($stmt);

mysqli_commit($db);
header('Location: ' . $kembali);
exit;
