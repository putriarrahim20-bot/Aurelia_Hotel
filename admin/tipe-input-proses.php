<?php
require_once __DIR__ . '/../fungsi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['simpan'])) {
    header('Location: index.php?p=tipe');
    exit;
}

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

$stmt = mysqli_prepare($db,
    "INSERT INTO tipe_kamar (nama_tipe, deskripsi, harga_malam, kapasitas, foto) VALUES (?, ?, ?, ?, '-')");
mysqli_stmt_bind_param($stmt, 'ssii', $nama, $deskripsi, $harga, $kapasitas);
if (!mysqli_stmt_execute($stmt)) {
    kembali_dengan_pesan('Gagal menyimpan tipe kamar.');
}
$id = mysqli_insert_id($db);
mysqli_stmt_close($stmt);

$error = '';
$foto  = simpan_foto($_FILES['foto'] ?? null, 'tipe_' . $id, $error);
if ($foto === false) {
    pesan_lalu_ke('Tipe kamar tersimpan, tetapi foto gagal diunggah: ' . $error, 'index.php?p=tipe-edit&id=' . $id);
}
if ($foto !== null) {
    $stmt = mysqli_prepare($db, "UPDATE tipe_kamar SET foto = ? WHERE idtipe = ?");
    mysqli_stmt_bind_param($stmt, 'si', $foto, $id);
    mysqli_stmt_execute($stmt);
}

header('Location: index.php?p=tipe');
exit;
