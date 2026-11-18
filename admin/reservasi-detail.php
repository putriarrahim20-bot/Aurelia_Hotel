<?php
$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($db,
    "SELECT r.*, k.nomor_kamar, t.nama_tipe
     FROM reservasi r
     JOIN kamar k ON k.idkamar = r.idkamar
     JOIN tipe_kamar t ON t.idtipe = k.idtipe
     WHERE r.idreservasi = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$r) {
    echo '<div id="label-page"><h3>Reservasi tidak ditemukan</h3></div>';
    return;
}

$stmt = mysqli_prepare($db, "SELECT * FROM pembayaran WHERE idreservasi = ? ORDER BY tgl_bayar");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$bayar = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$dibayar     = array_sum(array_column($bayar, 'jumlah'));
$sisa        = (int)$r['total'] - $dibayar;
$dp          = dp_minimal($r['total']);
$kedaluwarsa = $r['status'] === 'Menunggu' && $r['tgl_pesan'] <= batas_menunggu();
$bisa_bayar  = in_array($r['status'], ['Menunggu', 'Dikonfirmasi', 'Check-in'], true) && $sisa > 0;
?>
<div id="label-page"><h3>Reservasi <?= e($r['kode_booking']) ?></h3></div>
<div id="content">
    <p><a href="index.php?p=reservasi" class="tombol tombol-abu tombol-kecil">&larr; Kembali</a></p>

    <p>Status: <?= badge_status($r['status']) ?>
        <?php if ($kedaluwarsa): ?><span class="badge badge-batal">Kedaluwarsa</span><?php endif; ?></p>

    <table class="tabel">
        <tr><td>Tamu</td><td><b><?= e($r['nama_tamu']) ?></b> &middot; <?= e($r['no_hp']) ?><?= $r['email'] !== '' ? ' &middot; ' . e($r['email']) : '' ?></td></tr>
        <tr><td>Kamar</td><td><?= e($r['nama_tipe']) ?> &middot; No. <b><?= e($r['nomor_kamar']) ?></b></td></tr>
        <tr><td>Check-in</td><td><?= e(tanggal_indo($r['check_in'])) ?><?= $r['checkin_at'] ? ' <small>(masuk ' . e(date('d-m-Y H:i', strtotime($r['checkin_at']))) . ')</small>' : '' ?></td></tr>
        <tr><td>Check-out</td><td><?= e(tanggal_indo($r['check_out'])) ?> (<?= (int)$r['malam'] ?> malam)<?= $r['checkout_at'] ? ' <small>(keluar ' . e(date('d-m-Y H:i', strtotime($r['checkout_at']))) . ')</small>' : '' ?></td></tr>
        <tr><td>Jumlah tamu</td><td><?= (int)$r['jumlah_tamu'] ?> orang</td></tr>
        <tr><td>Catatan</td><td><?= $r['catatan'] !== '' ? e($r['catatan']) : '-' ?></td></tr>
        <tr><td>Waktu pesan</td><td><?= e(date('d-m-Y H:i', strtotime($r['tgl_pesan']))) ?></td></tr>
        <tr><td>Total biaya</td><td><b><?= e(rupiah($r['total'])) ?></b></td></tr>
        <tr><td>Sudah dibayar</td><td><?= e(rupiah($dibayar)) ?></td></tr>
        <tr><td>Sisa pembayaran</td><td><b><?= e(rupiah($sisa)) ?></b></td></tr>
    </table>

    <?php if ($r['status'] === 'Menunggu'): ?>
        <p class="info">DP minimal untuk konfirmasi: <b><?= e(rupiah($dp)) ?></b> (<?= PERSEN_DP ?>% dari total).
            <?= $dibayar >= $dp ? 'DP sudah terpenuhi.' : 'Baru diterima ' . e(rupiah($dibayar)) . '.' ?></p>
    <?php endif; ?>

    <div class="opsi" style="margin:14px 0">
        <?php if ($r['status'] === 'Menunggu'): ?>
            <form method="post" action="reservasi-status.php" class="inline" onsubmit="return confirm('Konfirmasi reservasi ini?')">
                <input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="aksi" value="konfirmasi">
                <button type="submit" class="tombol">Konfirmasi</button>
            </form>
        <?php endif; ?>
        <?php if ($r['status'] === 'Dikonfirmasi'): ?>
            <form method="post" action="reservasi-status.php" class="inline" onsubmit="return confirm('Proses check-in tamu ini?')">
                <input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="aksi" value="checkin">
                <button type="submit" class="tombol">Check-in</button>
            </form>
        <?php endif; ?>
        <?php if ($r['status'] === 'Check-in'): ?>
            <form method="post" action="reservasi-status.php" class="inline" onsubmit="return confirm('Proses check-out tamu ini?')">
                <input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="aksi" value="checkout">
                <button type="submit" class="tombol">Check-out</button>
            </form>
        <?php endif; ?>
        <?php if (in_array($r['status'], ['Menunggu', 'Dikonfirmasi'], true)): ?>
            <form method="post" action="reservasi-status.php" class="inline"
                  onsubmit="return confirm('Batalkan reservasi ini? Jika sudah ada pembayaran, pengembalian dana dilakukan manual dan tidak dihitung sebagai pendapatan.')">
                <input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="aksi" value="batal">
                <button type="submit" class="tombol tombol-abu">Batalkan</button>
            </form>
        <?php endif; ?>
    </div>

    <h3 class="sub-judul">Pembayaran</h3>
    <table class="tabel">
        <tr><th>Tanggal</th><th>Jumlah</th><th>Metode</th><th>Keterangan</th></tr>
        <?php if (!$bayar): ?>
            <tr><td colspan="4">Belum ada pembayaran.</td></tr>
        <?php else: foreach ($bayar as $b): ?>
            <tr>
                <td><?= e(date('d-m-Y H:i', strtotime($b['tgl_bayar']))) ?></td>
                <td><?= e(rupiah($b['jumlah'])) ?></td>
                <td><?= e($b['metode']) ?></td>
                <td><?= e($b['keterangan']) ?></td>
            </tr>
        <?php endforeach; endif; ?>
    </table>

    <?php if ($bisa_bayar): ?>
        <form action="bayar-proses.php" method="post" class="form-isian">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="tiga-kolom">
                <div><label>Jumlah (Rp)</label><input type="number" name="jumlah" min="1" max="<?= $sisa ?>" value="<?= $r['status'] === 'Menunggu' && $dibayar < $dp ? $dp - $dibayar : $sisa ?>" required></div>
                <div><label>Metode</label>
                    <select name="metode"><option>Tunai</option><option>Transfer</option><option>QRIS</option></select>
                </div>
                <div><label>Keterangan</label><input type="text" name="keterangan" maxlength="100" placeholder="DP / pelunasan"></div>
            </div>
            <div class="aksi-form"><input type="submit" value="Catat Pembayaran" class="tombol"></div>
        </form>
    <?php endif; ?>
</div>
