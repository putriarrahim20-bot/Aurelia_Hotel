<?php
$batas  = 10;
$cari   = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';
$hal    = max(1, (int)($_GET['hal'] ?? 1));
$posisi = ($hal - 1) * $batas;

$kond  = [];
$tipe  = '';
$param = [];
if ($cari !== '') {
    $kond[] = "(r.kode_booking LIKE ? OR r.nama_tamu LIKE ? OR r.email LIKE ? OR r.no_hp LIKE ?)";
    $like   = "%$cari%";
    $tipe  .= 'ssss';
    array_push($param, $like, $like, $like, $like);
}
if (in_array($status, ['Menunggu', 'Dikonfirmasi', 'Check-in', 'Check-out', 'Batal'], true)) {
    $kond[]  = "r.status = ?";
    $tipe   .= 's';
    $param[] = $status;
} else {
    $status = '';
}
$where = $kond ? 'WHERE ' . implode(' AND ', $kond) : '';

$stmt = mysqli_prepare($db, "SELECT COUNT(*) FROM reservasi r $where");
if ($tipe !== '') {
    mysqli_stmt_bind_param($stmt, $tipe, ...$param);
}
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $jml);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
$jml_hal = max(1, (int)ceil($jml / $batas));

$sql = "SELECT r.*, k.nomor_kamar, t.nama_tipe,
          (SELECT COALESCE(SUM(p.jumlah), 0) FROM pembayaran p WHERE p.idreservasi = r.idreservasi) AS dibayar
        FROM reservasi r
        JOIN kamar k ON k.idkamar = r.idkamar
        JOIN tipe_kamar t ON t.idtipe = k.idtipe
        $where
        ORDER BY r.tgl_pesan DESC
        LIMIT ?, ?";
$stmt = mysqli_prepare($db, $sql);
$param_hal = array_merge($param, [$posisi, $batas]);
mysqli_stmt_bind_param($stmt, $tipe . 'ii', ...$param_hal);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$batas_menunggu = batas_menunggu();
?>
<div id="label-page"><h3>Data Reservasi</h3></div>
<div id="content">
    <div class="bar-atas">
        <a href="index.php?p=reservasi-input" class="tombol">Reservasi Baru (Walk-in)</a>
        <form method="get" action="index.php">
            <input type="hidden" name="p" value="reservasi">
            <select name="status">
                <option value="">Semua status</option>
                <?php foreach (['Menunggu', 'Dikonfirmasi', 'Check-in', 'Check-out', 'Batal'] as $s): ?>
                    <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="q" value="<?= e($cari) ?>" placeholder="Kode / nama / HP...">
            <input type="submit" value="Filter" class="tombol">
        </form>
    </div>

    <table class="tabel">
        <tr><th>Kode</th><th>Tamu</th><th>Kamar</th><th>Menginap</th><th>Total</th><th>Dibayar</th><th>Status</th><th>Opsi</th></tr>
        <?php if (mysqli_num_rows($hasil) === 0): ?>
            <tr><td colspan="8">Data tidak ditemukan.</td></tr>
        <?php else: while ($r = mysqli_fetch_assoc($hasil)): ?>
            <tr>
                <td><b><?= e($r['kode_booking']) ?></b></td>
                <td><?= e($r['nama_tamu']) ?><br><small><?= e($r['no_hp']) ?></small></td>
                <td><?= e($r['nomor_kamar']) ?><br><small><?= e($r['nama_tipe']) ?></small></td>
                <td><?= e(date('d/m/y', strtotime($r['check_in']))) ?> &rarr; <?= e(date('d/m/y', strtotime($r['check_out']))) ?><br><small><?= (int)$r['malam'] ?> malam</small></td>
                <td><?= e(rupiah($r['total'])) ?></td>
                <td><?= e(rupiah($r['dibayar'])) ?></td>
                <td>
                    <?= badge_status($r['status']) ?>
                    <?php if ($r['status'] === 'Menunggu' && $r['tgl_pesan'] <= $batas_menunggu): ?>
                        <span class="badge badge-batal">Kedaluwarsa</span>
                    <?php endif; ?>
                </td>
                <td><a class="tombol tombol-kecil" href="index.php?p=reservasi-detail&id=<?= (int)$r['idreservasi'] ?>">Detail</a></td>
            </tr>
        <?php endwhile; endif; ?>
    </table>

    <div class="bar-bawah">
        <div>Jumlah reservasi : <b><?= $jml ?></b></div>
        <?php tampil_paginasi($jml_hal, $hal, ['p' => 'reservasi', 'q' => $cari, 'status' => $status]); ?>
    </div>
</div>
