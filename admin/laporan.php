<?php
$bulan = $_GET['bulan'] ?? date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $bulan) || !tanggal_valid($bulan . '-01')) {
    $bulan = date('Y-m');
}
$awal      = $bulan . '-01';
$akhir     = date('Y-m-d', strtotime($awal . ' +1 month'));   // batas atas (tidak termasuk)
$jml_hari  = (int)date('t', strtotime($awal));
$total_kamar = angka_tunggal($db, "SELECT COUNT(*) FROM kamar WHERE status = 'Aktif'");

// Jumlah kamar terisi per tanggal (malam menginap: check-in s/d sehari sebelum check-out)
$terisi = array_fill(1, $jml_hari, 0);
$stmt = mysqli_prepare($db,
    "SELECT check_in, check_out FROM reservasi
     WHERE status IN ('Dikonfirmasi', 'Check-in', 'Check-out') AND check_in < ? AND check_out > ?");
mysqli_stmt_bind_param($stmt, 'ss', $akhir, $awal);
mysqli_stmt_execute($stmt);
$daftar = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

foreach ($daftar as $r) {
    $tgl    = new DateTime(max($r['check_in'], $awal));
    $sampai = new DateTime(min($r['check_out'], $akhir));
    while ($tgl < $sampai) {
        $terisi[(int)$tgl->format('j')]++;
        $tgl->modify('+1 day');
    }
}
$total_malam = array_sum($terisi);
$rata_okupansi = $total_kamar > 0 ? $total_malam / ($total_kamar * $jml_hari) * 100 : 0;

// Pendapatan = pembayaran yang diterima pada bulan ini (reservasi Batal tidak dihitung)
$awal_waktu  = $awal . ' 00:00:00';
$akhir_waktu = $akhir . ' 00:00:00';
$stmt = mysqli_prepare($db,
    "SELECT COALESCE(SUM(p.jumlah), 0) FROM pembayaran p
     JOIN reservasi r ON r.idreservasi = p.idreservasi
     WHERE r.status <> 'Batal' AND p.tgl_bayar >= ? AND p.tgl_bayar < ?");
mysqli_stmt_bind_param($stmt, 'ss', $awal_waktu, $akhir_waktu);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $pendapatan);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Jumlah reservasi (berdasarkan tanggal check-in) per status
$stmt = mysqli_prepare($db,
    "SELECT status, COUNT(*) AS jml FROM reservasi WHERE check_in >= ? AND check_in < ? GROUP BY status");
mysqli_stmt_bind_param($stmt, 'ss', $awal, $akhir);
mysqli_stmt_execute($stmt);
$per_status = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>
<div id="label-page"><h3>Laporan Okupansi &amp; Pendapatan</h3></div>
<div id="content">
    <form method="get" action="index.php" class="bar-atas tidak-dicetak">
        <input type="hidden" name="p" value="laporan">
        <div>
            <input type="month" name="bulan" value="<?= e($bulan) ?>" style="width:auto">
            <input type="submit" value="Tampilkan" class="tombol">
        </div>
        <button type="button" class="tombol" onclick="window.print()">Cetak Laporan</button>
    </form>

    <h3 class="judul-cetak">Laporan Hotel &mdash; <?= e(date('m/Y', strtotime($awal))) ?></h3>

    <div class="kartu-statistik" style="margin:0 0 16px">
        <div class="stat"><span><?= number_format($rata_okupansi, 1, ',', '.') ?>%</span>Rata-rata okupansi</div>
        <div class="stat"><span><?= $total_malam ?></span>Malam kamar terjual</div>
        <div class="stat"><span><?= e(rupiah($pendapatan)) ?></span>Pendapatan diterima</div>
    </div>

    <?php if ($per_status): ?>
        <p>Reservasi dengan check-in bulan ini:
            <?php foreach ($per_status as $s): ?>
                <?= badge_status($s['status']) ?> <?= (int)$s['jml'] ?>&nbsp;&nbsp;
            <?php endforeach; ?>
        </p>
    <?php endif; ?>

    <table class="tabel">
        <tr><th>Tanggal</th><th>Kamar Terisi</th><th>Kamar Aktif</th><th>Okupansi</th></tr>
        <?php for ($h = 1; $h <= $jml_hari; $h++):
            $persen = $total_kamar > 0 ? $terisi[$h] / $total_kamar * 100 : 0;
        ?>
            <tr>
                <td><?= e(tanggal_indo($bulan . '-' . sprintf('%02d', $h))) ?></td>
                <td><?= $terisi[$h] ?></td>
                <td><?= $total_kamar ?></td>
                <td><?= number_format($persen, 0) ?>%</td>
            </tr>
        <?php endfor; ?>
    </table>
</div>
