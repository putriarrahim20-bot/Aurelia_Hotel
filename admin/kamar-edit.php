<?php
$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($db, "SELECT * FROM kamar WHERE idkamar = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$k = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$k) {
    echo '<div id="label-page"><h3>Kamar tidak ditemukan</h3></div>';
    return;
}
$daftar_tipe = mysqli_fetch_all(mysqli_query($db, "SELECT idtipe, nama_tipe FROM tipe_kamar ORDER BY nama_tipe"), MYSQLI_ASSOC);
?>
<div id="label-page"><h3>Edit Kamar <?= e($k['nomor_kamar']) ?></h3></div>
<div id="content">
    <form action="kamar-edit-proses.php" method="post" class="form-isian">
        <input type="hidden" name="idkamar" value="<?= $id ?>">
        <label>Nomor Kamar</label>
        <input type="text" name="nomor_kamar" value="<?= e($k['nomor_kamar']) ?>" required maxlength="10">
        <label>Tipe</label>
        <select name="idtipe">
            <?php foreach ($daftar_tipe as $t): ?>
                <option value="<?= (int)$t['idtipe'] ?>" <?= (int)$t['idtipe'] === (int)$k['idtipe'] ? 'selected' : '' ?>><?= e($t['nama_tipe']) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Status</label>
        <select name="status">
            <option value="Aktif" <?= $k['status'] === 'Aktif' ? 'selected' : '' ?>>Aktif (bisa dipesan)</option>
            <option value="Perbaikan" <?= $k['status'] === 'Perbaikan' ? 'selected' : '' ?>>Perbaikan (tidak bisa dipesan)</option>
        </select>
        <div class="aksi-form">
            <input type="submit" name="simpan" value="Simpan" class="tombol">
            <a href="index.php?p=kamar" class="tombol tombol-abu">Batal</a>
        </div>
    </form>
</div>
