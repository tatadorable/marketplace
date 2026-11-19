<?php
require 'config/koneksi.php';
require 'includes/functions.php';
$judul = 'Beranda';
$q = trim($_GET['q'] ?? '');
$kat = (int)($_GET['kategori'] ?? 0);
$sql = "SELECT p.*, k.nama_kategori, pj.nama_toko FROM produk p
        JOIN kategori k ON k.id_kategori = p.id_kategori
        JOIN penjual pj ON pj.id_penjual = p.id_penjual
        WHERE p.status = 'aktif'";
$types = ''; $params = [];
if ($q !== '') { $sql .= " AND p.nama_produk LIKE ?"; $types .= 's'; $params[] = "%$q%"; }
if ($kat > 0) { $sql .= " AND p.id_kategori = ?"; $types .= 'i'; $params[] = $kat; }
$sql .= " ORDER BY p.created_at DESC, p.id_produk DESC";
$stmt = $db->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$produk = $stmt->get_result();
$kategori = $db->query("SELECT * FROM kategori ORDER BY id_kategori");
$kategoriList = [];
while ($k = $kategori->fetch_assoc()) $kategoriList[] = $k;
$icons = ['makanan'=>'🍓','pakaian'=>'👕','skincare'=>'🧴','figure'=>'🧸','figures'=>'🧸'];
$classes = ['makanan'=>'cat-food','pakaian'=>'cat-fashion','skincare'=>'cat-beauty','figure'=>'cat-figure','figures'=>'cat-figure'];
include 'includes/header.php';
?>
<section class="hero">
 <div class="hero-copy">
  <span class="eyebrow">✿ LITTLE FINDS, BIG HAPPINESS</span>
  <h1>Belanja hal favoritmu,<br><span>jadi lebih seru!</span></h1>
  <p>Dari camilan enak sampai figure karakter kesayangan, temukan semuanya di satu tempat.</p>
  <a class="btn-toko" href="#produk">Jelajahi Tokoharu <span class="ms-1">→</span></a>
 </div>
 <div class="hero-sticker">Your happy little store ♡</div>
 <div class="hero-art" aria-hidden="true">🛍️🌷</div>
</section>
<section id="kategori">
 <div class="section-head"><div><h2>Belanja berdasarkan kategori</h2><p>Pilih yang lagi kamu cari hari ini</p></div></div>
 <div class="category-grid">
 <?php foreach ($kategoriList as $k): $key = strtolower($k['nama_kategori']); $icon = $icons[$key] ?? '🎀'; $class = $classes[$key] ?? 'cat-fashion'; ?>
  <a class="category-tile <?= e($class) ?>" href="index.php?kategori=<?= (int)$k['id_kategori'] ?>#produk"><span class="emoji"><?= $icon ?></span><strong><?= e($k['nama_kategori']) ?></strong></a>
 <?php endforeach; ?>
 </div>
</section>
<section id="produk">
 <div class="section-head"><div><h2><?= $q !== '' || $kat > 0 ? 'Hasil pencarian produk' : 'Temukan favoritmu' ?></h2><p>Barang pilihan yang mungkin kamu suka</p></div><span class="text-link"><?= $produk->num_rows ?> produk</span></div>
 <form class="product-tools" method="get">
  <div class="search-box"><input class="form-control" name="q" placeholder="Cari makanan, outfit, skincare, figure..." value="<?= e($q) ?>"></div>
  <select class="form-select" name="kategori" style="max-width:210px"><option value="0">Semua kategori</option><?php foreach ($kategoriList as $k): ?><option value="<?= (int)$k['id_kategori'] ?>" <?= $kat == $k['id_kategori'] ? 'selected' : '' ?>><?= e($k['nama_kategori']) ?></option><?php endforeach; ?></select>
  <button class="btn-toko" type="submit">Cari produk</button>
  <?php if ($q !== '' || $kat > 0): ?><a class="btn-toko-outline" href="index.php">Reset</a><?php endif; ?>
 </form>
 <?php if ($produk->num_rows === 0): ?>
  <div class="empty-state"><span class="emoji">🔎</span><strong>Belum menemukan produk yang cocok</strong><p class="mb-0 mt-2">Coba kata kunci atau kategori lainnya, ya.</p></div>
 <?php else: ?>
 <div class="product-grid">
 <?php while ($p = $produk->fetch_assoc()): ?>
  <article class="product-card">
   <a href="produk_detail.php?id=<?= (int)$p['id_produk'] ?>"><div class="product-image"><?php if ($p['gambar']): ?><img src="<?= e(gambar_url($p['gambar'])) ?>" alt="<?= e($p['nama_produk']) ?>" loading="lazy"><?php else: ?><span class="product-placeholder"><?= $icons[strtolower($p['nama_kategori'])] ?? '🎀' ?></span><?php endif; ?></div></a>
   <div class="product-info"><span class="product-category"><?= e($p['nama_kategori']) ?></span><div class="product-title"><?= e($p['nama_produk']) ?></div><div class="product-seller"><?= e($p['nama_toko']) ?></div><div class="product-price"><?= rupiah($p['harga']) ?></div>
    <div class="product-actions"><a href="produk_detail.php?id=<?= (int)$p['id_produk'] ?>" class="btn btn-toko-outline">Detail</a>
    <?php if ((int)$p['stok'] > 0): ?><form method="post" action="keranjang.php" class="flex-fill"><?= csrf_field() ?><input type="hidden" name="aksi" value="tambah"><input type="hidden" name="id" value="<?= (int)$p['id_produk'] ?>"><input type="hidden" name="jumlah" value="1"><input type="hidden" name="kembali" value="index"><button class="btn btn-toko w-100">+ Keranjang</button></form><?php else: ?><button class="btn btn-secondary" disabled>Habis</button><?php endif; ?>
    </div>
   </div>
  </article>
 <?php endwhile; ?>
 </div>
 <?php endif; ?>
</section>
<?php include 'includes/footer.php'; ?>
