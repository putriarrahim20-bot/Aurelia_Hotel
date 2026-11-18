<?php
/* Kepala halaman publik Aurelia Hotels & Resorts */
require_once __DIR__ . '/fungsi.php';
$judul_halaman = e($judul ?? 'Reservasi Hotel Mewah');
$halaman_aktif = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul_halaman ?> | Aurelia Hotels & Resorts</title>
    
    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Tailwind CSS with Luxury Hospitality Design System -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0F172A",
                        "on-primary": "#ffffff",
                        "primary-container": "#1E293B",
                        "on-primary-container": "#94A3B8",
                        "secondary": "#904D00",
                        "secondary-container": "#FE932C",
                        "on-secondary-container": "#663500",
                        "gold-light": "#FEF3C7",
                        "gold-muted": "#B45309",
                        "gold-amber": "#D97706",
                        "surface-linen": "#FDFCFB",
                        "surface-sand": "#F8FAFC",
                        "surface-subtle": "#F1F5F9",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#EFF4FF",
                        "surface-container": "#E5EEFF",
                        "surface-container-high": "#DCE9FF",
                        "surface-container-highest": "#D3E4FE",
                        "on-surface": "#0B1C30",
                        "on-surface-variant": "#45464D",
                        "border-hairline": "#E2E8F0"
                    },
                    fontFamily: {
                        "display": ["Playfair Display", "serif"],
                        "headline": ["Playfair Display", "serif"],
                        "body": ["Plus Jakarta Sans", "sans-serif"],
                        "label": ["Plus Jakarta Sans", "sans-serif"]
                    }
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FDFCFB; color: #0B1C30; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .material-symbols-filled { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="bg-[#FDFCFB] text-[#0B1C30] antialiased min-h-screen flex flex-col">

<!-- Top Luxury Header Navigation -->
<header class="fixed top-0 left-0 w-full z-50 bg-white/95 backdrop-blur-md border-b border-[#E2E8F0] shadow-[0_1px_8px_rgba(0,0,0,0.03)] transition-all">
    <div class="h-20 max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 flex items-center justify-between">
        
        <!-- Brand Logo -->
        <div class="flex items-center gap-8">
            <a href="index.php" class="flex items-center gap-3 group">
                <img src="images/logo.png" alt="Aurelia Hotels Logo" class="h-9 w-auto object-contain transition-transform group-hover:scale-105" onerror="this.style.display='none'">
                <div class="flex flex-col">
                    <span class="font-playfair font-semibold text-xl tracking-wider uppercase text-[#0F172A] leading-none">Aurelia</span>
                    <span class="text-[11px] font-bold tracking-[0.25em] text-[#B45309] uppercase mt-0.5">Hotels &amp; Resorts</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden xl:flex items-center gap-7">
                <a href="index.php" class="text-sm font-semibold tracking-wide transition-colors <?= $halaman_aktif === 'index' ? 'text-[#B45309] font-bold' : 'text-[#45464D] hover:text-[#0F172A]' ?>">Beranda</a>
                <a href="kamar.php" class="text-sm font-semibold tracking-wide transition-colors <?= $halaman_aktif === 'kamar' ? 'text-[#B45309] font-bold' : 'text-[#45464D] hover:text-[#0F172A]' ?>">Koleksi Kamar &amp; Suites</a>
                <a href="index.php#layanan" class="text-sm font-semibold tracking-wide text-[#45464D] hover:text-[#0F172A] transition-colors">Layanan Unggulan</a>
                <a href="cek-reservasi.php" class="text-sm font-semibold tracking-wide transition-colors <?= $halaman_aktif === 'cek-reservasi' ? 'text-[#B45309] font-bold' : 'text-[#45464D] hover:text-[#0F172A]' ?>">Cek Reservasi</a>
                <a href="admin/login.php" class="text-sm font-semibold tracking-wide text-[#45464D] hover:text-[#0F172A] transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span> Admin
                </a>
            </nav>
        </div>

        <!-- Right Side Utility & CTA -->
        <div class="flex items-center gap-3 sm:gap-5">
            <div class="hidden lg:flex items-center gap-2">
                <div class="flex items-center gap-1 text-[#45464D] px-2.5 py-1.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] text-xs font-semibold cursor-pointer">
                    <span>IDR</span>
                    <span class="material-symbols-outlined text-[16px]">expand_more</span>
                </div>
                <div class="flex items-center gap-1 text-[#45464D] px-2.5 py-1.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] text-xs font-semibold cursor-pointer">
                    <span>ID</span>
                    <span class="material-symbols-outlined text-[16px]">expand_more</span>
                </div>
            </div>

            <!-- Book a Stay Button -->
            <a href="kamar.php" class="inline-flex items-center justify-center px-4 sm:px-5 py-2.5 rounded-lg bg-gradient-to-r from-amber-600 via-amber-500 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white text-xs sm:text-sm font-bold uppercase tracking-wider shadow-sm transition-all hover:shadow-md">
                <span>Pesan Sekarang</span>
            </a>

            <!-- User Avatar & Mobile Menu Toggle -->
            <div class="flex items-center pl-1 sm:pl-2 gap-2">
                <a href="cek-reservasi.php" title="Portal Tamu" class="ring-2 ring-[#B45309]/30 rounded-full p-0.5 hover:ring-[#B45309] transition-all">
                    <img src="images/avatar.jpg" alt="Profile" class="w-8 h-8 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Guest+User&background=0F172A&color=fff'">
                </a>
                <button onclick="document.getElementById('mobileMenu').classList.toggle('hidden')" class="xl:hidden p-2 text-[#0F172A] hover:bg-[#F1F5F9] rounded-lg">
                    <span class="material-symbols-outlined text-[24px]">menu</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="mobileMenu" class="hidden xl:hidden bg-white border-b border-[#E2E8F0] px-6 py-4 space-y-3">
        <a href="index.php" class="block text-sm font-semibold text-[#0F172A] py-1.5">Beranda</a>
        <a href="kamar.php" class="block text-sm font-semibold text-[#0F172A] py-1.5">Koleksi Kamar &amp; Suites</a>
        <a href="index.php#layanan" class="block text-sm font-semibold text-[#0F172A] py-1.5">Layanan Unggulan</a>
        <a href="cek-reservasi.php" class="block text-sm font-semibold text-[#0F172A] py-1.5">Cek Reservasi</a>
        <a href="admin/login.php" class="block text-sm font-semibold text-[#B45309] py-1.5">Portal Admin</a>
    </div>
</header>

<!-- Main Container with padding for fixed header -->
<main class="w-full pt-20 flex-grow">
