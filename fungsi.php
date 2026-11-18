<?php
// Fungsi bantu yang dipakai halaman publik maupun admin.
require_once __DIR__ . '/koneksi.php';

const DIR_FOTO = __DIR__ . '/images/';
const JAM_BATAS_BAYAR = 24;   // reservasi "Menunggu" yang belum dikonfirmasi setelah 24 jam tidak lagi menahan kamar
const PERSEN_DP = 30;         // DP minimal (persen dari total) agar reservasi bisa dikonfirmasi

// ---------- Tampilan ----------
function e($teks)
{
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

function rupiah($angka)
{
    return 'Rp ' . number_format((int)$angka, 0, ',', '.');
}

function tanggal_indo($tanggal)
{
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $t = strtotime($tanggal);
    return date('j', $t) . ' ' . $bulan[(int)date('n', $t)] . ' ' . date('Y', $t);
}

function badge_status($status)
{
    $kelas = [
        'Menunggu'     => 'badge-menunggu',
        'Dikonfirmasi' => 'badge-info',
        'Check-in'     => 'badge-lunas',
        'Aktif'        => 'badge-lunas',
        'Check-out'    => 'badge-abu',
        'Batal'        => 'badge-batal',
        'Perbaikan'    => 'badge-menunggu',
    ];
    return '<span class="badge ' . ($kelas[$status] ?? '') . '">' . e($status) . '</span>';
}

function tampil_paginasi($jml_hal, $hal, $param)
{
    if ($jml_hal <= 1) {
        return;
    }
    echo '<div class="pagination">';
    for ($i = 1; $i <= $jml_hal; $i++) {
        $url = 'index.php?' . http_build_query(array_merge($param, ['hal' => $i]));
        if ($i === $hal) {
            echo '<a class="active">' . $i . '</a>';
        } else {
            echo '<a href="' . e($url) . '">' . $i . '</a>';
        }
    }
    echo '</div>';
}

// ---------- Pesan & login ----------
function kembali_dengan_pesan($pesan)
{
    echo '<script>alert(' . json_encode($pesan) . '); history.back();</script>';
    exit;
}

function pesan_lalu_ke($pesan, $url)
{
    echo '<script>alert(' . json_encode($pesan) . '); location.href=' . json_encode($url) . ';</script>';
    exit;
}

function wajib_login()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['sesi'])) {
        header('Location: login.php');
        exit;
    }
}

// ---------- Utilitas ----------
function angka_tunggal($db, $sql)
{
    $baris = mysqli_fetch_row(mysqli_query($db, $sql));
    return (int)$baris[0];
}

function kode_acak($panjang)
{
    $huruf = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $kode  = '';
    for ($i = 0; $i < $panjang; $i++) {
        $kode .= $huruf[random_int(0, strlen($huruf) - 1)];
    }
    return $kode;
}

function tanggal_valid($tanggal)
{
    $d = DateTime::createFromFormat('Y-m-d', (string)$tanggal);
    return $d && $d->format('Y-m-d') === $tanggal;
}

function hitung_malam($check_in, $check_out)
{
    return (int)(new DateTime($check_in))->diff(new DateTime($check_out))->days;
}

// Reservasi "Menunggu" yang dibuat sebelum waktu ini dianggap kedaluwarsa (tidak menahan kamar)
function batas_menunggu()
{
    return date('Y-m-d H:i:s', time() - JAM_BATAS_BAYAR * 3600);
}

function dp_minimal($total)
{
    return (int)ceil($total * PERSEN_DP / 100);
}

function total_dibayar($db, $idreservasi)
{
    $stmt = mysqli_prepare($db, "SELECT COALESCE(SUM(jumlah), 0) FROM pembayaran WHERE idreservasi = ?");
    mysqli_stmt_bind_param($stmt, 'i', $idreservasi);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $jumlah);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return (int)$jumlah;
}

// ---------- Ketersediaan kamar ----------
// Dua rentang [ci, co) bentrok jika ci_lama < co_baru DAN co_lama > ci_baru.
// Yang menahan kamar: Dikonfirmasi, Check-in, dan Menunggu yang belum kedaluwarsa.

// Daftar kamar aktif bertipe $idtipe yang kosong pada rentang tanggal tersebut
function kamar_kosong($db, $idtipe, $check_in, $check_out)
{
    $batas = batas_menunggu();
    $stmt = mysqli_prepare($db,
        "SELECT k.idkamar, k.nomor_kamar
         FROM kamar k
         WHERE k.idtipe = ? AND k.status = 'Aktif'
           AND NOT EXISTS (
               SELECT 1 FROM reservasi r
               WHERE r.idkamar = k.idkamar
                 AND r.check_in < ? AND r.check_out > ?
                 AND (r.status IN ('Dikonfirmasi', 'Check-in')
                      OR (r.status = 'Menunggu' AND r.tgl_pesan > ?))
           )
         ORDER BY k.nomor_kamar");
    mysqli_stmt_bind_param($stmt, 'isss', $idtipe, $check_out, $check_in, $batas);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $hasil;
}

// Jumlah reservasi lain yang bentrok pada satu kamar ($kecuali = id reservasi yang diabaikan)
function kamar_bentrok($db, $idkamar, $check_in, $check_out, $kecuali)
{
    $batas = batas_menunggu();
    $stmt = mysqli_prepare($db,
        "SELECT COUNT(*) FROM reservasi r
         WHERE r.idkamar = ? AND r.idreservasi <> ?
           AND r.check_in < ? AND r.check_out > ?
           AND (r.status IN ('Dikonfirmasi', 'Check-in')
                OR (r.status = 'Menunggu' AND r.tgl_pesan > ?))");
    mysqli_stmt_bind_param($stmt, 'iisss', $idkamar, $kecuali, $check_out, $check_in, $batas);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $jumlah);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return (int)$jumlah;
}

// Validasi tanggal menginap. Return pesan error, atau '' jika valid.
function cek_tanggal_menginap($check_in, $check_out)
{
    if (!tanggal_valid($check_in) || !tanggal_valid($check_out)) {
        return 'Tanggal tidak valid.';
    }
    if ($check_in < date('Y-m-d')) {
        return 'Tanggal check-in tidak boleh di masa lalu.';
    }
    if ($check_out <= $check_in) {
        return 'Tanggal check-out harus setelah tanggal check-in.';
    }
    if (hitung_malam($check_in, $check_out) > 30) {
        return 'Maksimal menginap 30 malam per reservasi.';
    }
    return '';
}

// Membuat reservasi. Kamar dipilih otomatis (nomor terkecil yang kosong).
// Return: kode booking | false (alasan di $error)
function buat_reservasi($db, $idtipe, $check_in, $check_out, $tamu, $nama, $email, $hp, $catatan, $status_awal, &$error, $biaya_tambahan = 0)
{
    $error = cek_tanggal_menginap($check_in, $check_out);
    if ($error !== '') {
        return false;
    }
    if ($nama === '' || mb_strlen($nama) > 100) {
        $error = 'Nama tamu wajib diisi (maksimal 100 karakter).';
        return false;
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
        return false;
    }
    if (!preg_match('/^[0-9+\-\s]{8,20}$/', $hp)) {
        $error = 'No. HP tidak valid.';
        return false;
    }
    if ($tamu < 1) {
        $error = 'Jumlah tamu minimal 1.';
        return false;
    }
    $catatan = mb_substr($catatan, 0, 255);

    $stmt = mysqli_prepare($db, "SELECT harga_malam, kapasitas FROM tipe_kamar WHERE idtipe = ?");
    mysqli_stmt_bind_param($stmt, 'i', $idtipe);
    mysqli_stmt_execute($stmt);
    $tipe = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$tipe) {
        $error = 'Tipe kamar tidak ditemukan.';
        return false;
    }
    if ($tamu > (int)$tipe['kapasitas']) {
        $error = 'Kapasitas tipe kamar ini maksimal ' . (int)$tipe['kapasitas'] . ' tamu.';
        return false;
    }

    $malam = hitung_malam($check_in, $check_out);
    $total = ((int)$tipe['harga_malam'] * $malam) + (int)$biaya_tambahan;

    // Transaksi: kunci semua kamar tipe ini supaya dua pemesan tidak mendapat kamar yang sama.
    // Pernyataan pertama di transaksi harus pembacaan terkunci ini.
    mysqli_begin_transaction($db);
    $stmt = mysqli_prepare($db,
        "SELECT idkamar FROM kamar WHERE idtipe = ? AND status = 'Aktif' ORDER BY nomor_kamar FOR UPDATE");
    mysqli_stmt_bind_param($stmt, 'i', $idtipe);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $kosong = kamar_kosong($db, $idtipe, $check_in, $check_out);
    if (!$kosong) {
        mysqli_rollback($db);
        $error = 'Maaf, kamar tipe ini sudah penuh pada tanggal tersebut.';
        return false;
    }
    $idkamar = (int)$kosong[0]['idkamar'];

    $tgl  = date('Y-m-d H:i:s');
    $kode = '';
    $stmt = mysqli_prepare($db,
        "INSERT INTO reservasi (kode_booking, nama_tamu, email, no_hp, idkamar, check_in, check_out,
                                jumlah_tamu, malam, total, status, catatan, tgl_pesan)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ok = false;
    for ($coba = 0; $coba < 5 && !$ok; $coba++) {
        $kode = kode_acak(8);
        mysqli_stmt_bind_param($stmt, 'ssssissiiisss',
            $kode, $nama, $email, $hp, $idkamar, $check_in, $check_out,
            $tamu, $malam, $total, $status_awal, $catatan, $tgl);
        $ok = mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
    if (!$ok) {
        mysqli_rollback($db);
        $error = 'Gagal menyimpan reservasi. Silakan coba lagi.';
        return false;
    }
    mysqli_commit($db);
    return $kode;
}

// ---------- Foto tipe kamar ----------
// Return: nama file | null (tidak ada file) | false (gagal, pesan di $error)
function simpan_foto($file, $nama_dasar, &$error)
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Upload foto gagal.";
        return false;
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        $error = "Ukuran foto maksimal 2 MB.";
        return false;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
        $error = "Format foto harus JPG, PNG, atau GIF.";
        return false;
    }
    if (@getimagesize($file['tmp_name']) === false) {
        $error = "File yang diupload bukan gambar yang valid.";
        return false;
    }
    $nama = $nama_dasar . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], DIR_FOTO . $nama)) {
        $error = "Foto gagal disimpan. Pastikan folder images/ ada dan bisa ditulisi.";
        return false;
    }
    return $nama;
}

function hapus_foto($nama)
{
    if (!empty($nama) && $nama !== '-') {
        $path = DIR_FOTO . basename($nama);
        if (is_file($path)) {
            unlink($path);
        }
    }
}

// Batalkan transaksi database lalu tampilkan pesan dan pindah halaman
function gagal_transaksi($db, $pesan, $url)
{
    mysqli_rollback($db);
    pesan_lalu_ke($pesan, $url);
}
