<?php
$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($db, "SELECT * FROM tipe_kamar WHERE idtipe = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$t = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$t) {
    echo '<div id="label-page"><h3>Tipe kamar tidak ditemukan</h3></div>';
    return;
}
?>
<div id="label-page"><h3>Edit Tipe Kamar</h3></div>
<div id="content">
    <form action="tipe-edit-proses.php" method="post" enctype="multipart/form-data" class="form-isian">
        <input type="hidden" name="idtipe" value="<?= $id ?>">
        <label>Nama Tipe</label>
        <input type="text" name="nama_tipe" value="<?= e($t['nama_tipe']) ?>" required maxlength="50">
        <label>Deskripsi / Fasilitas</label>
        <textarea name="deskripsi" rows="3"><?= e($t['deskripsi']) ?></textarea>
        <div class="dua-kolom">
            <div><label>Harga per Malam (Rp)</label><input type="number" name="harga_malam" value="<?= (int)$t['harga_malam'] ?>" required min="0"></div>
            <div><label>Kapasitas (tamu)</label><input type="number" name="kapasitas" value="<?= (int)$t['kapasitas'] ?>" required min="1" max="20"></div>
        </div>
        <small>Perubahan harga hanya berlaku untuk reservasi baru. Reservasi yang sudah ada tetap memakai harga saat dipesan.</small>
        <label>Foto</label>
        <?php if ($t['foto'] !== '-'): ?><img src="../images/<?= e($t['foto']) ?>" class="poster-kecil" alt=""><br><?php endif; ?>
        <input type="file" name="foto" accept="image/*">
        <small>Kosongkan jika tidak ingin mengganti foto.</small>
        <div class="aksi-form">
            <input type="submit" name="simpan" value="Simpan" class="tombol">
            <a href="index.php?p=tipe" class="tombol tombol-abu">Batal</a>
        </div>
    </form>
</div>
