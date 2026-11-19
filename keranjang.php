<?php
require 'config/koneksi.php';
require 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = $_POST['aksi'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $jml = max(1, (int)($_POST['jumlah'] ?? 1));

    $stmt = $db->prepare("SELECT stok FROM produk WHERE id_produk=? AND status='aktif'");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($aksi === 'tambah' && $row) {
        $baru = ($_SESSION['cart'][$id] ?? 0) + $jml;
        if ($baru > $row['stok']) { $baru = (int)$row['stok']; flash('Jumlah disesuaikan dengan stok tersedia.', 'warning'); }
        else flash('Produk ditambahkan ke keranjang.');
        $_SESSION['cart'][$id] = $baru;
    } elseif ($aksi === 'ubah' && $row) {
        $_SESSION['cart'][$id] = min($jml, (int)$row['stok']);
    } elseif ($aksi === 'hapus') {
        unset($_SESSION['cart'][$id]);
        flash('Produk dihapus dari keranjang.');
    }

    // Setelah "tambah", kembali ke halaman asal; aksi lain tetap di keranjang
    $tujuan = '/keranjang.php';
    if ($aksi === 'tambah') {
        $kembali = $_POST['kembali'] ?? '';
        if ($kembali === 'index')      $tujuan = '/index.php';
        elseif ($kembali === 'detail') $tujuan = '/produk_detail.php?id=' . $id;
    }
    redirect($tujuan);
}

$items = []; $total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $in = implode(',', $ids);
    $res = $db->query("SELECT * FROM produk WHERE id_produk IN ($in) AND status='aktif'");
    while ($p = $res->fetch_assoc()) {
        $p['qty'] = min($_SESSION['cart'][$p['id_produk']], $p['stok']);
        $p['subtotal'] = $p['qty'] * $p['harga'];
        $total += $p['subtotal'];
        $items[] = $p;
    }
}
$judul = 'Keranjang Belanja';
include 'includes/header.php';
?>
<h3 class="mb-3">Keranjang Belanja</h3>
<?php if (!$items): ?>
  <div class="alert alert-info">Keranjang masih kosong. <a href="index.php">Belanja sekarang</a></div>
<?php else: ?>
<div class="table-responsive"><table class="table table-bordered bg-white align-middle">
  <thead class="table-success"><tr><th>Produk</th><th>Harga</th><th style="width:150px">Jumlah</th><th>Subtotal</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($items as $p): ?>
    <tr>
      <td><?= e($p['nama_produk']) ?></td>
      <td><?= rupiah($p['harga']) ?></td>
      <td>
        <form method="post" class="d-flex gap-1">
          <?= csrf_field() ?><input type="hidden" name="aksi" value="ubah"><input type="hidden" name="id" value="<?= $p['id_produk'] ?>">
          <input type="number" name="jumlah" value="<?= $p['qty'] ?>" min="1" max="<?= (int)$p['stok'] ?>" class="form-control form-control-sm">
          <button class="btn btn-sm btn-outline-secondary">Ubah</button>
        </form>
      </td>
      <td><?= rupiah($p['subtotal']) ?></td>
      <td>
        <form method="post"><?= csrf_field() ?><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $p['id_produk'] ?>">
          <button class="btn btn-sm btn-danger">Hapus</button></form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
  <tfoot><tr><th colspan="3" class="text-end">Total</th><th colspan="2"><?= rupiah($total) ?></th></tr></tfoot>
</table></div>
<div class="d-flex justify-content-between">
  <a href="index.php" class="btn btn-outline-secondary">Lanjut Belanja</a>
  <a href="checkout.php" class="btn btn-success">Checkout</a>
</div>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>