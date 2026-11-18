<?php
require_once 'fungsi.php';
$judul = 'Koleksi Kamar & Private Suites';

$ci    = $_GET['ci'] ?? date('Y-m-d');
$co    = $_GET['co'] ?? date('Y-m-d', strtotime('+2 days'));
$tamu  = max(1, (int)($_GET['tamu'] ?? 2));
$urut  = $_GET['urut'] ?? 'rekomendasi';
$max_harga = (int)($_GET['max_harga'] ?? 20000000);

$error = cek_tanggal_menginap($ci, $co);
$malam = $error === '' ? hitung_malam($ci, $co) : 2;

// Query room types
$sql_order = "harga_malam ASC";
if ($urut === 'harga_desc') {
    $sql_order = "harga_malam DESC";
} elseif ($urut === 'kapasitas') {
    $sql_order = "kapasitas DESC";
}

$stmt = mysqli_prepare($db, "SELECT * FROM tipe_kamar ORDER BY $sql_order");
mysqli_stmt_execute($stmt);
$tipe_semua = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

require 'header.php';
?>

<!-- 1. Search Summary Floating Strip -->
<section class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 py-6">
    <div class="bg-white border border-[#E2E8F0] shadow-md rounded-xl p-4 lg:p-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-y-3 gap-x-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#FEF3C7] flex items-center justify-center text-[#B45309]">
                    <span class="material-symbols-outlined text-[20px]">location_on</span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-[#B45309] uppercase tracking-wider block">Destinasi Terpilih</span>
                    <h2 class="font-playfair text-lg text-[#0F172A] font-semibold leading-tight">Bali Sanctuary Resort &amp; Spa, Uluwatu</h2>
                </div>
            </div>
            <div class="hidden sm:block w-px h-8 bg-slate-200"></div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#F1F5F9] flex items-center justify-center text-slate-600">
                    <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Durasi Menginap</span>
                    <p class="text-sm font-bold text-[#0F172A]"><?= e(tanggal_indo($ci)) ?> &ndash; <?= e(tanggal_indo($co)) ?> 
                       <span class="text-[#B45309] font-normal">(<?= $malam ?> Malam)</span></p>
                </div>
            </div>
            <div class="hidden sm:block w-px h-8 bg-slate-200"></div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#F1F5F9] flex items-center justify-center text-slate-600">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Kapasitas Tamu</span>
                    <p class="text-sm font-bold text-[#0F172A]"><?= $tamu ?> Orang Tamu Dewasa</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
            <button onclick="document.getElementById('search-edit-drawer').classList.toggle('hidden')" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#F8FAFC] hover:bg-[#F1F5F9] border border-[#E2E8F0] text-[#0F172A] text-xs font-bold uppercase tracking-wider transition-colors w-full sm:w-auto justify-center">
                <span class="material-symbols-outlined text-[18px]">tune</span>
                Ubah Pencarian
            </button>
        </div>
    </div>

    <!-- Collapsible Search Edit Drawer -->
    <div id="search-edit-drawer" class="hidden mt-4 bg-white p-6 rounded-xl border border-[#E2E8F0] shadow-xl transition-all">
        <form method="get" action="kamar.php" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-[11px] uppercase tracking-wider text-slate-500 font-bold block mb-1.5">Destinasi / Resor</label>
                <div class="flex items-center gap-2 px-3 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg">
                    <span class="material-symbols-outlined text-[18px] text-[#B45309]">apartment</span>
                    <input type="text" value="Bali Sanctuary Resort &amp; Spa, Uluwatu" readonly class="bg-transparent text-xs font-semibold text-[#0F172A] w-full outline-none">
                </div>
            </div>
            <div>
                <label class="text-[11px] uppercase tracking-wider text-slate-500 font-bold block mb-1.5">Tanggal Check-in</label>
                <div class="flex items-center gap-2 px-3 py-2 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg">
                    <input type="date" name="ci" value="<?= e($ci) ?>" min="<?= date('Y-m-d') ?>" required class="bg-transparent text-xs font-semibold text-[#0F172A] w-full outline-none">
                </div>
            </div>
            <div>
                <label class="text-[11px] uppercase tracking-wider text-slate-500 font-bold block mb-1.5">Tanggal Check-out</label>
                <div class="flex items-center gap-2 px-3 py-2 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg">
                    <input type="date" name="co" value="<?= e($co) ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required class="bg-transparent text-xs font-semibold text-[#0F172A] w-full outline-none">
                </div>
            </div>
            <div>
                <label class="text-[11px] uppercase tracking-wider text-slate-500 font-bold block mb-1.5">Jumlah Tamu</label>
                <div class="flex items-center gap-2">
                    <select name="tamu" class="flex-1 px-3 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-xs font-semibold text-[#0F172A] outline-none">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?= $i ?>" <?= $tamu === $i ? 'selected' : '' ?>><?= $i ?> Orang</option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-[#0F172A] hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition-colors">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Error notification if any -->
<?php if ($error): ?>
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12 mb-4">
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
            <span><?= e($error) ?></span>
        </div>
    </div>
<?php endif; ?>

<!-- 2. Editorial Header Title & Meta -->
<section class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pt-2 pb-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs uppercase tracking-[0.2em] text-[#B45309] font-bold">Sanctuary Portfolio</span>
                <span class="w-8 h-px bg-[#B45309]"></span>
                <span class="text-xs text-slate-500"><?= count($tipe_semua) ?> Kategori Tersedia</span>
            </div>
            <h1 class="font-playfair text-3xl sm:text-4xl text-[#0F172A] font-semibold">Koleksi Kamar &amp; Private Suites</h1>
        </div>

        <!-- Sorting dropdown -->
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-500 font-medium">Urutkan:</span>
            <div class="relative inline-block">
                <form id="sortForm" method="get">
                    <input type="hidden" name="ci" value="<?= e($ci) ?>">
                    <input type="hidden" name="co" value="<?= e($co) ?>">
                    <input type="hidden" name="tamu" value="<?= $tamu ?>">
                    <select name="urut" onchange="document.getElementById('sortForm').submit()" 
                            class="bg-white border border-[#E2E8F0] text-[#0F172A] text-xs font-semibold px-4 py-2 pr-9 rounded-lg appearance-none shadow-sm cursor-pointer outline-none">
                        <option value="rekomendasi" <?= $urut === 'rekomendasi' ? 'selected' : '' ?>>Rekomendasi Utama</option>
                        <option value="harga_asc" <?= $urut === 'harga_asc' ? 'selected' : '' ?>>Harga: Rendah ke Tinggi</option>
                        <option value="harga_desc" <?= $urut === 'harga_desc' ? 'selected' : '' ?>>Harga: Tinggi ke Rendah</option>
                        <option value="kapasitas" <?= $urut === 'kapasitas' ? 'selected' : '' ?>>Kapasitas Tamu Terbesar</option>
                    </select>
                </form>
                <span class="material-symbols-outlined absolute right-2.5 top-2 pointer-events-none text-[18px] text-slate-400">expand_more</span>
            </div>
        </div>
    </div>
</section>

<!-- 3. Main Content: Filter + Rooms -->
<section class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Precision Filter Aside -->
        <aside class="lg:col-span-4 xl:col-span-3 space-y-6">
            <div class="bg-white p-6 rounded-xl border border-[#E2E8F0] shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-[#F1F5F9]">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-[#B45309]">filter_list</span>
                        <h3 class="font-playfair text-lg text-[#0F172A] font-semibold">Filter Presisi</h3>
                    </div>
                    <a href="kamar.php?ci=<?= e($ci) ?>&co=<?= e($co) ?>&tamu=<?= $tamu ?>" class="text-[11px] font-bold text-[#B45309] hover:underline uppercase tracking-wider">Reset</a>
                </div>

                <!-- Price Range Slider -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-[#0F172A]">Maks. Tarif / Malam</span>
                        <span id="priceDisplay" class="text-xs font-bold text-[#B45309]">Rp 20 Jt+</span>
                    </div>
                    <input type="range" id="priceRange" min="2000000" max="20000000" step="500000" value="20000000" 
                           oninput="document.getElementById('priceDisplay').innerText = 'Rp ' + (this.value / 1000000).toFixed(1) + ' Jt'; filterCards(this.value);"
                           class="w-full accent-[#0F172A] cursor-pointer h-1.5 bg-slate-200 rounded-lg">
                    <div class="flex justify-between text-[11px] text-slate-400">
                        <span>Rp 2 Jt</span>
                        <span>Rp 20 Jt+</span>
                    </div>
                </div>

                <div class="w-full h-px bg-[#F1F5F9]"></div>

                <!-- Bed Configuration -->
                <div class="space-y-3">
                    <span class="text-xs font-bold text-[#0F172A] block">Konfigurasi Tempat Tidur</span>
                    <div class="space-y-2 text-xs text-slate-700">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="bed_filter" checked class="text-[#0F172A] accent-[#0F172A]">
                            <span>Semua Pilihan Ranjang</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="bed_filter" class="text-[#0F172A] accent-[#0F172A]">
                            <span>King Bed Plush (180x200)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="bed_filter" class="text-[#0F172A] accent-[#0F172A]">
                            <span>Twin Bed (2 Ranjang Terpisah)</span>
                        </label>
                    </div>
                </div>

                <div class="w-full h-px bg-[#F1F5F9]"></div>

                <!-- Features & Amenities -->
                <div class="space-y-3">
                    <span class="text-xs font-bold text-[#0F172A] block">Fasilitas Unggulan</span>
                    <div class="space-y-2 text-xs text-slate-700">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" checked class="text-[#0F172A] accent-[#0F172A] rounded">
                            <span>Private Infinity Pool</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" checked class="text-[#0F172A] accent-[#0F172A] rounded">
                            <span>Bathtub Marmer Mewah</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" checked class="text-[#0F172A] accent-[#0F172A] rounded">
                            <span>Pemandangan Samudra Hindia</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" checked class="text-[#0F172A] accent-[#0F172A] rounded">
                            <span>Sarapan Buffet Gourmet Gratis</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Concierge Assistance Callout -->
            <div class="bg-[#0F172A] text-white p-6 rounded-xl shadow-md space-y-3 relative overflow-hidden">
                <span class="text-[10px] uppercase tracking-wider text-[#FEF3C7] font-bold block">Bespoke Concierge</span>
                <h4 class="font-playfair text-lg text-white font-semibold">Butuh Rekomendasi Khusus?</h4>
                <p class="text-xs text-slate-300 leading-relaxed">Spesialis reservasi kami siap mengkurasi vila privat atau suite impian Anda di Uluwatu 24 jam non-stop.</p>
                <a href="tel:+623618928888" class="inline-flex items-center gap-2 text-xs font-bold text-[#FEF3C7] hover:underline pt-2">
                    <span class="material-symbols-outlined text-[16px]">call</span>
                    +62 (361) 892-8888
                </a>
            </div>
        </aside>

        <!-- Right Column: Room Cards List -->
        <main class="lg:col-span-8 xl:col-span-9 space-y-6">
            <?php foreach ($tipe_semua as $t): 
                $kosong_units = count(kamar_kosong($db, $t['idtipe'], $ci, $co));
                $muat_tamu    = $tamu <= (int)$t['kapasitas'];
                $foto_path    = (!empty($t['foto']) && file_exists(__DIR__ . '/images/' . $t['foto']))
                                ? 'images/' . $t['foto'] 
                                : 'images/room_oceanfront.jpg';
                $total_malam  = (int)$t['harga_malam'] * $malam;
                $url_pesan    = "pesan.php?" . http_build_query([
                    'tipe' => $t['idtipe'],
                    'ci'   => $ci,
                    'co'   => $co,
                    'tamu' => $tamu
                ]);
            ?>
                <article data-price="<?= (int)$t['harga_malam'] ?>" 
                         class="room-card bg-white rounded-xl border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col xl:flex-row overflow-hidden group">
                    
                    <!-- Image Showcase -->
                    <div class="relative xl:w-2/5 h-64 xl:h-auto min-h-[260px] overflow-hidden bg-slate-100 flex-shrink-0">
                        <img src="<?= e($foto_path) ?>" alt="<?= e($t['nama_tipe']) ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute top-4 left-4 flex gap-2">
                            <span class="bg-[#0F172A]/85 backdrop-blur-sm text-white text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Favorit Tamu</span>
                            <span class="bg-[#FEF3C7] text-[#B45309] text-[11px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-[14px] text-amber-600">star</span> 4.96
                            </span>
                        </div>
                    </div>

                    <!-- Room Details Content -->
                    <div class="p-6 xl:w-3/5 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-[11px] uppercase tracking-wider text-[#B45309] font-bold">Sanctuary Suites &middot; Wing Samudra</span>
                                <span class="text-xs text-slate-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px] text-amber-600">verified</span> 100% Terverifikasi
                                </span>
                            </div>

                            <h3 class="font-playfair text-2xl text-[#0F172A] font-semibold group-hover:text-amber-700 transition-colors">
                                <?= e($t['nama_tipe']) ?>
                            </h3>

                            <!-- Specifications Pills -->
                            <div class="flex flex-wrap items-center gap-y-1 gap-x-3 text-slate-600 text-xs mt-2.5">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px] text-slate-400">group</span>
                                    Maks. <?= (int)$t['kapasitas'] ?> Tamu
                                </span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px] text-slate-400">bed</span>
                                    King Plush Bed
                                </span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px] text-slate-400">water</span>
                                    Ocean View
                                </span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px] text-slate-400">hot_tub</span>
                                    Jacuzzi Teras
                                </span>
                            </div>

                            <!-- Inclusions Box -->
                            <div class="mt-4 p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="flex items-center gap-2 text-xs font-semibold text-[#0F172A]">
                                    <span class="material-symbols-outlined text-[18px] text-[#B45309]">restaurant</span>
                                    <span>Termasuk Sarapan Gourmet &amp; Afternoon Tea</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#B45309] bg-[#FEF3C7] px-2 py-0.5 rounded">
                                    Akses Lounge
                                </span>
                            </div>

                            <p class="text-xs text-slate-600 mt-3 leading-relaxed">
                                <?= e($t['deskripsi']) ?>
                            </p>
                        </div>

                        <!-- Price & Action Row -->
                        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                            <div>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-2xl font-bold text-[#0F172A] leading-none">
                                        <?= e(rupiah($t['harga_malam'])) ?>
                                    </span>
                                    <span class="text-xs text-slate-500">/ malam</span>
                                </div>
                                <span class="text-[11px] text-slate-500 block mt-1">
                                    Total <?= $malam ?> Malam: <b class="text-[#0F172A]"><?= e(rupiah($total_malam)) ?></b> (sudah termasuk pajak)
                                </span>
                                <?php if (!$muat_tamu): ?>
                                    <span class="inline-block mt-1 text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded">Kapasitas kurang untuk <?= $tamu ?> tamu</span>
                                <?php elseif ($kosong_units > 0): ?>
                                    <span class="inline-block mt-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Tersedia <?= $kosong_units ?> unit kamar</span>
                                <?php else: ?>
                                    <span class="inline-block mt-1 text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">Penuh pada tanggal ini</span>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openRoomModal(<?= htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8') ?>, '<?= e($foto_path) ?>')" 
                                        class="px-4 py-2.5 rounded-lg bg-[#F8FAFC] hover:bg-[#F1F5F9] border border-[#E2E8F0] text-[#0F172A] text-xs font-bold uppercase tracking-wider transition-colors">
                                    Detail Kamar
                                </button>
                                <?php if ($muat_tamu && $kosong_units > 0): ?>
                                    <a href="<?= e($url_pesan) ?>" 
                                       class="px-6 py-2.5 rounded-lg bg-[#0F172A] hover:bg-amber-600 text-white text-xs font-bold uppercase tracking-wider shadow-sm transition-all">
                                        Pilih Kamar
                                    </a>
                                <?php else: ?>
                                    <button disabled class="px-6 py-2.5 rounded-lg bg-slate-200 text-slate-400 text-xs font-bold uppercase tracking-wider cursor-not-allowed">
                                        Tidak Tersedia
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </main>
    </div>
</section>

<!-- Interactive Room Detail Modal -->
<div id="roomModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in duration-200">
        <div class="relative h-64 bg-slate-100">
            <img id="modalImg" src="" alt="" class="w-full h-full object-cover">
            <button onclick="document.getElementById('roomModal').classList.add('hidden')" 
                    class="absolute top-4 right-4 w-9 h-9 rounded-full bg-black/60 text-white flex items-center justify-center hover:bg-black transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs uppercase tracking-wider text-[#B45309] font-bold block">Aurelia Suites</span>
                    <h3 id="modalTitle" class="font-playfair text-2xl font-bold text-[#0F172A]"></h3>
                </div>
                <div class="text-right">
                    <span id="modalPrice" class="text-xl font-bold text-[#0F172A] block"></span>
                    <span class="text-xs text-slate-500">per malam</span>
                </div>
            </div>
            <p id="modalDesc" class="text-sm text-slate-600 leading-relaxed"></p>
            <div class="p-4 bg-[#F8FAFC] rounded-xl border border-slate-200 space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-[#0F172A] block">Fasilitas Termasuk:</span>
                <ul class="grid grid-cols-2 gap-2 text-xs text-slate-600">
                    <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-emerald-600">check</span> Sarapan Gourmet Restoran</li>
                    <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-emerald-600">check</span> Private Infinity Pool / Jacuzzi</li>
                    <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-emerald-600">check</span> Akses Executive Club Lounge</li>
                    <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-emerald-600">check</span> Layanan Butler Pribadi 24 Jam</li>
                    <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-emerald-600">check</span> High-Speed WiFi &amp; Smart TV</li>
                    <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-emerald-600">check</span> Perlengkapan Mandi Aromaterapi</li>
                </ul>
            </div>
            <div class="pt-2 flex justify-end">
                <button onclick="document.getElementById('roomModal').classList.add('hidden')" 
                        class="px-5 py-2.5 bg-[#0F172A] text-white text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-slate-800 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function filterCards(maxPrice) {
    document.querySelectorAll('.room-card').forEach(function(card) {
        var price = parseInt(card.getAttribute('data-price')) || 0;
        if (price <= maxPrice) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function openRoomModal(room, imgPath) {
    document.getElementById('modalTitle').innerText = room.nama_tipe;
    document.getElementById('modalDesc').innerText = room.deskripsi;
    document.getElementById('modalPrice').innerText = 'Rp ' + Number(room.harga_malam).toLocaleString('id-ID');
    document.getElementById('modalImg').src = imgPath;
    document.getElementById('roomModal').classList.remove('hidden');
}
</script>

<?php require 'footer.php'; ?>
