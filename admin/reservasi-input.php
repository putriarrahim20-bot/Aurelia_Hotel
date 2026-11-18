<?php
$daftar_tipe = mysqli_fetch_all(
    mysqli_query($db, "SELECT idtipe, nama_tipe, harga_malam, kapasitas FROM tipe_kamar ORDER BY harga_malam"), MYSQLI_ASSOC);
?>
<div id="label-page"><h3>Reservasi Baru (Walk-in)</h3></div>
<div id="content">
    <?php if (!$daftar_tipe): ?>
        <p class="pesan-error">Belum ada tipe kamar. Buat dulu di menu Tipe Kamar.</p>
    <?php else: ?>
    <form action="reservasi-input-proses.php" method="post" class="form-isian">
        <label>Tipe Kamar</label>
        <select name="idtipe">
            <?php foreach ($daftar_tipe as $t): ?>
                <option value="<?= (int)$t['idtipe'] ?>">
                    <?= e($t['nama_tipe']) ?> &mdash; <?= e(rupiah($t['harga_malam'])) ?>/malam (maks. <?= (int)$t['kapasitas'] ?> tamu)
                </option>
            <?php endforeach; ?>
        </select>

        <div class="tiga-kolom">
            <div><label>Check-in</label><input type="date" name="ci" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required></div>
            <div><label>Check-out</label><input type="date" name="co" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required></div>
            <div><label>Jumlah Tamu</label><input type="number" name="tamu" value="1" min="1" max="20" required></div>
        </div>

        <label>Nama Tamu</label>
        <input type="text" name="nama" required maxlength="100">
        <div class="dua-kolom">
            <div><label>No. HP</label><input type="text" name="no_hp" required maxlength="20"></div>
            <div><label>Email (opsional)</label><input type="email" name="email" maxlength="100"></div>
        </div>
        <label>Catatan</label>
        <textarea name="catatan" rows="2" maxlength="255"></textarea>

        <small>Reservasi walk-in langsung berstatus Dikonfirmasi. Kamar dipilih otomatis (nomor terkecil yang kosong).</small>
        <div class="aksi-form">
            <input type="submit" name="simpan" value="Simpan Reservasi" class="tombol">
            <a href="index.php?p=reservasi" class="tombol tombol-abu">Batal</a>
        </div>
    </form>
    <?php endif; ?>
</div>
