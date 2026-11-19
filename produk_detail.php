<?php
require 'config/koneksi.php';
require 'includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT p.*, k.nama_kategori, pj.nama_toko FROM produk p
    JOIN kategori k ON k.id_kategori=p.id_kategori
    JOIN penjual pj ON pj.id_penjual=p.id_penjual
    WHERE p.id_produk=? AND p.status='aktif'");
$stmt->bind_param('i', $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if (!$p) { http_response_code(404); $judul = 'Tidak ditemukan'; include 'includes/header.php'; echo '<div class="alert alert-warning">Produk tidak ditemukan.</div>'; include 'includes/footer.php'; exit; }
$judul = $p['nama_produk'];
include 'includes/header.php';
?>
<a href="index.php" class="btn btn-link px-0">&larr; Kembali</a>
<div class="card shadow-sm"><div class="row g-0">
  <div class="col-md-5"><img src="<?= e(gambar_url($p['gambar'])) ?>" class="img-fluid rounded-start w-100" alt=""></div>
  <div class="col-md-7"><div class="card-body">
    <span class="badge bg-secondary"><?= e($p['nama_kategori']) ?></span>
    <h3 class="mt-2"><?= e($p['nama_produk']) ?></h3>
    <p class="text-muted mb-1">Penjual: <?= e($p['nama_toko']) ?></p>
    <h4 class="text-success"><?= rupiah($p['harga']) ?></h4>
    <p><?= nl2br(e($p['deskripsi'])) ?></p>
    <p>Stok: <strong><?= (int)$p['stok'] ?></strong></p>
    <?php if ($p['stok'] > 0): ?>
    <form method="post" action="keranjang.php" class="d-flex gap-2" style="max-width:280px">
      <?= csrf_field() ?>
      <input type="hidden" name="aksi" value="tambah">
      <input type="hidden" name="id" value="<?= $p['id_produk'] ?>">
      <input type="hidden" name="kembali" value="detail">
      <input type="number" name="jumlah" value="1" min="1" max="<?= (int)$p['stok'] ?>" class="form-control">
      <button class="btn btn-success text-nowrap">+ Keranjang</button>
    </form>
    <?php else: ?><div class="alert alert-danger">Stok habis.</div><?php endif; ?>
  </div></div>
</div></div>
<?php include 'includes/footer.php'; ?>