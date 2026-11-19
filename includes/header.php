<?php
require_once __DIR__ . '/functions.php';
$isPenjual = !empty($_SESSION['penjual']);
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#fff0f5">
<title><?= e($judul ?? 'Beranda') ?> - Tokoharu</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/tokoharu.css" rel="stylesheet">
</head>
<body>
<header class="site-header">
<nav class="navbar navbar-expand-lg">
 <div class="container">
  <a class="navbar-brand" href="<?= BASE_URL ?>/index.php"><span class="brand-mark">🌸</span>Tokoharu</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Buka navigasi"><span class="navbar-toggler-icon"></span></button>
  <div class="collapse navbar-collapse" id="mainNav">
   <ul class="navbar-nav mx-auto">
    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php">Beranda</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php#kategori">Kategori</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php#produk">Produk</a></li>
   </ul>
   <div class="nav-actions">
   <?php if ($isPenjual): ?>
    <a class="icon-link" href="<?= BASE_URL ?>/admin/produk.php" title="Kelola produk">Produk</a>
    <a class="icon-link" href="<?= BASE_URL ?>/admin/pesanan.php" title="Pesanan">Pesanan</a>
    <a class="btn-toko-outline" href="<?= BASE_URL ?>/logout.php">Keluar</a>
   <?php else: ?>
    <a class="icon-link" href="<?= BASE_URL ?>/login.php" title="Login penjual">♙</a>
    <a class="icon-link cart-link" href="<?= BASE_URL ?>/keranjang.php" title="Buka keranjang belanja" aria-label="Keranjang belanja, <?= (int)$cartCount ?> item"><span class="cart-icon" aria-hidden="true">🛒</span><span class="cart-label">Keranjang</span><span class="cart-count"><?= (int)$cartCount ?></span></a>
   <?php endif; ?>
   </div>
  </div>
 </div>
</nav>
</header>
<main class="container page-wrap">
<?php flash(); ?>
