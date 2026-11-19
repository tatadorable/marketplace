<?php
require '../config/koneksi.php';
require '../includes/functions.php';
require_login();
$idPenjual = (int)$_SESSION['penjual']['id'];
$id = (int)($_GET['id'] ?? 0);
$p = ['nama_produk'=>'','deskripsi'=>'','harga'=>'','stok'=>'','id_kategori'=>'','gambar'=>null];
if ($id) {
    $stmt = $db->prepare("SELECT * FROM produk WHERE id_produk=? AND id_penjual=?");
    $stmt->bind_param('ii', $id, $idPenjual); $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    if (!$p) redirect('/admin/produk.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nama = trim($_POST['nama_produk'] ?? ''); $desk = trim($_POST['deskripsi'] ?? '');
    $harga = (int)($_POST['harga'] ?? 0); $stok = (int)($_POST['stok'] ?? 0);
    $kat = (int)($_POST['id_kategori'] ?? 0);
    $gambar = $p['gambar'];
    $err = '';

    if ($nama === '' || $harga <= 0 || $stok < 0 || $kat <= 0) $err = 'Data belum valid.';

    if (!$err && !empty($_FILES['gambar']['name'])) {
        $f = $_FILES['gambar'];
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $info = $f['error'] === UPLOAD_ERR_OK ? @getimagesize($f['tmp_name']) : false;
        if (!$info || !in_array($ext, ['jpg','jpeg','png','webp']) || $f['size'] > 2*1024*1024) {
            $err = 'Gambar harus JPG/PNG/WEBP dan maksimal 2 MB.';
        } else {
            $baru = bin2hex(random_bytes(8)) . '.' . $ext;
            if (move_uploaded_file($f['tmp_name'], __DIR__ . '/../uploads/' . $baru)) {
                if ($gambar) @unlink(__DIR__ . '/../uploads/' . basename($gambar));
                $gambar = $baru;
            } else $err = 'Gagal mengunggah gambar.';
        }
    }

    if ($err) { flash($err, 'danger'); }
    else {
        if ($id) {
            $stmt = $db->prepare("UPDATE produk SET id_kategori=?, nama_produk=?, deskripsi=?, harga=?, stok=?, gambar=? WHERE id_produk=? AND id_penjual=?");
            $stmt->bind_param('issiisii', $kat, $nama, $desk, $harga, $stok, $gambar, $id, $idPenjual);
        } else {
            $status = 'aktif';
            $stmt = $db->prepare("INSERT INTO produk (id_penjual,id_kategori,nama_produk,deskripsi,harga,stok,gambar,status) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->bind_param('iissiiss', $idPenjual, $kat, $nama, $desk, $harga, $stok, $gambar, $status);
        }
        $stmt->execute();
        flash($id ? 'Produk diperbarui.' : 'Produk ditambahkan.');
        redirect('/admin/produk.php');
    }
    $p = array_merge($p, ['nama_produk'=>$nama,'deskripsi'=>$desk,'harga'=>$harga,'stok'=>$stok,'id_kategori'=>$kat]);
}
$kategori = $db->query("SELECT * FROM kategori ORDER BY nama_kategori");
$judul = $id ? 'Edit Produk' : 'Tambah Produk';
include '../includes/header.php';
?>
<h3 class="mb-3"><?= e($judul) ?></h3>
<div class="card shadow-sm" style="max-width:700px"><div class="card-body">
<form method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="mb-3"><label class="form-label">Nama Produk</label><input name="nama_produk" class="form-control" required value="<?= e($p['nama_produk']) ?>"></div>
  <div class="mb-3"><label class="form-label">Kategori</label>
    <select name="id_kategori" class="form-select" required><option value="">-- pilih --</option>
    <?php while ($k = $kategori->fetch_assoc()): ?>
      <option value="<?= $k['id_kategori'] ?>" <?= $p['id_kategori']==$k['id_kategori']?'selected':'' ?>><?= e($k['nama_kategori']) ?></option>
    <?php endwhile; ?></select></div>
  <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="deskripsi" rows="3" class="form-control"><?= e($p['deskripsi']) ?></textarea></div>
  <div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">Harga (Rp)</label><input type="number" min="1" name="harga" class="form-control" required value="<?= e($p['harga']) ?>"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Stok</label><input type="number" min="0" name="stok" class="form-control" required value="<?= e($p['stok']) ?>"></div>
  </div>
  <div class="mb-3"><label class="form-label">Gambar (JPG/PNG/WEBP, maks 2 MB)</label><input type="file" name="gambar" class="form-control" accept="image/*">
    <?php if ($p['gambar']): ?><img src="<?= e(gambar_url($p['gambar'])) ?>" width="100" class="mt-2" alt=""><?php endif; ?></div>
  <button class="btn btn-success">Simpan</button> <a href="produk.php" class="btn btn-link">Batal</a>
</form></div></div>
<?php include '../includes/footer.php'; ?>