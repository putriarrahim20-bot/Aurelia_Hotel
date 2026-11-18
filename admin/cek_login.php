<?php
// Ubah ke false setelah login berhasil agar pesan error tidak membocorkan detail
const LOGIN_DEBUG = true;

session_start();
require_once __DIR__ . '/../fungsi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

function login_gagal($kode)
{
    header('Location: login.php?error=' . (LOGIN_DEBUG ? $kode : '1'));
    exit;
}

$user = trim($_POST['user'] ?? '');
$pass = $_POST['pass'] ?? '';

$stmt = mysqli_prepare($db, "SELECT id_admin, username, nm_admin, password FROM adminH WHERE username = ?");
if (!$stmt) {
    // Biasanya karena tabel adminH belum ada di database dbhotel
    die('Query login gagal: ' . e(mysqli_error($db)) .
        '<br>Pastikan tabel <b>adminH</b> ada di database <b>dbhotel</b> ' .
        '(import ulang database.sql, atau jalankan: RENAME TABLE admin TO adminH;)');
}
mysqli_stmt_bind_param($stmt, 's', $user);
mysqli_stmt_execute($stmt);
$admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$admin) {
    login_gagal('user');                       // username tidak ada di tabel
}
if (empty(password_get_info($admin['password'])['algo'])) {
    login_gagal('hash');                       // password di database bukan hash (teks biasa / terpotong)
}
if (!password_verify($pass, $admin['password'])) {
    login_gagal('pass');                       // password tidak cocok
}

session_regenerate_id(true);
$_SESSION['id_admin'] = $admin['id_admin'];
// Nama tampilan tidak boleh kosong/"0", karena halaman admin memeriksa $_SESSION['sesi'] dengan empty()
$nama = trim((string)$admin['nm_admin']);
$_SESSION['sesi'] = ($nama === '' || $nama === '0') ? 'Admin' : $nama;

header('Location: index.php');
exit;
