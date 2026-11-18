<div id="label-page"><h3>Tambah Tipe Kamar</h3></div>
<div id="content">
    <form action="tipe-input-proses.php" method="post" enctype="multipart/form-data" class="form-isian">
        <label>Nama Tipe</label>
        <input type="text" name="nama_tipe" required maxlength="50" placeholder="Standard / Deluxe / Suite">
        <label>Deskripsi / Fasilitas</label>
        <textarea name="deskripsi" rows="3"></textarea>
        <div class="dua-kolom">
            <div><label>Harga per Malam (Rp)</label><input type="number" name="harga_malam" required min="0"></div>
            <div><label>Kapasitas (tamu)</label><input type="number" name="kapasitas" required min="1" max="20"></div>
        </div>
        <label>Foto (JPG/PNG/GIF, maks 2 MB)</label>
        <input type="file" name="foto" accept="image/*">
        <div class="aksi-form">
            <input type="submit" name="simpan" value="Simpan" class="tombol">
            <a href="index.php?p=tipe" class="tombol tombol-abu">Batal</a>
        </div>
    </form>
</div>
