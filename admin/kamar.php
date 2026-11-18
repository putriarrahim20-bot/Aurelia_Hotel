<?php
$hasil = mysqli_query($db,
    "SELECT k.*, t.nama_tipe,
        EXISTS (SELECT 1 FROM reservasi r WHERE r.idkamar = k.idkamar AND r.status = 'Check-in') AS terisi
     FROM kamar k JOIN tipe_kamar t ON t.idtipe = k.idtipe
     ORDER BY k.nomor_kamar");
$daftar_tipe = mysqli_fetch_all(mysqli_query($db, "SELECT idtipe, nama_tipe FROM tipe_kamar ORDER BY nama_tipe"), MYSQLI_ASSOC);
?>
<div id="label-page"><h3>Data Kamar</h3></div>
<div id="content">
    <table class="tabel">
        <tr><th>No. Kamar</th><th>Tipe</th><th>Status Kamar</th><th>Hari Ini</th><th>Opsi</th></tr>
        <?php if (mysqli_num_rows($hasil) === 0): ?>
            <tr><td colspan="5">Belum ada kamar.</td></tr>
        <?php else: while ($k = mysqli_fetch_assoc($hasil)): ?>
            <tr>
                <td><b><?= e($k['nomor_kamar']) ?></b></td>
                <td><?= e($k['nama_tipe']) ?></td>
                <td><?= badge_status($k['status']) ?></td>
                <td><?= $k['terisi'] ? '<span class="badge badge-lunas">Terisi</span>' : '<small>Kosong</small>' ?></td>
                <td>
                    <div class="opsi">
                        <a href="index.php?p=kamar-edit&id=<?= (int)$k['idkamar'] ?>" class="tombol tombol-kecil">Edit</a>
                        <form method="post" action="kamar-hapus.php" class="inline" onsubmit="return confirm('Yakin hapus kamar ini?')">
                            <input type="hidden" name="id" value="<?= (int)$k['idkamar'] ?>">
                            <button type="submit" class="tombol tombol-kecil">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endwhile; endif; ?>
    </table>

    <h3 class="sub-judul">Tambah Kamar</h3>
    <?php if (!$daftar_tipe): ?>
        <p class="pesan-error">Buat tipe kamar terlebih dahulu di menu Tipe Kamar.</p>
    <?php else: ?>
        <form action="kamar-input-proses.php" method="post" class="form-isian">
            <div class="tiga-kolom">
                <div><label>Nomor Kamar</label><input type="text" name="nomor_kamar" required maxlength="10" placeholder="101"></div>
                <div><label>Tipe</label>
                    <select name="idtipe">
                        <?php foreach ($daftar_tipe as $t): ?>
                            <option value="<?= (int)$t['idtipe'] ?>"><?= e($t['nama_tipe']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div><label>Status</label>
                    <select name="status"><option value="Aktif">Aktif</option><option value="Perbaikan">Perbaikan</option></select>
                </div>
            </div>
            <div class="aksi-form"><input type="submit" name="simpan" value="Tambah" class="tombol"></div>
        </form>
    <?php endif; ?>
</div>
