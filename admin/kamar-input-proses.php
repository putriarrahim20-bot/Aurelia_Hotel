<?php
require_once __DIR__ . '/../fungsi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['simpan'])) {
    header('Location: index.php?p=kamar');
    exit;
}

$nomor  = trim($_POST['nomor_kamar'] ?? '');
$idtipe = (int)($_POST['idtipe'] ?? 0);
$status = $_POST['status'] ?? '';

if (!preg_match('/^[A-Za-z0-9\-]{1,10}$/', $nomor)) {
    kembali_dengan_pesan('Nomor kamar hanya boleh huruf, angka, dan tanda - (maksimal 10 karakter).');
}
if (!in_array($status, ['Aktif', 'Perbaikan'], true)) {
    kembali_dengan_pesan('Status tidak valid.');
}

$cek = mysqli_prepare($db, "SELECT 1 FROM tipe_kamar WHERE idtipe = ?");
mysqli_stmt_bind_param($cek, 'i', $idtipe);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);
if (mysqli_stmt_num_rows($cek) === 0) {
    kembali_dengan_pesan('Tipe kamar tidak ditemukan.');
}
mysqli_stmt_close($cek);

$cek = mysqli_prepare($db, "SELECT 1 FROM kamar WHERE nomor_kamar = ?");
mysqli_stmt_bind_param($cek, 's', $nomor);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);
if (mysqli_stmt_num_rows($cek) > 0) {
    kembali_dengan_pesan('Nomor kamar sudah ada.');
}
mysqli_stmt_close($cek);

$stmt = mysqli_prepare($db, "INSERT INTO kamar (nomor_kamar, idtipe, status) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sis', $nomor, $idtipe, $status);
mysqli_stmt_execute($stmt);

header('Location: index.php?p=kamar');
exit;
