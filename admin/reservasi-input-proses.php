<?php
require_once __DIR__ . '/../fungsi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['simpan'])) {
    header('Location: index.php?p=reservasi');
    exit;
}

$idtipe  = (int)($_POST['idtipe'] ?? 0);
$ci      = $_POST['ci'] ?? '';
$co      = $_POST['co'] ?? '';
$tamu    = (int)($_POST['tamu'] ?? 0);
$nama    = trim($_POST['nama'] ?? '');
$email   = trim($_POST['email'] ?? '');
$no_hp   = trim($_POST['no_hp'] ?? '');
$catatan = trim($_POST['catatan'] ?? '');

$error = '';
$kode  = buat_reservasi($db, $idtipe, $ci, $co, $tamu, $nama, $email, $no_hp, $catatan, 'Dikonfirmasi', $error);
if ($kode === false) {
    kembali_dengan_pesan($error);
}

// Buka halaman detail reservasi yang baru dibuat (untuk mencatat pembayaran)
$stmt = mysqli_prepare($db, "SELECT idreservasi FROM reservasi WHERE kode_booking = ?");
mysqli_stmt_bind_param($stmt, 's', $kode);
mysqli_stmt_execute($stmt);
$r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

header('Location: index.php?p=reservasi-detail&id=' . (int)$r['idreservasi']);
exit;
