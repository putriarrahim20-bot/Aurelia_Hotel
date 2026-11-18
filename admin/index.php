<?php
session_start();
require_once __DIR__ . '/../fungsi.php';

if (empty($_SESSION['sesi'])) {
    header('Location: login.php');
    exit;
}

// Halaman yang boleh dibuka lewat ?p=...
$halaman_valid = [
    'dashboard', 'tipe', 'tipe-input', 'tipe-edit', 'kamar', 'kamar-edit',
    'reservasi', 'reservasi-input', 'reservasi-detail', 'laporan',
];
$p = $_GET['p'] ?? 'dashboard';

$menu = [
    'dashboard' => 'Dashboard',
    'reservasi' => 'Reservasi',
    'tipe'      => 'Tipe Kamar',
    'kamar'     => 'Data Kamar',
    'laporan'   => 'Laporan',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Admin | Aurelia Hotels &amp; Resorts</title>
    <link rel="stylesheet" href="../style.css?v=<?= filemtime(__DIR__ . '/../style.css') ?>">
</head>
<body class="admin">
<div class="admin-wrap">
    <aside class="admin-sidebar">
        <div class="admin-logo">
            AURELIA
            <small>Hotels &amp; Resorts &middot; Admin</small>
        </div>
        <div class="admin-user">Hai, <?= e($_SESSION['sesi']) ?>!</div>
        <?php foreach ($menu as $kunci => $label): ?>
            <a href="index.php?p=<?= $kunci ?>" class="<?= strpos($p, $kunci) === 0 ? 'aktif' : '' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
        <a href="../index.php" target="_blank">&rarr; Buka Situs Resor</a>
        <a href="logout.php">Keluar (Logout)</a>
    </aside>

    <section class="admin-isi">
        <?php
        if (in_array($p, $halaman_valid, true) && file_exists(__DIR__ . '/' . $p . '.php')) {
            include __DIR__ . '/' . $p . '.php';
        } else {
            echo '<div id="label-page"><h3>Halaman Tidak Ditemukan</h3></div>';
        }
        ?>
    </section>
</div>
</body>
</html>
