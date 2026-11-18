<?php
require_once __DIR__ . '/../fungsi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    $stmt = mysqli_prepare($db, "SELECT COUNT(*) FROM kamar WHERE idtipe = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $jml_kamar);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($jml_kamar > 0) {
        pesan_lalu_ke('Tipe ini masih memiliki kamar. Hapus atau pindahkan kamarnya terlebih dahulu.', 'index.php?p=tipe');
    }

    $stmt = mysqli_prepare($db, "SELECT foto FROM tipe_kamar WHERE idtipe = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $t = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($t) {
        $stmt = mysqli_prepare($db, "DELETE FROM tipe_kamar WHERE idtipe = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        hapus_foto($t['foto']);
    }
}

header('Location: index.php?p=tipe');
exit;
