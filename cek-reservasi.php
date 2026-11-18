<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'fungsi.php';
$judul = 'Konfirmasi & Voucher Reservasi';

$kode  = strtoupper(trim($_POST['kode'] ?? $_GET['kode'] ?? ''));
$email = trim($_POST['email'] ?? '');
$rsv   = null;
$error = '';

if ($kode !== '') {
    $stmt = mysqli_prepare($db,
        "SELECT r.*, k.nomor_kamar, t.nama_tipe, t.deskripsi, t.harga_malam, t.foto
         FROM reservasi r
         JOIN kamar k ON k.idkamar = r.idkamar
         JOIN tipe_kamar t ON t.idtipe = k.idtipe
         WHERE r.kode_booking = ?");
    mysqli_stmt_bind_param($stmt, 's', $kode);
    mysqli_stmt_execute($stmt);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    $boleh = false;
    if ($r) {
        $boleh = !empty($_SESSION['akses_reservasi'][$kode]);
        if (!$boleh && $email !== '' && strcasecmp($email, $r['email']) === 0) {
            $boleh = true;
            $_SESSION['akses_reservasi'][$kode] = true;
        }
    }
    if ($boleh) {
        $rsv = $r;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = 'Kode booking atau alamat email tidak cocok.';
    }
}

$bayar = [];
if ($rsv) {
    $stmt = mysqli_prepare($db, "SELECT * FROM pembayaran WHERE idreservasi = ? ORDER BY tgl_bayar ASC");
    mysqli_stmt_bind_param($stmt, 'i', $rsv['idreservasi']);
    mysqli_stmt_execute($stmt);
    $bayar = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    $dibayar = array_sum(array_column($bayar, 'jumlah'));
    $sisa    = (int)$rsv['total'] - $dibayar;
    $kedaluwarsa = $rsv['status'] === 'Menunggu' && $rsv['tgl_pesan'] <= batas_menunggu();
    
    $foto_path = (!empty($rsv['foto']) && file_exists(__DIR__ . '/images/' . $rsv['foto'])) 
                 ? 'images/' . $rsv['foto'] 
                 : 'images/room_oceanfront.jpg';
}

require 'header.php';
?>

<div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 py-10">

<?php if (!$rsv): ?>
    <!-- 1. Look-up Booking Form -->
    <div class="max-w-xl mx-auto my-12 bg-white rounded-2xl border border-[#E2E8F0] shadow-xl p-8 space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-full bg-[#FEF3C7] text-[#B45309] flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-[28px]">search_check</span>
            </div>
            <span class="text-xs uppercase tracking-[0.25em] text-[#B45309] font-bold block">Portal Tamu Resmi</span>
            <h1 class="font-playfair text-3xl font-semibold text-[#0F172A]">Cek Status Reservasi</h1>
            <p class="text-xs text-slate-500">Masukkan 8 karakter kode booking dan email yang Anda gunakan saat memesan.</p>
        </div>

        <?php if ($error): ?>
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-xs flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-rose-600">error</span>
                <span><?= e($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="cek-reservasi.php" class="space-y-4">
            <div>
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Kode Booking Resmi *</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-3.5 text-slate-400 text-[18px]">confirmation_number</span>
                    <input type="text" name="kode" value="<?= e($kode) ?>" required maxlength="8" placeholder="Contoh: 7K9YAB2C" 
                           class="w-full pl-11 pr-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-sm text-[#0F172A] font-bold tracking-wider uppercase outline-none focus:border-[#0F172A] transition-all">
                </div>
            </div>

            <div>
                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Email Saat Memesan *</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-3.5 text-slate-400 text-[18px]">mail</span>
                    <input type="email" name="email" value="<?= e($email) ?>" required placeholder="nama@email.com" 
                           class="w-full pl-11 pr-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-sm text-[#0F172A] font-semibold outline-none focus:border-[#0F172A] transition-all">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-bold uppercase tracking-wider text-xs shadow-md transition-all">
                Cari &amp; Buka Voucher
            </button>
        </form>

        <p class="text-[11px] text-center text-slate-400 pt-2">
            Belum memiliki reservasi? <a href="kamar.php" class="text-[#B45309] font-bold hover:underline">Pilih Kamar &amp; Pesan Sekarang</a>
        </p>
    </div>

<?php else: ?>
    <!-- 2. Confirmed Booking & E-Voucher Presentation -->

    <!-- Header Status Sukses -->
    <div class="flex flex-col items-center text-center max-w-3xl mx-auto mb-10">
        <div class="relative flex items-center justify-center w-20 h-20 mb-5 rounded-full bg-[#FEF3C7] shadow-md">
            <span class="material-symbols-outlined text-[42px] text-[#B45309]">verified</span>
        </div>
        <span class="text-xs uppercase tracking-[0.25em] text-[#B45309] font-bold mb-2">
            Konfirmasi Pemesanan Resmi &middot; Aurelia Hotels
        </span>
        <h1 class="font-playfair text-3xl sm:text-4xl text-[#0F172A] font-semibold tracking-tight mb-3">
            Reservasi Anda Telah Tercatat!
        </h1>
        <p class="text-sm text-slate-600 max-w-2xl leading-relaxed">
            Terima kasih <span class="font-bold text-[#0F172A]"><?= e($rsv['nama_tamu']) ?></span>. Rincian voucher dan tanda terima elektronik telah dikonfirmasi untuk penginapan Anda di pesisir Uluwatu.
        </p>
    </div>

    <!-- Official Booking Code Banner -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-xl p-6 lg:p-8 mb-10 relative overflow-hidden">
        <div class="absolute top-0 left-0 h-1.5 w-full bg-gradient-to-r from-amber-600 via-amber-400 to-[#0F172A]"></div>
        <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-5">
                <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] text-[#B45309]">
                    <span class="material-symbols-outlined text-[36px]">confirmation_number</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 justify-center sm:justify-start">
                        <span class="text-[11px] uppercase tracking-widest text-slate-500 font-bold">Kode Booking Resmi</span>
                        <?= badge_status($rsv['status']) ?>
                    </div>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="font-playfair text-3xl font-bold tracking-wider text-[#0F172A] select-all" id="bookingCode">
                            <?= e($rsv['kode_booking']) ?>
                        </span>
                        <button onclick="salinKode()" class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#F1F5F9] hover:bg-slate-200 text-[#0F172A] text-xs font-semibold transition-all" title="Salin Kode">
                            <span class="material-symbols-outlined text-[16px]">content_copy</span>
                            <span id="copyText">Salin</span>
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Tunjukkan kode ini atau QR saat registrasi di meja lobi eksklusif.</p>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center justify-center gap-3 w-full lg:w-auto">
                <a href="cetak-voucher.php?kode=<?= urlencode($rsv['kode_booking']) ?>" target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider transition-all shadow-md">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Cetak E-Voucher PDF</span>
                </a>
                <a href="kamar.php" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-lg bg-[#F8FAFC] hover:bg-[#F1F5F9] border border-[#E2E8F0] text-[#0F172A] text-xs font-bold uppercase tracking-wider transition-all">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>Pesan Kamar Lain</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Grid Content: Left 8 cols, Right 4 cols -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-16">
        
        <!-- Left Column: Stay Details & Finance (8 cols) -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Room & Resort Banner Card -->
            <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-sm overflow-hidden">
                <div class="relative w-full h-64 sm:h-80 bg-slate-100">
                    <img src="<?= e($foto_path) ?>" alt="<?= e($rsv['nama_tipe']) ?>" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A]/85 via-[#0F172A]/30 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6 text-white flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-1 text-amber-400 mb-1">
                                <span class="material-symbols-outlined text-[16px]">star</span>
                                <span class="material-symbols-outlined text-[16px]">star</span>
                                <span class="material-symbols-outlined text-[16px]">star</span>
                                <span class="material-symbols-outlined text-[16px]">star</span>
                                <span class="material-symbols-outlined text-[16px]">star</span>
                                <span class="text-[11px] font-bold tracking-wider text-slate-200 ml-1">Resor Mewah Bintang 5</span>
                            </div>
                            <h2 class="font-playfair text-2xl sm:text-3xl font-bold text-white">Aurelia Sanctuary Resort &amp; Spa Bali</h2>
                            <p class="text-xs text-slate-300 flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-[16px]">location_on</span>
                                Jl. Pantai Selatan No. 88, Uluwatu, Bali 80361
                            </p>
                        </div>
                        <span class="px-3 py-1.5 rounded-lg bg-white/20 backdrop-blur-md text-xs font-semibold self-start sm:self-auto">
                            Nomor Unit: <b><?= in_array($rsv['status'], ['Dikonfirmasi', 'Check-in', 'Check-out']) ? e($rsv['nomor_kamar']) : 'Dialokasikan saat Check-in' ?></b>
                        </span>
                    </div>
                </div>

                <!-- Check-in & Check-out Grid -->
                <div class="p-6 bg-[#F8FAFC] border-b border-[#E2E8F0] grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-white border border-[#E2E8F0] shadow-sm">
                        <div class="p-3 rounded-lg bg-[#FEF3C7] text-[#B45309]">
                            <span class="material-symbols-outlined text-[24px]">login</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Waktu Check-in</span>
                            <span class="font-playfair text-lg font-bold text-[#0F172A] mt-0.5 block"><?= e(tanggal_indo($rsv['check_in'])) ?></span>
                            <span class="text-xs text-slate-500">Mulai pukul 14.00 WITA</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 rounded-xl bg-white border border-[#E2E8F0] shadow-sm">
                        <div class="p-3 rounded-lg bg-[#F1F5F9] text-[#0F172A]">
                            <span class="material-symbols-outlined text-[24px]">logout</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Waktu Check-out</span>
                            <span class="font-playfair text-lg font-bold text-[#0F172A] mt-0.5 block"><?= e(tanggal_indo($rsv['check_out'])) ?></span>
                            <span class="text-xs text-slate-500">Sebelum pukul 12.00 WITA (<?= (int)$rsv['malam'] ?> Malam)</span>
                        </div>
                    </div>
                </div>

                <!-- Registered Parameters -->
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-5 rounded-xl bg-[#FDFCFB] border border-[#E2E8F0]">
                            <span class="text-[11px] font-bold text-[#B45309] uppercase tracking-wider flex items-center gap-1.5 mb-2">
                                <span class="material-symbols-outlined text-[16px]">bed</span>
                                Tipe Kamar
                            </span>
                            <h3 class="font-playfair text-xl font-bold text-[#0F172A]"><?= e($rsv['nama_tipe']) ?></h3>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed"><?= e($rsv['deskripsi']) ?></p>
                            <div class="flex flex-wrap gap-2 mt-3 text-[10px] font-bold text-slate-600">
                                <span class="px-2 py-1 rounded bg-white border border-[#E2E8F0]">King Bed</span>
                                <span class="px-2 py-1 rounded bg-white border border-[#E2E8F0]">Oceanfront</span>
                                <span class="px-2 py-1 rounded bg-white border border-[#E2E8F0]">Free Gourmet Breakfast</span>
                            </div>
                        </div>

                        <div class="p-5 rounded-xl bg-[#FDFCFB] border border-[#E2E8F0]">
                            <span class="text-[11px] font-bold text-[#B45309] uppercase tracking-wider flex items-center gap-1.5 mb-2">
                                <span class="material-symbols-outlined text-[16px]">group</span>
                                Data Tamu Terdaftar
                            </span>
                            <h3 class="font-playfair text-xl font-bold text-[#0F172A]"><?= e($rsv['nama_tamu']) ?></h3>
                            <p class="text-xs text-slate-600 mt-1">Total: <b><?= (int)$rsv['jumlah_tamu'] ?> Tamu Dewasa</b></p>
                            <p class="text-xs text-slate-500 mt-0.5">WhatsApp / No. HP: <?= e($rsv['no_hp']) ?></p>
                            <p class="text-xs text-slate-500 mt-0.5">Email: <?= e($rsv['email']) ?></p>
                            <?php if (!empty($rsv['catatan'])): ?>
                                <div class="mt-3 p-2.5 rounded bg-white border border-[#E2E8F0] text-[11px] text-slate-600">
                                    <b>Catatan Tamu:</b> <?= e($rsv['catatan']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial & Payment Status Card -->
            <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-[#F1F5F9]">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-[#B45309]">payments</span>
                        <h3 class="font-playfair text-lg text-[#0F172A] font-semibold">Rincian Keuangan &amp; Pembayaran</h3>
                    </div>
                    <span class="text-xs text-slate-500">ID Reservasi: #<?= (int)$rsv['idreservasi'] ?></span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Total Biaya</span>
                        <span class="font-playfair text-lg sm:text-xl font-bold text-[#0F172A] mt-1 block"><?= e(rupiah($rsv['total'])) ?></span>
                    </div>
                    <div class="p-4 rounded-xl bg-[#FEF3C7] border border-amber-200">
                        <span class="text-[10px] uppercase font-bold text-[#663500] block">DP Minimal (30%)</span>
                        <span class="font-playfair text-lg sm:text-xl font-bold text-[#B45309] mt-1 block"><?= e(rupiah(dp_minimal($rsv['total']))) ?></span>
                    </div>
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                        <span class="text-[10px] uppercase font-bold text-emerald-800 block">Sudah Dibayar</span>
                        <span class="font-playfair text-lg sm:text-xl font-bold text-emerald-700 mt-1 block"><?= e(rupiah($dibayar)) ?></span>
                    </div>
                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Sisa Pelunasan</span>
                        <span class="font-playfair text-lg sm:text-xl font-bold text-[#0F172A] mt-1 block"><?= e(rupiah($sisa)) ?></span>
                    </div>
                </div>

                <!-- Status Alerts -->
                <?php if ($rsv['status'] === 'Menunggu'): ?>
                    <?php if ($kedaluwarsa): ?>
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                            <span class="font-bold flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px]">warning</span>
                                Batas Waktu Pembayaran Telah Lewat
                            </span>
                            <p>Batas waktu pembayaran DP telah melewati <?= JAM_BATAS_BAYAR ?> jam, sehingga kamar tidak lagi ditahan. Silakan hubungi staf reservasi atau buat reservasi baru.</p>
                        </div>
                    <?php else: ?>
                        <div class="p-5 rounded-xl bg-[#FEF3C7] border border-amber-300 text-[#663500] text-xs space-y-2">
                            <span class="font-bold text-sm flex items-center gap-1.5 text-[#B45309]">
                                <span class="material-symbols-outlined text-[20px]">info</span>
                                Menunggu Pembayaran DP Minimal
                            </span>
                            <p class="leading-relaxed">
                                Mohon lakukan transfer DP minimal <b><?= e(rupiah(dp_minimal($rsv['total']))) ?></b> sebelum 
                                <b><?= e(date('d F Y, H:i', strtotime($rsv['tgl_pesan']) + JAM_BATAS_BAYAR * 3600)) ?> WITA</b>.
                            </p>
                            <div class="p-3 bg-white/80 rounded-lg border border-amber-200 font-mono text-xs text-[#0F172A] space-y-1">
                                <div><b>BCA:</b> 8829-0192-88 (a.n. PT Aurelia Resort Indonesia)</div>
                                <div><b>Mandiri:</b> 137-00-882910-1 (a.n. PT Aurelia Resort Indonesia)</div>
                            </div>
                            <p class="text-[11px] text-[#B45309]">
                                Setelah mentransfer, konfirmasi melalui WhatsApp ke <b>+62 812-3456-7890</b> atau hubungi Front Desk dengan menyebutkan kode booking Anda.
                            </p>
                        </div>
                    <?php endif; ?>
                <?php elseif ($rsv['status'] === 'Batal'): ?>
                    <div class="p-4 rounded-xl bg-slate-100 border border-slate-300 text-slate-700 text-xs">
                        <b>Status:</b> Reservasi ini telah dibatalkan.
                    </div>
                <?php else: ?>
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between">
                        <span class="flex items-center gap-2 font-bold">
                            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
                            Reservasi ini telah terkonfirmasi resmi.
                        </span>
                        <a href="cetak-voucher.php?kode=<?= urlencode($rsv['kode_booking']) ?>" target="_blank"
                           class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-bold uppercase tracking-wider text-[11px]">
                            Cetak Voucher
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Payment History Table -->
                <?php if ($bayar): ?>
                    <div class="pt-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-3">Riwayat Pembayaran Diterima</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead>
                                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-slate-500 uppercase tracking-wider text-[10px]">
                                        <th class="py-2.5 px-3">Tanggal</th>
                                        <th class="py-2.5 px-3">Jumlah</th>
                                        <th class="py-2.5 px-3">Metode</th>
                                        <th class="py-2.5 px-3">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bayar as $b): ?>
                                        <tr class="border-b border-[#F1F5F9]">
                                            <td class="py-2.5 px-3"><?= e(date('d-m-Y H:i', strtotime($b['tgl_bayar']))) ?></td>
                                            <td class="py-2.5 px-3 font-bold text-[#0F172A]"><?= e(rupiah($b['jumlah'])) ?></td>
                                            <td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700"><?= e($b['metode']) ?></span></td>
                                            <td class="py-2.5 px-3 text-slate-600"><?= e($b['keterangan']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Digital Pass & QR (4 cols) -->
        <div class="lg:col-span-4 space-y-6 sticky top-28">
            <!-- Digital Pass Card -->
            <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-xl p-6 text-center space-y-4 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-2 bg-[#0F172A]"></div>
                
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#B45309] block pt-2">Aurelia Digital Pass</span>
                
                <!-- Simulated QR Code SVG -->
                <div class="p-4 bg-[#F8FAFC] rounded-xl border border-dashed border-[#E2E8F0] inline-block shadow-inner">
                    <svg class="w-40 h-40 mx-auto" viewBox="0 0 100 100" fill="#0F172A">
                        <!-- Corners -->
                        <rect x="5" y="5" width="26" height="26" fill="#0F172A" rx="3"/>
                        <rect x="9" y="9" width="18" height="18" fill="white" rx="2"/>
                        <rect x="13" y="13" width="10" height="10" fill="#0F172A" rx="1"/>
                        
                        <rect x="69" y="5" width="26" height="26" fill="#0F172A" rx="3"/>
                        <rect x="73" y="9" width="18" height="18" fill="white" rx="2"/>
                        <rect x="77" y="13" width="10" height="10" fill="#0F172A" rx="1"/>
                        
                        <rect x="5" y="69" width="26" height="26" fill="#0F172A" rx="3"/>
                        <rect x="9" y="73" width="18" height="18" fill="white" rx="2"/>
                        <rect x="13" y="77" width="10" height="10" fill="#0F172A" rx="1"/>

                        <!-- Pattern Blocks -->
                        <rect x="36" y="8" width="6" height="6"/>
                        <rect x="46" y="8" width="8" height="6"/>
                        <rect x="58" y="14" width="6" height="8"/>
                        <rect x="36" y="20" width="8" height="8"/>
                        <rect x="48" y="24" width="6" height="6"/>
                        <rect x="8" y="36" width="8" height="8"/>
                        <rect x="20" y="42" width="6" height="6"/>
                        <rect x="36" y="36" width="12" height="12"/>
                        <rect x="54" y="38" width="8" height="8"/>
                        <rect x="68" y="36" width="6" height="10"/>
                        <rect x="80" y="40" width="10" height="6"/>
                        <rect x="38" y="56" width="8" height="8"/>
                        <rect x="52" y="52" width="14" height="6"/>
                        <rect x="72" y="54" width="8" height="10"/>
                        <rect x="86" y="68" width="6" height="14"/>
                        <rect x="38" y="72" width="8" height="8"/>
                        <rect x="54" y="68" width="12" height="6"/>
                        <rect x="48" y="82" width="10" height="8"/>
                        <rect x="68" y="76" width="8" height="12"/>
                    </svg>
                </div>

                <div class="text-xs text-slate-500">
                    <span class="font-bold text-[#0F172A] block"><?= e($rsv['kode_booking']) ?></span>
                    <span>Tunjukkan saat tiba di meja VIP Concierge.</span>
                </div>

                <div class="pt-2 border-t border-[#F1F5F9] text-left text-xs text-slate-600 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-[#B45309]">schedule</span>
                        <span>Check-in 14.00 &middot; Check-out 12.00</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-[#B45309]">badge</span>
                        <span>Wajib bawa KTP / Paspor asli pemesan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-[#B45309]">call</span>
                        <span>Hotline Butler: +62 (361) 892-8888</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function salinKode() {
        var kode = document.getElementById('bookingCode').innerText.trim();
        navigator.clipboard.writeText(kode).then(function() {
            var el = document.getElementById('copyText');
            el.innerText = 'Tersalin!';
            setTimeout(function() { el.innerText = 'Salin'; }, 2000);
        });
    }
    </script>
<?php endif; ?>

</div>

<?php require 'footer.php'; ?>
