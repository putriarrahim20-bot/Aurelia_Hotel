# Aurelia Hotels & Resorts - Sistem Reservasi Hotel Mewah

Sistem reservasi hotel butik mewah (*Luxury Hospitality & Boutique Stays*) berbasis PHP & MySQL dengan sistem desain editorial modern terinspirasi dari arsitektur pesisir tebing Uluwatu, Bali.

## 🌟 Fitur Utama

- **Beranda Sinematik & Mesin Pencarian**:
  - Hero visual bernuansa alam tebing pesisir pantai.
  - Floating search engine: pemilihan destinasi, durasi menginap (check-in & check-out dinamis), jumlah tamu, dan kode promo.
  - Layanan unggulan bento: Concierge 24 Jam, Michelin Dining, World-class Spa & Wellness, Helipad VIP.
  - Koleksi kamar & private suites populer terhubung langsung ke database.

- **Koleksi Kamar & Filter Presisi**:
  - Ringkasan pencarian menginap dan drawer pengubah tanggal/tamu.
  - Filter presisi interaktif: rentang harga slider, jenis tempat tidur, fasilitas unggulan, dan kebijakan pembatalan.
  - Cek ketersediaan kamar riil secara otomatis per tanggal booking.
  - Modal pop-up interaktif detail fasilitas dan spesifikasi suite.

- **Detail Reservasi & Pembayaran (4-Step Checkout)**:
  - Indikator progres 4 tahap yang elegan.
  - Informasi tamu utama & nomor anggota Aurelia Club.
  - Preferensi khusus (*Early Check-in, High Floor, Non-Smoking, Extra Bed*).
  - Layanan kurasi tambahan (*Airport Limousine VIP, Paket Spa Bali, Romantic Candlelight Dinner*) dengan kalkulasi total dinamis.
  - Pilihan metode pembayaran terenkripsi (Transfer Bank, QRIS Instant, Bayar di Resor).

- **Konfirmasi & E-Voucher Digital**:
  - Tampilan kode booking resmi dengan tombol *Salin Kode*.
  - Rincian lengkap jadwal menginap, tipe suite, data tamu, dan status DP.
  - Digital Pass & simulasi QR Code untuk registrasi cepat di meja concierge.
  - Riwayat transaksi pembayaran tercatat.

- **Cetak E-Voucher PDF**:
  - Format cetak profesional berstandar hotel bintang 5 dengan kop surat resmi, ketentuan registrasi, dan tanda tangan manajemen.

- **Portal Staf & Administrasi Hotel**:
  - Dashboard statistik kamar terisi, reservasi menunggu konfirmasi, dan pendapatan bulanan.
  - Manajemen tipe kamar (nama, deskripsi, harga, kapasitas, upload foto).
  - Manajemen unit kamar fisik & status operasional.
  - Pemrosesan status reservasi (*Menunggu, Dikonfirmasi, Check-in, Check-out, Batal*).
  - Pencatatan pembayaran dan laporan keuangan.

---

## 🛠️ Persyaratan Sistem & Instalasi

1. **Persyaratan**:
   - Web Server: Apache (XAMPP / Laragon)
   - PHP: Versi 7.4 atau 8.x (dengan ekstensi `mysqli`, `mbstring`)
   - Database: MySQL atau MariaDB

2. **Langkah Instalasi**:
   - Letakkan folder proyek di `htdocs` (misal: `C:\xampp\htdocs\reservasi-hotel`).
   - Pastikan MySQL dan Apache telah aktif.
   - Database `dbhotel` sudah otomatis tersedia, atau import file `database.sql` ke MySQL.
   - Konfigurasi database dapat dicek di `koneksi.php`.

3. **Menjalankan Proyek**:
   - Buka browser dan akses: `http://localhost/reservasi-hotel/`
   - Portal Admin: `http://localhost/reservasi-hotel/admin/login.php`
   - Kredensial Admin Default:
     - **Username**: `admin`
     - **Password**: `admin123`
