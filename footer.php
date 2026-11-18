</main>

<!-- Luxury Hospitality Footer -->
<footer class="w-full bg-[#0F172A] text-white pt-16 pb-12 mt-20 border-t border-[#1E293B]">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 pb-12 border-b border-white/10">
            
            <!-- Col 1: Brand Info (5 cols) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="images/logo.png" alt="Aurelia Logo" class="h-8 w-auto object-contain brightness-0 invert opacity-90" onerror="this.style.display='none'">
                    <div>
                        <span class="font-playfair text-xl tracking-wider uppercase text-white font-semibold block leading-none">Aurelia</span>
                        <span class="text-[10px] font-bold tracking-[0.25em] text-[#FEF3C7] uppercase mt-0.5 block">Hotels &amp; Resorts</span>
                    </div>
                </div>
                <p class="text-sm text-slate-300 max-w-sm leading-relaxed">
                    Menghadirkan perpaduan sempurna antara kemegahan arsitektur modern, kehangatan keramahan Nusantara, dan ketenangan abadi di pesisir tebing Uluwatu, Bali.
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-[#FEF3C7]">
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                    </div>
                    <span class="text-xs text-slate-300 font-medium">Bintang 5 Forbes Travel Guide &middot; Michelin Key Selection</span>
                </div>
            </div>

            <!-- Col 2: Navigation Links (2 cols) -->
            <div class="lg:col-span-2 space-y-3">
                <h4 class="text-xs uppercase tracking-[0.2em] text-[#FEF3C7] font-bold mb-4">Navigasi Resor</h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li><a href="index.php" class="hover:text-white transition-colors">Beranda</a></li>
                    <li><a href="kamar.php" class="hover:text-white transition-colors">Kamar &amp; Suites</a></li>
                    <li><a href="index.php#layanan" class="hover:text-white transition-colors">Layanan Unggulan</a></li>
                    <li><a href="cek-reservasi.php" class="hover:text-white transition-colors">Cek Status Booking</a></li>
                    <li><a href="admin/login.php" class="hover:text-white transition-colors">Portal Staf Hotel</a></li>
                </ul>
            </div>

            <!-- Col 3: Concierge 24 Jam (2 cols) -->
            <div class="lg:col-span-2 space-y-3">
                <h4 class="text-xs uppercase tracking-[0.2em] text-[#FEF3C7] font-bold mb-4">Layanan Butler</h4>
                <ul class="space-y-2.5 text-sm text-slate-300">
                    <li class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-[18px] text-amber-400 mt-0.5">location_on</span>
                        <span>Jl. Pantai Selatan No. 88, Uluwatu, Bali 80361</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-amber-400">call</span>
                        <span>+62 (361) 892-8888</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-amber-400">mail</span>
                        <span>concierge@aureliahotels.com</span>
                    </li>
                </ul>
            </div>

            <!-- Col 4: Newsletter / Aurelia Club (3 cols) -->
            <div class="lg:col-span-3 space-y-3">
                <h4 class="text-xs uppercase tracking-[0.2em] text-[#FEF3C7] font-bold mb-4">Aurelia Club Privileges</h4>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Daftarkan email Anda untuk menerima penawaran eksklusif, undangan acara gala privat, dan diskon keanggotaan hingga 20%.
                </p>
                <form onsubmit="event.preventDefault(); alert('Terima kasih! Anda telah terdaftar dalam program Aurelia Club.');" class="flex items-center gap-2 pt-1">
                    <input type="email" placeholder="Alamat email Anda..." required class="w-full px-3 py-2 text-xs bg-white/10 rounded-lg border border-white/20 text-white placeholder-slate-400 outline-none focus:border-amber-400">
                    <button type="submit" class="px-3.5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold uppercase tracking-wider transition-colors">Gabung</button>
                </form>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
            <p>&copy; <?= date('Y') ?> Aurelia Hotels &amp; Resorts. Hak Cipta Dilindungi Undang-Undang.</p>
            <div class="flex items-center gap-6">
                <span>Jaminan Transaksi Enkripsi 256-Bit</span>
                <span>PCI-DSS Level 1 Compliant</span>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
