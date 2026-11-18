<?php
require_once 'fungsi.php';
$judul = 'Detail Pemesanan & Pembayaran';

$idtipe = (int)($_GET['tipe'] ?? 1);
$ci     = $_GET['ci'] ?? date('Y-m-d');
$co     = $_GET['co'] ?? date('Y-m-d', strtotime('+2 days'));
$tamu   = max(1, (int)($_GET['tamu'] ?? 2));

$stmt = mysqli_prepare($db, "SELECT * FROM tipe_kamar WHERE idtipe = ?");
mysqli_stmt_bind_param($stmt, 'i', $idtipe);
mysqli_stmt_execute($stmt);
$tipe = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$error = $tipe ? cek_tanggal_menginap($ci, $co) : 'Tipe kamar tidak ditemukan.';
if (!$error && $tamu > (int)$tipe['kapasitas']) {
    $error = 'Kapasitas tipe kamar ini maksimal ' . (int)$tipe['kapasitas'] . ' tamu.';
}
if (!$error && !kamar_kosong($db, $idtipe, $ci, $co)) {
    $error = 'Maaf, kamar tipe ini sudah penuh pada tanggal tersebut.';
}

require 'header.php';

if ($error) {
    echo '<div class="max-w-[1440px] mx-auto px-6 py-12"><div class="p-6 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl">';
    echo '<h3 class="font-bold text-lg mb-2">Mohon Maaf</h3><p>' . e($error) . '</p>';
    echo '<div class="mt-4"><a href="kamar.php" class="px-5 py-2.5 bg-[#0F172A] text-white rounded-lg text-xs font-bold uppercase tracking-wider inline-block">&larr; Pilih Kamar Lain</a></div>';
    echo '</div></div>';
    require 'footer.php';
    exit;
}

$malam = hitung_malam($ci, $co);
$harga_kamar = (int)$tipe['harga_malam'] * $malam;
$foto_path = (!empty($tipe['foto']) && file_exists(__DIR__ . '/images/' . $tipe['foto'])) 
             ? 'images/' . $tipe['foto'] 
             : 'images/room_oceanfront.jpg';
?>

<!-- 1. Top Stepper Header -->
<section class="w-full bg-white border-b border-[#E2E8F0] shadow-sm py-6 px-4 sm:px-6 lg:px-12">
    <div class="max-w-[1440px] mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.25em] text-[#B45309] block mb-1">Pemesanan Langsung Resmi</span>
                <h1 class="font-playfair text-3xl sm:text-4xl text-[#0F172A] font-semibold tracking-tight">Selesaikan Reservasi Anda</h1>
            </div>
            <div class="flex items-center gap-2 text-slate-600 bg-[#F8FAFC] border border-[#E2E8F0] px-3.5 py-1.5 rounded-full self-start md:self-auto text-xs font-medium">
                <span class="material-symbols-outlined text-[16px] text-[#B45309]">lock</span>
                <span>Sesi Terenkripsi 256-Bit SSL</span>
            </div>
        </div>

        <!-- 4-Step Progress Indicator -->
        <div class="grid grid-cols-4 gap-2 sm:gap-4 relative pt-2">
            <!-- Step 1 -->
            <div class="flex flex-col items-start group">
                <div class="flex items-center w-full gap-2 mb-2">
                    <div class="w-7 h-7 rounded-full bg-[#B45309] text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">check</span>
                    </div>
                    <div class="h-[2px] w-full bg-[#B45309] rounded-full"></div>
                </div>
                <span class="text-[10px] text-[#B45309] uppercase font-bold tracking-wider hidden sm:inline">Tahap 1</span>
                <span class="text-xs font-semibold text-[#0F172A] truncate">Pilih Kamar</span>
            </div>

            <!-- Step 2 (Active) -->
            <div class="flex flex-col items-start">
                <div class="flex items-center w-full gap-2 mb-2">
                    <div class="w-7 h-7 rounded-full bg-[#0F172A] text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-md ring-4 ring-[#0F172A]/10">
                        2
                    </div>
                    <div class="h-[2px] w-full bg-[#E2E8F0] rounded-full"></div>
                </div>
                <span class="text-[10px] text-[#B45309] uppercase font-bold tracking-wider hidden sm:inline">Tahap 2</span>
                <span class="text-xs font-bold text-[#0F172A] truncate">Detail &amp; Preferensi</span>
            </div>

            <!-- Step 3 -->
            <div class="flex flex-col items-start opacity-50">
                <div class="flex items-center w-full gap-2 mb-2">
                    <div class="w-7 h-7 rounded-full bg-[#F1F5F9] text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">
                        3
                    </div>
                    <div class="h-[2px] w-full bg-[#E2E8F0] rounded-full"></div>
                </div>
                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider hidden sm:inline">Tahap 3</span>
                <span class="text-xs font-medium text-slate-500 truncate">Pembayaran</span>
            </div>

            <!-- Step 4 -->
            <div class="flex flex-col items-start opacity-50">
                <div class="flex items-center w-full gap-2 mb-2">
                    <div class="w-7 h-7 rounded-full bg-[#F1F5F9] text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">
                        4
                    </div>
                    <div class="h-[2px] w-full bg-[#E2E8F0] rounded-full"></div>
                </div>
                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider hidden sm:inline">Tahap 4</span>
                <span class="text-xs font-medium text-slate-500 truncate">Konfirmasi</span>
            </div>
        </div>
    </div>
</section>

<!-- 2. Main Booking Interface -->
<section class="max-w-[1440px] mx-auto w-full px-4 sm:px-6 lg:px-12 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        <!-- LEFT COLUMN: Forms (7 of 12 cols) -->
        <div class="lg:col-span-7 flex flex-col gap-8">
            <!-- Privilege Badge Banner -->
            <div class="bg-gradient-to-r from-amber-700 via-amber-600 to-amber-800 text-white p-5 rounded-xl shadow-md flex items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0 text-[#FEF3C7]">
                        <span class="material-symbols-outlined text-[22px]">workspace_premium</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-[#FEF3C7]">Aurelia Signature Privilege Diterapkan</p>
                        <p class="text-xs text-white/90">Fasilitas Late Check-out Gratis hingga 15:00 WITA &amp; Prioritas Butler.</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold px-3 py-1 bg-white text-[#0F172A] rounded-full uppercase tracking-wider shrink-0">Aktif</span>
            </div>

            <!-- Reservation Form Start -->
            <form id="formPesan" method="post" action="pesan-proses.php" class="space-y-8">
                <input type="hidden" name="tipe" value="<?= $idtipe ?>">
                <input type="hidden" name="ci" value="<?= e($ci) ?>">
                <input type="hidden" name="co" value="<?= e($co) ?>">
                <input type="hidden" name="tamu" value="<?= $tamu ?>">
                <input type="hidden" name="total_addons" id="total_addons_input" value="0">

                <!-- 1. Informasi Tamu Utama -->
                <div class="bg-white rounded-xl p-6 sm:p-8 border border-[#E2E8F0] shadow-sm space-y-6">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-[#F1F5F9] text-[#0F172A] font-bold flex items-center justify-center text-xs">01</span>
                        <div>
                            <h2 class="font-playfair text-xl text-[#0F172A] font-semibold">Informasi Tamu Utama</h2>
                            <p class="text-xs text-slate-500">Sesuai dengan kartu identitas resmi (KTP / Paspor) yang akan ditunjukkan saat check-in.</p>
                        </div>
                    </div>

                    <!-- Salutation -->
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Panggilan / Salutation</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="salutation" value="Mr." checked class="peer sr-only">
                                <div class="py-2.5 text-center rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] peer-checked:bg-[#0F172A] peer-checked:text-white peer-checked:border-[#0F172A] text-xs font-bold text-slate-700 transition-all">
                                    Mr. (Tuan)
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="salutation" value="Mrs." class="peer sr-only">
                                <div class="py-2.5 text-center rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] peer-checked:bg-[#0F172A] peer-checked:text-white peer-checked:border-[#0F172A] text-xs font-bold text-slate-700 transition-all">
                                    Mrs. (Nyonya)
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="salutation" value="Ms." class="peer sr-only">
                                <div class="py-2.5 text-center rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] peer-checked:bg-[#0F172A] peer-checked:text-white peer-checked:border-[#0F172A] text-xs font-bold text-slate-700 transition-all">
                                    Ms. (Nona)
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- First & Last Name -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5" for="first-name">Nama Depan *</label>
                            <input type="text" name="first_name" id="first-name" required placeholder="Contoh: Raden" 
                                   class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-sm text-[#0F172A] font-semibold outline-none focus:border-[#0F172A] transition-all">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5" for="last-name">Nama Belakang *</label>
                            <input type="text" name="last_name" id="last-name" required placeholder="Contoh: Aryasuta" 
                                   class="w-full px-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-sm text-[#0F172A] font-semibold outline-none focus:border-[#0F172A] transition-all">
                        </div>
                    </div>

                    <!-- Email & WhatsApp -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5" for="email">Alamat Email *</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-3.5 text-slate-400 text-[18px]">mail</span>
                                <input type="email" name="email" id="email" required placeholder="nama@email.com" 
                                       class="w-full pl-11 pr-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-sm text-[#0F172A] font-semibold outline-none focus:border-[#0F172A] transition-all">
                            </div>
                            <span class="text-[11px] text-slate-400 mt-1 block">E-Voucher resmi instan dikirim ke alamat ini.</span>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5" for="whatsapp">Nomor WhatsApp Aktif *</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-3.5 text-slate-400 text-[18px]">phone</span>
                                <input type="tel" name="no_hp" id="whatsapp" required placeholder="08xxxxxxxxxx" 
                                       class="w-full pl-11 pr-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-sm text-[#0F172A] font-semibold outline-none focus:border-[#0F172A] transition-all">
                            </div>
                            <span class="text-[11px] text-slate-400 mt-1 block">Untuk koordinasi penjemputan &amp; butler pribadi.</span>
                        </div>
                    </div>

                    <!-- Aurelia Club ID -->
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5" for="club-id">
                            Nomor Keanggotaan Aurelia Club (Opsional)
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-3.5 text-[#B45309] text-[20px]">diamond</span>
                            <input type="text" name="club_id" id="club-id" placeholder="AUR-XXXX-ID" 
                                   class="w-full pl-11 pr-4 py-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-sm text-[#0F172A] font-semibold outline-none focus:border-[#B45309] transition-all">
                        </div>
                    </div>
                </div>

                <!-- 2. Preferensi & Permintaan Khusus -->
                <div class="bg-white rounded-xl p-6 sm:p-8 border border-[#E2E8F0] shadow-sm space-y-6">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-[#F1F5F9] text-[#0F172A] font-bold flex items-center justify-center text-xs">02</span>
                        <div>
                            <h2 class="font-playfair text-xl text-[#0F172A] font-semibold">Preferensi &amp; Permintaan Khusus</h2>
                            <p class="text-xs text-slate-500">Penyediaan disesuaikan dengan ketersediaan kamar saat kedatangan.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <label class="flex items-start gap-3 p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] cursor-pointer hover:bg-[#F1F5F9] transition-colors">
                            <input type="checkbox" name="pref[]" value="Early Check-in" checked class="mt-1 w-4 h-4 text-[#0F172A] accent-[#0F172A]">
                            <div>
                                <span class="text-xs font-bold text-[#0F172A] block">Check-in Lebih Awal (Early Check-in)</span>
                                <span class="text-[11px] text-slate-500">Perkiraan tiba pk 12.00 WITA</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] cursor-pointer hover:bg-[#F1F5F9] transition-colors">
                            <input type="checkbox" name="pref[]" value="High Floor" checked class="mt-1 w-4 h-4 text-[#0F172A] accent-[#0F172A]">
                            <div>
                                <span class="text-xs font-bold text-[#0F172A] block">Kamar Lantai Tinggi (High Floor)</span>
                                <span class="text-[11px] text-slate-500">Pemandangan samudra lepas</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] cursor-pointer hover:bg-[#F1F5F9] transition-colors">
                            <input type="checkbox" name="pref[]" value="Non-Smoking" checked class="mt-1 w-4 h-4 text-[#0F172A] accent-[#0F172A]">
                            <div>
                                <span class="text-xs font-bold text-[#0F172A] block">Bebas Asap Rokok (Non-Smoking)</span>
                                <span class="text-[11px] text-slate-500">Aromaterapi murni relaksasi</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] cursor-pointer hover:bg-[#F1F5F9] transition-colors">
                            <input type="checkbox" name="pref[]" value="Extra Bed" class="mt-1 w-4 h-4 text-[#0F172A] accent-[#0F172A]">
                            <div>
                                <span class="text-xs font-bold text-[#0F172A] block">Ranjang Ekstra (Extra Plush Bed)</span>
                                <span class="text-[11px] text-slate-500">Dikenakan biaya tambahan saat check-in</span>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5" for="catatan">Catatan Tambahan untuk Concierge</label>
                        <textarea name="catatan" id="catatan" rows="2" maxlength="255" placeholder="Informasikan alergi makanan, perayaan ulang tahun/anniversary, atau instruksi kedatangan khusus..." 
                                  class="w-full p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-xs text-[#0F172A] outline-none focus:border-[#0F172A] transition-all"></textarea>
                    </div>
                </div>

                <!-- 3. Layanan Tambahan Mewah (Add-ons) -->
                <div class="bg-white rounded-xl p-6 sm:p-8 border border-[#E2E8F0] shadow-sm space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-[#F1F5F9] text-[#0F172A] font-bold flex items-center justify-center text-xs">03</span>
                            <div>
                                <h2 class="font-playfair text-xl text-[#0F172A] font-semibold">Layanan Tambahan Mewah</h2>
                                <p class="text-xs text-slate-500">Sempurnakan masa inap Anda dengan pengalaman kurasi istimewa.</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#B45309] bg-[#FEF3C7] px-2.5 py-1 rounded">Bespoke</span>
                    </div>

                    <div class="space-y-4">
                        <!-- Addon 1 -->
                        <label class="flex items-start justify-between p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] cursor-pointer hover:bg-[#F1F5F9] transition-all">
                            <div class="flex items-start gap-3.5">
                                <input type="checkbox" name="addons[]" value="transfer" data-price="650000" onchange="hitungTotal()" 
                                       class="addon-check mt-1 w-4 h-4 text-[#B45309] accent-[#B45309]">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-[#0F172A]">Antar-Jemput Bandara VIP Mercedes-Benz S-Class</span>
                                        <span class="text-[9px] bg-[#0F172A] text-white px-1.5 py-0.5 rounded uppercase font-bold">Termasuk Butler</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Penjemputan tarmac eksklusif dari Bandara Ngurah Rai (DPS) langsung ke lobi resor.</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0 pl-4">
                                <span class="text-xs font-bold text-[#0F172A] block">+ Rp 650.000</span>
                                <span class="text-[10px] text-slate-400">Sekali jalan / 2 Tamu</span>
                            </div>
                        </label>

                        <!-- Addon 2 -->
                        <label class="flex items-start justify-between p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] cursor-pointer hover:bg-[#F1F5F9] transition-all">
                            <div class="flex items-start gap-3.5">
                                <input type="checkbox" name="addons[]" value="spa" data-price="1200000" onchange="hitungTotal()" 
                                       class="addon-check mt-1 w-4 h-4 text-[#B45309] accent-[#B45309]">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-[#0F172A]">Paket Spa &amp; Pijat Tradisional Bali (90 Menit)</span>
                                        <span class="text-[9px] bg-[#FEF3C7] text-[#B45309] px-1.5 py-0.5 rounded uppercase font-bold">Paling Diminati</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Relaksasi sepasang di atas tebing samudra menggunakan minyak herbal organik alami.</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0 pl-4">
                                <span class="text-xs font-bold text-[#0F172A] block">+ Rp 1.200.000</span>
                                <span class="text-[10px] text-slate-400">Untuk 2 Orang</span>
                            </div>
                        </label>

                        <!-- Addon 3 -->
                        <label class="flex items-start justify-between p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] cursor-pointer hover:bg-[#F1F5F9] transition-all">
                            <div class="flex items-start gap-3.5">
                                <input type="checkbox" name="addons[]" value="dinner" data-price="1800000" onchange="hitungTotal()" 
                                       class="addon-check mt-1 w-4 h-4 text-[#B45309] accent-[#B45309]">
                                <div>
                                    <span class="text-xs font-bold text-[#0F172A] block">Romantic Candlelight Dinner Tepi Pantai</span>
                                    <p class="text-[11px] text-slate-500 mt-0.5">5-course degustation menu kreasi Chef Eksekutif dengan pasangan anggur premium.</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0 pl-4">
                                <span class="text-xs font-bold text-[#0F172A] block">+ Rp 1.800.000</span>
                                <span class="text-[10px] text-slate-400">Untuk Pasangan</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 4. Pilihan Metode Pembayaran -->
                <div class="bg-white rounded-xl p-6 sm:p-8 border border-[#E2E8F0] shadow-sm space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-[#F1F5F9] text-[#0F172A] font-bold flex items-center justify-center text-xs">04</span>
                            <div>
                                <h2 class="font-playfair text-xl text-[#0F172A] font-semibold">Pilihan Metode Pembayaran</h2>
                                <p class="text-xs text-slate-500">Transaksi dilindungi enkripsi PCI-DSS Tingkat 1.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 text-emerald-600 text-xs font-bold">
                            <span class="material-symbols-outlined text-[16px]">verified_user</span>
                            <span>Aman 100%</span>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs text-slate-700">
                        <label class="flex items-center justify-between p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] cursor-pointer hover:bg-[#F1F5F9] transition-colors">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="metode_bayar" value="Transfer" checked class="w-4 h-4 text-[#0F172A] accent-[#0F172A]">
                                <div>
                                    <span class="font-bold text-[#0F172A] block">Transfer Bank &amp; DP 30% (Konfirmasi Cepat)</span>
                                    <span class="text-[11px] text-slate-500">BCA, Mandiri, BNI, BRI. Pelunasan sisa saat check-in.</span>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-slate-500 text-[20px]">account_balance</span>
                        </label>

                        <label class="flex items-center justify-between p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] cursor-pointer hover:bg-[#F1F5F9] transition-colors">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="metode_bayar" value="QRIS" class="w-4 h-4 text-[#0F172A] accent-[#0F172A]">
                                <div>
                                    <span class="font-bold text-[#0F172A] block">QRIS Instant Payment</span>
                                    <span class="text-[11px] text-slate-500">BCA Mobile, GoPay, OVO, ShopeePay, Dana</span>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-slate-500 text-[20px]">qr_code_scanner</span>
                        </label>

                        <label class="flex items-center justify-between p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] cursor-pointer hover:bg-[#F1F5F9] transition-colors">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="metode_bayar" value="Tunai" class="w-4 h-4 text-[#0F172A] accent-[#0F172A]">
                                <div>
                                    <span class="font-bold text-[#0F172A] block">Bayar Langsung di Resor (Front Desk)</span>
                                    <span class="text-[11px] text-slate-500">Pembayaran tunai / kartu saat kedatangan di lobi utama.</span>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-slate-500 text-[20px]">credit_card</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button on mobile -->
                <div class="block lg:hidden">
                    <button type="submit" class="w-full py-4 rounded-xl bg-[#0F172A] hover:bg-amber-600 text-white font-bold uppercase tracking-wider shadow-lg transition-all text-sm">
                        Konfirmasi &amp; Pesan Sekarang
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT COLUMN: Sticky Reservation Summary (5 of 12 cols) -->
        <div class="lg:col-span-5 sticky top-28">
            <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-md overflow-hidden p-6 space-y-6">
                <!-- Room Preview -->
                <div class="flex gap-4 items-center pb-5 border-b border-[#F1F5F9]">
                    <img src="<?= e($foto_path) ?>" alt="<?= e($tipe['nama_tipe']) ?>" class="w-20 h-20 rounded-lg object-cover shadow-sm bg-slate-100 flex-shrink-0">
                    <div>
                        <span class="text-[10px] font-bold text-[#B45309] uppercase tracking-wider block">Aurelia Resort &amp; Spa</span>
                        <h3 class="font-playfair text-lg font-semibold text-[#0F172A] leading-snug"><?= e($tipe['nama_tipe']) ?></h3>
                        <span class="text-xs text-slate-500"><?= $tamu ?> Orang Tamu &middot; 1 Kamar</span>
                    </div>
                </div>

                <!-- Stay Details -->
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Check-in</span>
                        <span class="font-bold text-[#0F172A]"><?= e(tanggal_indo($ci)) ?> (14.00 WITA)</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Check-out</span>
                        <span class="font-bold text-[#0F172A]"><?= e(tanggal_indo($co)) ?> (12.00 WITA)</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Durasi Menginap</span>
                        <span class="font-bold text-[#0F172A]"><?= $malam ?> Malam</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Tarif per Malam</span>
                        <span class="font-bold text-[#0F172A]"><?= e(rupiah($tipe['harga_malam'])) ?></span>
                    </div>
                </div>

                <!-- Price Breakdown Table -->
                <div class="pt-4 border-t border-[#F1F5F9] space-y-2 text-xs">
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Total Sewa Kamar (<?= $malam ?> malam)</span>
                        <span class="font-bold text-[#0F172A]" id="displayBiayaKamar"><?= e(rupiah($harga_kamar)) ?></span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Layanan Tambahan (Add-ons)</span>
                        <span class="font-bold text-[#0F172A]" id="displayAddons">Rp 0</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Pajak &amp; Biaya Layanan (21%)</span>
                        <span class="text-emerald-700 font-semibold">Termasuk</span>
                    </div>

                    <!-- Grand Total -->
                    <div class="pt-3 border-t border-slate-200 flex justify-between items-baseline">
                        <span class="text-sm font-bold text-[#0F172A]">Total Biaya</span>
                        <span class="font-playfair text-2xl font-bold text-[#0F172A]" id="displayTotal"><?= e(rupiah($harga_kamar)) ?></span>
                    </div>

                    <!-- DP Required -->
                    <div class="p-3 bg-[#FEF3C7] rounded-lg mt-3 text-xs text-[#663500] flex justify-between items-center">
                        <div>
                            <span class="font-bold block">DP Minimal <?= PERSEN_DP ?>%:</span>
                            <span class="text-[10px]">Wajib dibayar dalam <?= JAM_BATAS_BAYAR ?> jam</span>
                        </div>
                        <span class="font-bold text-sm text-[#B45309]" id="displayDp"><?= e(rupiah(dp_minimal($harga_kamar))) ?></span>
                    </div>
                </div>

                <!-- Submit Button Desktop -->
                <button type="button" onclick="document.getElementById('formPesan').submit()" 
                        class="hidden lg:block w-full py-4 rounded-xl bg-[#0F172A] hover:bg-amber-600 text-white font-bold uppercase tracking-wider shadow-lg transition-all text-sm">
                    Konfirmasi &amp; Pesan Sekarang
                </button>
            </div>
        </div>
    </div>
</section>

<script>
var baseKamarPrice = <?= (int)$harga_kamar ?>;
var persenDp = <?= (int)PERSEN_DP ?>;

function hitungTotal() {
    var addonsTotal = 0;
    document.querySelectorAll('.addon-check:checked').forEach(function(el) {
        addonsTotal += parseInt(el.getAttribute('data-price')) || 0;
    });

    var grandTotal = baseKamarPrice + addonsTotal;
    var dpMinimal = Math.ceil(grandTotal * persenDp / 100);

    document.getElementById('total_addons_input').value = addonsTotal;
    document.getElementById('displayAddons').innerText = 'Rp ' + addonsTotal.toLocaleString('id-ID');
    document.getElementById('displayTotal').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    document.getElementById('displayDp').innerText = 'Rp ' + dpMinimal.toLocaleString('id-ID');
}
</script>

<?php require 'footer.php'; ?>
