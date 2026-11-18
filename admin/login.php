<?php
session_start();
if (!empty($_SESSION['sesi'])) {
    header('Location: index.php');
    exit;
}

$pesan_error = [
    'user' => 'Username tidak ditemukan di database.',
    'hash' => 'Format password hash tidak valid.',
    'pass' => 'Password salah. Silakan coba lagi.',
    '1'    => 'Username atau password salah.',
];
$kode_error = $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Aurelia Hotels &amp; Resorts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-[#0F172A] min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-b from-[#1E293B] to-[#0F172A] p-8 text-center text-white border-b-2 border-amber-500">
            <div class="flex items-center justify-center gap-3 mb-2">
                <img src="../images/logo.png" alt="Aurelia Logo" class="h-8 w-auto brightness-0 invert" onerror="this.style.display='none'">
                <div>
                    <span class="font-playfair text-2xl font-bold tracking-wider uppercase block leading-none">AURELIA</span>
                    <span class="text-[9px] font-bold tracking-[0.25em] text-amber-300 uppercase block mt-0.5">Hotels &amp; Resorts</span>
                </div>
            </div>
            <p class="text-xs text-slate-300 mt-2">Portal Manajemen Staf &amp; Administrasi Resor</p>
        </div>

        <!-- Form Body -->
        <div class="p-8 space-y-6">
            <?php if ($kode_error !== null): ?>
                <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-lg flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-rose-500">error</span>
                    <span><?= htmlspecialchars($pesan_error[$kode_error] ?? $pesan_error['1'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <form method="post" action="cek_login.php" class="space-y-4">
                <div>
                    <label for="user" class="text-xs font-bold uppercase tracking-wider text-slate-600 block mb-1.5">Username Akun</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-3 text-slate-400 text-[18px]">person</span>
                        <input type="text" id="user" name="user" required autofocus placeholder="admin" 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-[#0F172A] font-medium outline-none focus:border-[#0F172A] focus:bg-white transition-all">
                    </div>
                </div>

                <div>
                    <label for="pass" class="text-xs font-bold uppercase tracking-wider text-slate-600 block mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-3 text-slate-400 text-[18px]">lock</span>
                        <input type="password" id="pass" name="pass" required placeholder="••••••••" 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-[#0F172A] font-medium outline-none focus:border-[#0F172A] focus:bg-white transition-all">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full py-3 bg-[#0F172A] hover:bg-amber-600 text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-md transition-all">
                    Masuk ke Sistem
                </button>
            </form>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <a href="../index.php" class="hover:text-[#0F172A] transition-colors flex items-center gap-1 font-semibold">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span> Lihat Situs Publik
                </a>
                <span>Verifikasi Sandi SHA-256</span>
            </div>
        </div>
    </div>

</body>
</html>
