<?php
require_once 'fungsi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$idtipe       = (int)($_POST['tipe'] ?? 0);
$ci           = $_POST['ci'] ?? '';
$co           = $_POST['co'] ?? '';
$tamu         = (int)($_POST['tamu'] ?? 1);
$salutation   = trim($_POST['salutation'] ?? '');
$first_name   = trim($_POST['first_name'] ?? '');
$last_name    = trim($_POST['last_name'] ?? '');
$nama         = trim($salutation . ' ' . $first_name . ' ' . $last_name);
if ($nama === '') {
    $nama = trim($_POST['nama'] ?? '');
}

$email        = trim($_POST['email'] ?? '');
$no_hp        = trim($_POST['no_hp'] ?? '');
$club_id      = trim($_POST['club_id'] ?? '');
$user_note    = trim($_POST['catatan'] ?? '');
$biaya_addon  = (int)($_POST['total_addons'] ?? 0);
$metode_bayar = trim($_POST['metode_bayar'] ?? 'Transfer');

if ($email === '') {
    kembali_dengan_pesan('Alamat email wajib diisi.');
}
if ($nama === '') {
    kembali_dengan_pesan('Nama tamu wajib diisi.');
}

// Assemble detailed notes for concierge & hotel admin
$note_parts = [];
if (!empty($club_id)) {
    $note_parts[] = "Aurelia Club: " . $club_id;
}
if (!empty($_POST['pref']) && is_array($_POST['pref'])) {
    $note_parts[] = "Preferensi: " . implode(', ', array_map('strip_tags', $_POST['pref']));
}
if (!empty($_POST['addons']) && is_array($_POST['addons'])) {
    $note_parts[] = "Add-on: " . implode(', ', array_map('strip_tags', $_POST['addons']));
}
if (!empty($user_note)) {
    $note_parts[] = "Pesan: " . $user_note;
}
$catatan = implode(' | ', $note_parts);

$error = '';
$kode  = buat_reservasi($db, $idtipe, $ci, $co, $tamu, $nama, $email, $no_hp, $catatan, 'Menunggu', $error, $biaya_addon);
if ($kode === false) {
    kembali_dengan_pesan($error);
}

// Izinkan tamu melihat reservasinya tanpa mengetik email lagi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['akses_reservasi'][$kode] = true;
$_SESSION['metode_terpilih'][$kode] = $metode_bayar;

header('Location: cek-reservasi.php?kode=' . urlencode($kode));
exit;
