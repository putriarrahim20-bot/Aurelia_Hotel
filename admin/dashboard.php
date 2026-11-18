<?php
$hari_ini = date('Y-m-d');
$batas    = batas_menunggu();
$awal_bln = date('Y-m-01 00:00:00');

$kamar_aktif = angka_tunggal($db, "SELECT COUNT(*) FROM kamar WHERE status = 'Aktif'");
$terisi      = angka_tunggal($db, "SELECT COUNT(*) FROM reservasi WHERE status = 'Check-in'");
$menunggu    = angka_tunggal($db, "SELECT COUNT(*) FROM reservasi WHERE status = 'Menunggu' AND tgl_pesan > '$batas'");
$pendapatan  = angka_tunggal($db,
    "SELECT COALESCE(SUM(p.jumlah), 0) FROM pembayaran p
     JOIN reservasi r ON r.idreservasi = p.idreservasi
     WHERE r.status <> 'Batal' AND p.tgl_bayar >= '$awal_bln'");

$akan_masuk = mysqli_query($db,
    "SELECT r.idreservasi, r.kode_booking, r.nama_tamu, r.check_in, k.nomor_kamar
     FROM reservasi r JOIN kamar k ON k.idkamar = r.idkamar
     WHERE r.status = 'Dikonfirmasi' AND r.check_in <= '$hari_ini' AND r.check_out > '$hari_ini'
     ORDER BY r.check_in");
$akan_keluar = mysqli_query($db,
    "SELECT r.idreservasi, r.kode_booking, r.nama_tamu, r.check_out, k.nomor_kamar
     FROM reservasi r JOIN kamar k ON k.idkamar = r.idkamar
     WHERE r.status = 'Check-in' AND r.check_out <= '$hari_ini'
     ORDER BY r.check_out");
?>
<div id="label-page"><h3>Dashboard</h3></div>

<div class="kartu-statistik">
    <div class="stat"><span><?= $terisi ?> / <?= $kamar_aktif ?></span>Kamar terisi</div>
    <div class="stat stat-peringatan"><span><?= $menunggu ?></span>Menunggu konfirmasi</div>
    <div class="stat"><span><?= e(rupiah($pendapatan)) ?></span>Pendapatan bulan ini</div>
</div>

<div id="content">
    <h3>Tamu yang akan check-in</h3>
    <table class="tabel">
        <tr><th>Kode</th><th>Tamu</th><th>Kamar</th><th>Tanggal</th><th>Opsi</th></tr>
        <?php if (mysqli_num_rows($akan_masuk) === 0): ?>
            <tr><td colspan="5">Tidak ada tamu yang menunggu check-in.</td></tr>
        <?php else: while ($r = mysqli_fetch_assoc($akan_masuk)): ?>
            <tr>
                <td><?= e($r['kode_booking']) ?></td>
                <td><?= e($r['nama_tamu']) ?></td>
                <td><?= e($r['nomor_kamar']) ?></td>
                <td><?= e(tanggal_indo($r['check_in'])) ?></td>
                <td><a class="tombol tombol-kecil" href="index.php?p=reservasi-detail&id=<?= (int)$r['idreservasi'] ?>">Proses</a></td>
            </tr>
        <?php endwhile; endif; ?>
    </table>

    <h3 class="sub-judul">Tamu yang akan check-out</h3>
    <table class="tabel">
        <tr><th>Kode</th><th>Tamu</th><th>Kamar</th><th>Jadwal Check-out</th><th>Opsi</th></tr>
        <?php if (mysqli_num_rows($akan_keluar) === 0): ?>
            <tr><td colspan="5">Tidak ada tamu yang harus check-out.</td></tr>
        <?php else: while ($r = mysqli_fetch_assoc($akan_keluar)): ?>
            <tr>
                <td><?= e($r['kode_booking']) ?></td>
                <td><?= e($r['nama_tamu']) ?></td>
                <td><?= e($r['nomor_kamar']) ?></td>
                <td><?= e(tanggal_indo($r['check_out'])) ?></td>
                <td><a class="tombol tombol-kecil" href="index.php?p=reservasi-detail&id=<?= (int)$r['idreservasi'] ?>">Proses</a></td>
            </tr>
        <?php endwhile; endif; ?>
    </table>
</div>
