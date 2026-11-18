<?php
require_once __DIR__ . '/../fungsi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    $stmt = mysqli_prepare($db, "SELECT COUNT(*) FROM reservasi WHERE idkamar = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $jml);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($jml > 0) {
        pesan_lalu_ke('Kamar ini punya riwayat reservasi sehingga tidak bisa dihapus. Ubah statusnya menjadi Perbaikan.', 'index.php?p=kamar');
    }

    $stmt = mysqli_prepare($db, "DELETE FROM kamar WHERE idkamar = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}

header('Location: index.php?p=kamar');
exit;
