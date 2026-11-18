<?php
require_once __DIR__ . '/../fungsi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['simpan'])) {
    header('Location: index.php?p=tipe');
    exit;
}

$id        = (int)($_POST['idtipe'] ?? 0);
$nama      = trim($_POST['nama_tipe'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$harga     = (int)($_POST['harga_malam'] ?? -1);
$kapasitas = (int)($_POST['kapasitas'] ?? 0);

if ($nama === '' || mb_strlen($nama) > 50) {
    kembali_dengan_pesan('Nama tipe wajib diisi (maksimal 50 karakter).');
}
if ($harga < 0) {
    kembali_dengan_pesan('Harga tidak boleh negatif.');
}
if ($kapasitas < 1) {
    kembali_dengan_pesan('Kapasitas minimal 1 tamu.');
}

$stmt = mysqli_prepare($db, "SELECT foto FROM tipe_kamar WHERE idtipe = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$lama = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$lama) {
    kembali_dengan_pesan('Tipe kamar tidak ditemukan.');
}
$foto_lama = $lama['foto'] ?: '-';

$error = '';
$foto  = simpan_foto($_FILES['foto'] ?? null, 'tipe_' . $id, $error);
if ($foto === false) {
    kembali_dengan_pesan($error);
}
if ($foto === null) {
    $foto = $foto_lama;
} elseif ($foto !== $foto_lama) {
    hapus_foto($foto_lama);
}

$stmt = mysqli_prepare($db,
    "UPDATE tipe_kamar SET nama_tipe = ?, deskripsi = ?, harga_malam = ?, kapasitas = ?, foto = ? WHERE idtipe = ?");
mysqli_stmt_bind_param($stmt, 'ssiisi', $nama, $deskripsi, $harga, $kapasitas, $foto, $id);
mysqli_stmt_execute($stmt);

header('Location: index.php?p=tipe');
exit;
