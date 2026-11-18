<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'fungsi.php';

$kode = strtoupper(trim($_GET['kode'] ?? ''));
if ($kode === '') {
    header('Location: cek-reservasi.php');
    exit;
}

// Check authorization: from session, or admin session
$is_admin = !empty($_SESSION['sesi']);
$is_guest = !empty($_SESSION['akses_reservasi'][$kode]);
if (!$is_admin && !$is_guest) {
    header('Location: cek-reservasi.php?kode=' . urlencode($kode));
    exit;
}

$stmt = mysqli_prepare($db,
    "SELECT r.*, k.nomor_kamar, t.nama_tipe, t.deskripsi
     FROM reservasi r
     JOIN kamar k ON k.idkamar = r.idkamar
     JOIN tipe_kamar t ON t.idtipe = k.idtipe
     WHERE r.kode_booking = ?");
mysqli_stmt_bind_param($stmt, 's', $kode);
mysqli_stmt_execute($stmt);
$r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$r) {
    die('Reservasi dengan kode tersebut tidak ditemukan.');
}

$dibayar = total_dibayar($db, $r['idreservasi']);
$sisa    = (int)$r['total'] - $dibayar;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voucher Resmi <?= e($r['kode_booking']) ?> | Aurelia Hotels &amp; Resorts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            color: #0F172A;
            padding: 30px 15px;
            font-size: 13px;
            line-height: 1.5;
        }
        .container {
            max-width: 760px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }
        .header-voucher {
            background: #0F172A;
            color: #fff;
            padding: 24px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #B45309;
        }
        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .brand-sub {
            font-size: 10px;
            font-weight: 700;
            color: #FEF3C7;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .badge-status {
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: #FEF3C7;
            color: #B45309;
        }
        .body-voucher {
            padding: 30px;
        }
        .booking-banner {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .booking-code {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 3px;
            color: #0F172A;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 700;
            color: #0F172A;
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 6px;
            margin: 20px 0 12px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        .box-info {
            background: #FDFCFB;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 14px;
        }
        .label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748B;
            margin-bottom: 2px;
            display: block;
        }
        .value {
            font-size: 14px;
            font-weight: 600;
            color: #0F172A;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 9px 12px;
            border-bottom: 1px solid #E2E8F0;
            text-align: left;
        }
        th {
            background: #F8FAFC;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748B;
        }
        .text-right { text-align: right; }
        .text-bold { font-weight: 700; }
        .total-row td {
            font-size: 15px;
            font-weight: 700;
            color: #0F172A;
            background: #F8FAFC;
            border-top: 2px solid #0F172A;
        }
        .footer-note {
            margin-top: 24px;
            padding: 14px;
            background: #FEF3C7;
            border-radius: 8px;
            font-size: 11px;
            color: #78350F;
            line-height: 1.6;
        }
        .signature-row {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .sign-box {
            width: 200px;
        }
        .sign-line {
            margin-top: 50px;
            border-top: 1px dashed #94A3B8;
            padding-top: 4px;
            font-size: 11px;
            color: #64748B;
        }
        .btn-bar {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 24px;
            background: #0F172A;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            border: none;
            margin: 0 4px;
        }
        .btn-secondary {
            background: #E2E8F0;
            color: #0F172A;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .container { border: none; box-shadow: none; max-width: 100%; border-radius: 0; }
            .btn-bar { display: none !important; }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="header-voucher">
        <div>
            <div class="brand-title">AURELIA</div>
            <div class="brand-sub">Hotels &amp; Resorts &middot; Bali</div>
        </div>
        <div>
            <span class="badge-status"><?= e($r['status']) ?></span>
        </div>
    </div>

    <!-- Body -->
    <div class="body-voucher">
        <!-- Booking Code Banner -->
        <div class="booking-banner">
            <div>
                <span class="label">Kode Booking Resmi</span>
                <span class="booking-code"><?= e($r['kode_booking']) ?></span>
            </div>
            <div class="text-right">
                <span class="label">Tanggal Diterbitkan</span>
                <span class="value"><?= date('d F Y, H:i') ?> WITA</span>
            </div>
        </div>

        <!-- Resort & Guest Info -->
        <div class="grid-2">
            <div class="box-info">
                <span class="label">Properti &amp; Destinasi</span>
                <div class="value">Aurelia Sanctuary Resort &amp; Spa</div>
                <div style="font-size:11px; color:#64748B; margin-top:2px;">
                    Jl. Pantai Selatan No. 88, Uluwatu, Bali 80361<br>
                    Tel: +62 (361) 892-8888
                </div>
            </div>

            <div class="box-info">
                <span class="label">Tamu Utama Terdaftar</span>
                <div class="value"><?= e($r['nama_tamu']) ?></div>
                <div style="font-size:11px; color:#64748B; margin-top:2px;">
                    Email: <?= e($r['email']) ?><br>
                    No. HP: <?= e($r['no_hp']) ?>
                </div>
            </div>
        </div>

        <!-- Stay Details -->
        <div class="section-title">Detail Reservasi Menginap</div>
        <div class="grid-2">
            <div class="box-info">
                <span class="label">Tipe Kamar</span>
                <div class="value"><?= e($r['nama_tipe']) ?></div>
                <div style="font-size:11px; color:#64748B; margin-top:2px;">
                    Unit: <b><?= in_array($r['status'], ['Dikonfirmasi', 'Check-in', 'Check-out']) ? 'Nomor ' . e($r['nomor_kamar']) : 'Dialokasikan saat Check-in' ?></b> &middot; <?= (int)$r['jumlah_tamu'] ?> Tamu
                </div>
            </div>

            <div class="box-info">
                <span class="label">Jadwal Menginap (<?= (int)$r['malam'] ?> Malam)</span>
                <div class="value">
                    <?= e(tanggal_indo($r['check_in'])) ?> &ndash; <?= e(tanggal_indo($r['check_out'])) ?>
                </div>
                <div style="font-size:11px; color:#64748B; margin-top:2px;">
                    Check-in: 14.00 WITA &middot; Check-out: 12.00 WITA
                </div>
            </div>
        </div>

        <?php if (!empty($r['catatan'])): ?>
            <div class="box-info" style="margin-bottom:16px;">
                <span class="label">Preferensi &amp; Catatan Khusus</span>
                <div style="font-size:12px; color:#334155;"><?= e($r['catatan']) ?></div>
            </div>
        <?php endif; ?>

        <!-- Financial Breakdown Table -->
        <div class="section-title">Rincian Pembayaran</div>
        <table>
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th class="text-right">Durasi</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <b><?= e($r['nama_tipe']) ?></b><br>
                        <small style="color:#64748B;">Termasuk Sarapan Gourmet &amp; Akses Fasilitas Resor</small>
                    </td>
                    <td class="text-right"><?= (int)$r['malam'] ?> Malam</td>
                    <td class="text-right text-bold"><?= e(rupiah($r['total'])) ?></td>
                </tr>
                <tr class="total-row">
                    <td colspan="2">TOTAL BIAYA</td>
                    <td class="text-right"><?= e(rupiah($r['total'])) ?></td>
                </tr>
                <tr>
                    <td colspan="2" style="color:#059669; font-weight:600;">Jumlah Telah Dibayar</td>
                    <td class="text-right" style="color:#059669; font-weight:700;"><?= e(rupiah($dibayar)) ?></td>
                </tr>
                <tr>
                    <td colspan="2" style="font-weight:700;">Sisa Pembayaran (Pelunasan saat Check-in/out)</td>
                    <td class="text-right text-bold" style="color:#B45309;"><?= e(rupiah($sisa)) ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Notice -->
        <div class="footer-note">
            <b>Ketentuan Registrasi &amp; Check-in:</b><br>
            1. Harap menunjukkan voucher ini dan kartu identitas fisik resmi (KTP / Paspor) saat proses registrasi di meja concierge.<br>
            2. Jam check-in standar dimulai pukul 14.00 WITA dan check-out maksimal pukul 12.00 WITA.<br>
            3. Bagi reservasi yang berstatus "Menunggu", reservasi akan dikonfirmasi secara otomatis setelah minimal DP 30% diterima.
        </div>

        <!-- Signature -->
        <div class="signature-row">
            <div class="sign-box">
                <div class="sign-line">Tanda Tangan Tamu Utama</div>
            </div>
            <div class="sign-box">
                <div class="sign-line">Manajemen Resor &amp; Concierge</div>
            </div>
        </div>
    </div>
</div>

<div class="btn-bar">
    <button class="btn" onclick="window.print()">Cetak / Simpan PDF</button>
    <a href="cek-reservasi.php?kode=<?= urlencode($r['kode_booking']) ?>" class="btn btn-secondary">&larr; Kembali</a>
</div>

</body>
</html>
