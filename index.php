<?php
require_once 'fungsi.php';
$judul = 'Resor Mewah & Sanctuary Pesisir';

$ci    = $_GET['ci'] ?? date('Y-m-d');
$co    = $_GET['co'] ?? date('Y-m-d', strtotime('+2 days'));
$tamu  = max(1, (int)($_GET['tamu'] ?? 2));

$tipe_semua = mysqli_fetch_all(
    mysqli_query($db, "SELECT * FROM tipe_kamar ORDER BY harga_malam ASC"), MYSQLI_ASSOC
);

require 'header.php';
?>

<div class="flex flex-col w-full -mt-20">
    <!-- 1. Immersive Cinematic Hero -->
    <section class="relative w-full overflow-hidden bg-[#0F172A]">
        <div class="relative w-full min-h-[680px] lg:min-h-[780px] flex items-center justify-center">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center scale-105" 
                 style="background-image: url('images/hero_resort.jpg');"></div>
            <!-- Gradient Overlays for Readability -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A] via-[#0F172A]/50 to-[#0F172A]/35"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#0F172A]/80 via-transparent to-[#0F172A]/40"></div>

            <!-- Hero Typography Content -->
            <div class="relative z-10 w-full max-w-[1440px] mx-auto px-6 lg:px-12 pt-36 pb-28 flex flex-col items-start justify-center">
                <!-- Prestige Overline Pill -->
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/15 backdrop-blur-md mb-6 shadow-sm border border-white/20">
                    <span class="w-2 h-2 rounded-full bg-[#FE932C] animate-pulse"></span>
                    <span class="text-xs uppercase tracking-[0.25em] text-[#FEF3C7] font-bold">Aurelia Sanctuary Collection</span>
                    <span class="text-white/40 text-xs">|</span>
                    <span class="text-xs tracking-wider text-white">Edisi 2025</span>
                </div>

                <h1 class="font-playfair text-4xl sm:text-5xl lg:text-6xl text-white max-w-4xl tracking-tight leading-[1.1] mb-6 drop-shadow-md">
                    Kemewahan Abadi di Setiap Sudut Dunia
                </h1>
                <p class="text-base sm:text-lg text-slate-200 max-w-2xl font-light leading-relaxed mb-10">
                    Selamat datang di naungan ketenangan tanpa batas. Nikmati keramahan eksklusif nan elegan, arsitektur megah yang harmonis dengan alam pantai Uluwatu, serta layanan butler personal yang melampaui segala ekspektasi Anda.
                </p>

                <!-- Trust Badges Bar in Hero -->
                <div class="flex flex-wrap items-center gap-6 pt-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center text-[#FE932C]">
                            <span class="material-symbols-outlined text-[20px] text-amber-400">star</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-white font-semibold">Bintang 5 Berlian</span>
                            <span class="text-xs text-slate-300">Penghargaan Forbes Travel Guide</span>
                        </div>
                    </div>
                    <div class="hidden sm:block w-px h-8 bg-white/20"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center text-[#FE932C]">
                            <span class="material-symbols-outlined text-[20px] text-amber-400">verified_user</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-white font-semibold">Jaminan Tarif Terbaik</span>
                            <span class="text-xs text-slate-300">Khusus Reservasi Situs Resmi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Interactive Floating Booking Bar Engine -->
    <section class="relative z-20 max-w-[1360px] w-full mx-auto px-4 sm:px-6 lg:px-8 -mt-16">
        <div class="w-full bg-white rounded-xl shadow-[0_24px_50px_-12px_rgba(11,28,48,0.14)] p-4 lg:p-6 border border-[#E2E8F0]">
            <!-- Quick Utility Header -->
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 mb-4 border-b border-[#F1F5F9]">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-[#B45309]">apartment</span>
                    <span class="text-xs uppercase tracking-wider font-bold text-[#0F172A]">Reservasi Langsung &amp; Akses Prioritas</span>
                </div>
                <div class="flex items-center gap-5 text-xs text-[#45464D]">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-[#FEF3C7] text-[#B45309] font-semibold">
                        <span class="material-symbols-outlined text-[14px]">workspace_premium</span>
                        Gratis Akses Executive Lounge &amp; Penjemputan VIP
                    </span>
                </div>
            </div>

            <!-- Booking Search Form -->
            <form action="kamar.php" method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-3 lg:gap-4 items-stretch">
                <!-- Field 1: Destination -->
                <div class="lg:col-span-4 bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors p-3.5 rounded-lg flex flex-col justify-center border border-[#E2E8F0]">
                    <span class="text-[11px] uppercase tracking-wider text-[#B45309] font-bold flex items-center gap-1 mb-1">
                        <span class="material-symbols-outlined text-[16px]">location_on</span>
                        Destinasi Resor
                    </span>
                    <div class="relative">
                        <select name="destinasi" class="w-full bg-transparent font-playfair text-base sm:text-lg text-[#0F172A] font-semibold appearance-none outline-none cursor-pointer pr-8">
                            <option value="bali">Bali Sanctuary Resort &amp; Spa, Uluwatu</option>
                            <option value="ubud">Ubud Forest Villa, Bali</option>
                            <option value="jakarta">Jakarta Luxury Suite, Senayan</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-0 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                    </div>
                    <span class="text-xs text-slate-500 mt-0.5 truncate">Indonesia &middot; Tepi Tebing Samudra Hindia</span>
                </div>

                <!-- Field 2: Check-in & Check-out -->
                <div class="lg:col-span-3 bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors p-3.5 rounded-lg flex flex-col justify-center border border-[#E2E8F0]">
                    <span class="text-[11px] uppercase tracking-wider text-[#B45309] font-bold flex items-center gap-1 mb-1">
                        <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                        Tanggal Menginap
                    </span>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] text-slate-500 block uppercase font-medium">Check-in</label>
                            <input type="date" name="ci" value="<?= e($ci) ?>" min="<?= date('Y-m-d') ?>" required
                                   class="w-full bg-transparent text-xs sm:text-sm font-semibold text-[#0F172A] outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-500 block uppercase font-medium">Check-out</label>
                            <input type="date" name="co" value="<?= e($co) ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required
                                   class="w-full bg-transparent text-xs sm:text-sm font-semibold text-[#0F172A] outline-none">
                        </div>
                    </div>
                </div>

                <!-- Field 3: Tamu -->
                <div class="lg:col-span-2 bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors p-3.5 rounded-lg flex flex-col justify-center border border-[#E2E8F0]">
                    <span class="text-[11px] uppercase tracking-wider text-[#B45309] font-bold flex items-center gap-1 mb-1">
                        <span class="material-symbols-outlined text-[16px]">group</span>
                        Kapasitas Tamu
                    </span>
                    <select name="tamu" class="w-full bg-transparent font-playfair text-base sm:text-lg text-[#0F172A] font-semibold appearance-none outline-none cursor-pointer">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?= $i ?>" <?= $tamu === $i ? 'selected' : '' ?>><?= $i ?> Orang Tamu</option>
                        <?php endfor; ?>
                    </select>
                    <span class="text-xs text-slate-500 mt-0.5">Termasuk Sarapan</span>
                </div>

                <!-- Field 4: Promo & CTA -->
                <div class="lg:col-span-3 flex flex-col sm:flex-row gap-2.5">
                    <div class="relative flex-1 bg-[#F8FAFC] rounded-lg p-3.5 flex flex-col justify-center border border-[#E2E8F0]">
                        <span class="text-[11px] uppercase tracking-wider text-slate-500 font-bold mb-0.5">Kode Promo</span>
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-[#B45309]">local_offer</span>
                            <input type="text" name="promo" placeholder="AURELIA2025" value="AURELIA2025" 
                                   class="w-full bg-transparent text-xs font-bold uppercase text-[#0F172A] outline-none placeholder:text-slate-400">
                        </div>
                    </div>
                    <button type="submit" class="px-6 py-4 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 group flex-shrink-0">
                        <span class="material-symbols-outlined text-[20px] transition-transform group-hover:scale-110 text-amber-400">search</span>
                        <span class="text-xs uppercase font-bold tracking-wider whitespace-nowrap">Cari Kamar</span>
                    </button>
                </div>
            </form>

            <!-- Bottom Value Assurance Micro-Bar -->
            <div class="mt-4 pt-3 flex flex-wrap items-center justify-between text-xs text-slate-600 gap-3 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                    <span>Harga Terendah Dijamin Langsung dari Resor Resmi. Pembatalan fleksibel hingga H-3.</span>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold text-[#B45309]">
                    <span>IDR &middot; Rupiah Termasuk Pajak &amp; Layanan Resor</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Section: Layanan Ikonik Unggulan -->
    <section id="layanan" class="max-w-[1440px] mx-auto px-6 lg:px-12 pt-28 pb-20 w-full">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 mb-16">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 mb-3">
                    <span class="h-px w-8 bg-[#B45309]"></span>
                    <span class="text-xs uppercase tracking-[0.2em] text-[#B45309] font-bold">Dedikasi Tanpa Kompromi</span>
                </div>
                <h2 class="font-playfair text-3xl sm:text-4xl text-[#0F172A] leading-tight font-semibold">
                    Layanan Ikonik untuk Kenyamanan Paripurna Anda
                </h2>
            </div>
            <p class="text-sm text-slate-600 max-w-md leading-relaxed">
                Setiap detik di Aurelia Hotels &amp; Resorts dirancang dengan kepedulian mendalam, memberikan kehangatan dan privasi absolut yang dipersonalisasi sesuai ritme hidup Anda.
            </p>
        </div>

        <!-- 4 Bento Feature Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Feature 1 -->
            <div class="group relative bg-white rounded-xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all flex flex-col justify-between overflow-hidden">
                <div>
                    <div class="w-14 h-14 rounded-xl bg-[#F8FAFC] group-hover:bg-[#0F172A] group-hover:text-white transition-colors flex items-center justify-center text-[#0F172A] mb-8 shadow-sm">
                        <span class="material-symbols-outlined text-[28px]">support_agent</span>
                    </div>
                    <span class="text-xs text-[#B45309] uppercase tracking-wider font-bold block mb-2">24 Jam Non-Stop</span>
                    <h3 class="font-playfair text-xl text-[#0F172A] font-semibold mb-3 group-hover:text-amber-700 transition-colors">
                        Concierge Pribadi 24 Jam
                    </h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Asisten pribadi siap mengurus rencana perjalanan, reservasi yacht, pemesanan helipad, hingga tur privat tersembunyi di Bali.
                    </p>
                </div>
                <div class="pt-8 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#0F172A] group-hover:translate-x-1 transition-transform">
                    <span>Akses Layanan</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="group relative bg-white rounded-xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all flex flex-col justify-between overflow-hidden">
                <div>
                    <div class="w-14 h-14 rounded-xl bg-[#F8FAFC] group-hover:bg-[#0F172A] group-hover:text-white transition-colors flex items-center justify-center text-[#0F172A] mb-8 shadow-sm">
                        <span class="material-symbols-outlined text-[28px]">restaurant</span>
                    </div>
                    <span class="text-xs text-[#B45309] uppercase tracking-wider font-bold block mb-2">Gastronomi Tinggi</span>
                    <h3 class="font-playfair text-xl text-[#0F172A] font-semibold mb-3 group-hover:text-amber-700 transition-colors">
                        Sarapan Gourmet &amp; Fine Dining
                    </h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Perjalanan rasa eksklusif kreasi chef terkemuka, memadukan bahan organik segar kualitas terbaik dengan tradisi kuliner kelas dunia.
                    </p>
                </div>
                <div class="pt-8 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#0F172A] group-hover:translate-x-1 transition-transform">
                    <span>Eksplorasi Restoran</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="group relative bg-white rounded-xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all flex flex-col justify-between overflow-hidden">
                <div>
                    <div class="w-14 h-14 rounded-xl bg-[#F8FAFC] group-hover:bg-[#0F172A] group-hover:text-white transition-colors flex items-center justify-center text-[#0F172A] mb-8 shadow-sm">
                        <span class="material-symbols-outlined text-[28px]">spa</span>
                    </div>
                    <span class="text-xs text-[#B45309] uppercase tracking-wider font-bold block mb-2">Holistic Vitality</span>
                    <h3 class="font-playfair text-xl text-[#0F172A] font-semibold mb-3 group-hover:text-amber-700 transition-colors">
                        Spa &amp; Wellness Kelas Dunia
                    </h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Perawatan tubuh holistik, ritual uap aromaterapi, hydrotherapy hangat, dan sesi yoga privat menghadap cakrawala samudra lepas.
                    </p>
                </div>
                <div class="pt-8 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#0F172A] group-hover:translate-x-1 transition-transform">
                    <span>Menu Relaksasi</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="group relative bg-white rounded-xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all flex flex-col justify-between overflow-hidden">
                <div>
                    <div class="w-14 h-14 rounded-xl bg-[#F8FAFC] group-hover:bg-[#0F172A] group-hover:text-white transition-colors flex items-center justify-center text-[#0F172A] mb-8 shadow-sm">
                        <span class="material-symbols-outlined text-[28px]">flight_takeoff</span>
                    </div>
                    <span class="text-xs text-[#B45309] uppercase tracking-wider font-bold block mb-2">Akses Cepat &amp; Privat</span>
                    <h3 class="font-playfair text-xl text-[#0F172A] font-semibold mb-3 group-hover:text-amber-700 transition-colors">
                        Helipad &amp; VIP Limousine
                    </h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Layanan penjemputan limusin Mercedes-Benz langsung dari tarmac bandara Ngurah Rai atau pendaratan helikopter pribadi di helipad resor.
                    </p>
                </div>
                <div class="pt-8 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#0F172A] group-hover:translate-x-1 transition-transform">
                    <span>Protokol Kedatangan</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Section: Pilihan Kamar & Suite Populer -->
    <section id="kamar" class="w-full bg-[#F8FAFC] py-24 border-t border-[#E2E8F0]">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-14">
                <div>
                    <div class="inline-flex items-center gap-2 mb-2">
                        <span class="h-px w-8 bg-[#B45309]"></span>
                        <span class="text-xs uppercase tracking-[0.2em] text-[#B45309] font-bold">Koleksi Terkurasi</span>
                    </div>
                    <h2 class="font-playfair text-3xl sm:text-4xl text-[#0F172A] font-semibold">
                        Kamar &amp; Private Suites Terbaik
                    </h2>
                    <p class="text-sm text-slate-600 mt-2">Didesain dengan presisi arsitektural untuk ketenangan dan kenyamanan optimal.</p>
                </div>
                <a href="kamar.php" class="inline-flex items-center gap-2 text-sm font-bold text-[#B45309] hover:underline self-start lg:self-auto">
                    <span>Lihat Semua Kamar &amp; Filter Detail</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>

            <!-- Dynamic Room Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($tipe_semua as $t): 
                    $foto_file = (!empty($t['foto']) && file_exists(__DIR__ . '/images/' . $t['foto'])) 
                                 ? 'images/' . $t['foto'] 
                                 : 'images/room_oceanfront.jpg';
                    $url_pesan = "pesan.php?" . http_build_query([
                        'tipe' => $t['idtipe'],
                        'ci'   => $ci,
                        'co'   => $co,
                        'tamu' => min($tamu, (int)$t['kapasitas'])
                    ]);
                ?>
                    <article class="bg-white rounded-xl border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden group">
                        <!-- Image Frame -->
                        <div class="relative h-56 w-full overflow-hidden bg-slate-100">
                            <img src="<?= e($foto_file) ?>" alt="<?= e($t['nama_tipe']) ?>" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute top-3 left-3 flex gap-2">
                                <span class="bg-[#0F172A]/85 backdrop-blur-sm text-white text-[11px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Favorit</span>
                                <span class="bg-[#FEF3C7] text-[#B45309] text-[11px] font-bold px-2 py-1 rounded-full flex items-center gap-0.5 shadow-sm">
                                    <span class="material-symbols-outlined text-[13px] text-amber-600">star</span> 4.96
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex flex-col justify-between flex-grow space-y-4">
                            <div>
                                <span class="text-[11px] uppercase tracking-wider text-[#B45309] font-bold block mb-1">Boutique Stays</span>
                                <h3 class="font-playfair text-xl text-[#0F172A] font-semibold group-hover:text-amber-700 transition-colors">
                                    <?= e($t['nama_tipe']) ?>
                                </h3>
                                
                                <!-- Meta specs -->
                                <div class="flex items-center gap-3 text-xs text-slate-500 mt-2">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">group</span>
                                        Maks. <?= (int)$t['kapasitas'] ?> Tamu
                                    </span>
                                    <span>&middot;</span>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">wifi</span>
                                        High-speed WiFi
                                    </span>
                                </div>

                                <p class="text-xs text-slate-600 mt-3 line-clamp-2 leading-relaxed">
                                    <?= e($t['deskripsi']) ?>
                                </p>
                            </div>

                            <!-- Price and CTA -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-lg font-bold text-[#0F172A] block leading-none">
                                        <?= e(rupiah($t['harga_malam'])) ?>
                                    </span>
                                    <span class="text-[11px] text-slate-500">per malam &middot; termasuk pajak</span>
                                </div>
                                <a href="<?= e($url_pesan) ?>" 
                                   class="px-4 py-2 rounded-lg bg-[#0F172A] hover:bg-amber-600 text-white text-xs font-bold uppercase tracking-wider transition-colors shadow-sm">
                                    Pesan
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 5. Prestige & Testimonial Quote -->
    <section class="max-w-[1440px] mx-auto px-6 lg:px-12 py-24 w-full">
        <div class="bg-[#0F172A] rounded-2xl p-8 lg:p-16 text-white text-center relative overflow-hidden shadow-2xl">
            <div class="max-w-3xl mx-auto space-y-6">
                <span class="text-xs uppercase tracking-[0.3em] text-[#FEF3C7] font-bold block">Ulasan Kehormatan Tamu</span>
                <blockquote class="font-playfair text-2xl lg:text-3xl font-light italic leading-relaxed text-slate-100">
                    &ldquo;Menginap di Aurelia adalah pengalaman yang mendefinisikan ulang kemewahan sejati. Dari sambutan butler hingga matahari terbenam di balkon suite, segalanya sempurna tanpa cela.&rdquo;
                </blockquote>
                <div class="pt-4">
                    <span class="font-semibold text-white block">Raden Mas Aryasuta</span>
                    <span class="text-xs text-amber-400">Anggota Kehormatan Aurelia Club &middot; Jakarta</span>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require 'footer.php'; ?>
