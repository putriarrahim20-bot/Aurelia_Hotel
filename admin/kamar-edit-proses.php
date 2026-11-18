<?php
require_once __DIR__ . '/../fungsi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['simpan'])) {
    header('Location: index.php?p=kamar');
    exit;
}

$id     = (int)($_POST['idkamar'] ?? 0);
$nomor  = trim($_POST['nomor_kamar'] ?? '');
$idtipe = (int)($_POST['idtipe'] ?? 0);
$status = $_POST['status'] ?? '';

if (!preg_match('/^[A-Za-z0-9\-]{1,10}$/', $nomor)) {
    kembali_dengan_pesan('Nomor kamar hanya boleh huruf, angka, dan tanda - (maksimal 10 karakter).');
}
if (!in_array($status, ['Aktif', 'Perbaikan'], true)) {
    kembali_dengan_pesan('Status tidak valid.');
}

$cek = mysqli_prepare($db, "SELECT 1 FROM kamar WHERE nomor_kamar = ? AND idkamar <> ?");
mysqli_stmt_bind_param($cek, 'si', $nomor, $id);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);
if (mysqli_stmt_num_rows($cek) > 0) {
    kembali_dengan_pesan('Nomor kamar sudah dipakai kamar lain.');
}
mysqli_stmt_close($cek);

// Kamar yang masih dipakai atau sudah dipesan tidak boleh dialihkan ke status Perbaikan
if ($status === 'Perbaikan') {
    $hari_ini = date('Y-m-d');
    $stmt = mysqli_prepare($db,
        "SELECT COUNT(*) FROM reservasi
         WHERE idkamar = ? AND status IN ('Dikonfirmasi', 'Check-in') AND check_out > ?");
    mysqli_stmt_bind_param($stmt, 'is', $id, $hari_ini);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $aktif);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if ($aktif > 0) {
        kembali_dengan_pesan('Kamar ini masih punya reservasi aktif/mendatang. Pindahkan atau batalkan reservasinya dulu.');
    }
}

$stmt = mysqli_prepare($db, "UPDATE kamar SET nomor_kamar = ?, idtipe = ?, status = ? WHERE idkamar = ?");
mysqli_stmt_bind_param($stmt, 'sisi', $nomor, $idtipe, $status, $id);
mysqli_stmt_execute($stmt);

header('Location: index.php?p=kamar');
exit;
