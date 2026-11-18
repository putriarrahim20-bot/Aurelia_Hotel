<?php
$hasil = mysqli_query($db,
    "SELECT t.*, (SELECT COUNT(*) FROM kamar k WHERE k.idtipe = t.idtipe) AS jml_kamar
     FROM tipe_kamar t ORDER BY t.harga_malam");
?>
<div id="label-page"><h3>Tipe Kamar</h3></div>
<div id="content">
    <div class="bar-atas"><a href="index.php?p=tipe-input" class="tombol">Tambah Tipe Kamar</a></div>
    <table class="tabel">
        <tr><th>Foto</th><th>Tipe</th><th>Harga / Malam</th><th>Kapasitas</th><th>Jumlah Kamar</th><th>Opsi</th></tr>
        <?php if (mysqli_num_rows($hasil) === 0): ?>
            <tr><td colspan="6">Belum ada tipe kamar.</td></tr>
        <?php else: while ($t = mysqli_fetch_assoc($hasil)): ?>
            <tr>
                <td><?php if ($t['foto'] !== '-'): ?><img src="../images/<?= e($t['foto']) ?>" class="poster-kecil" alt=""><?php else: ?>-<?php endif; ?></td>
                <td><b><?= e($t['nama_tipe']) ?></b><br><small><?= e(mb_strimwidth($t['deskripsi'], 0, 70, '...')) ?></small></td>
                <td><?= e(rupiah($t['harga_malam'])) ?></td>
                <td><?= (int)$t['kapasitas'] ?> tamu</td>
                <td><?= (int)$t['jml_kamar'] ?></td>
                <td>
                    <div class="opsi">
                        <a href="index.php?p=tipe-edit&id=<?= (int)$t['idtipe'] ?>" class="tombol tombol-kecil">Edit</a>
                        <form method="post" action="tipe-hapus.php" class="inline" onsubmit="return confirm('Yakin hapus tipe kamar ini?')">
                            <input type="hidden" name="id" value="<?= (int)$t['idtipe'] ?>">
                            <button type="submit" class="tombol tombol-kecil">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endwhile; endif; ?>
    </table>
</div>
