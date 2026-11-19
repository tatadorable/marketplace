<?php
require '../config/koneksi.php';
require '../includes/functions.php';
require_login();
$judul = 'Kelola Produk';
$idp = (int)$_SESSION['penjual']['id'];
$stmt = $db->prepare("SELECT p.*, k.nama_kategori FROM produk p JOIN kategori k ON k.id_kategori=p.id_kategori WHERE p.id_penjual=? ORDER BY p.id_produk ASC");
$stmt->bind_param('i', $idp); $stmt->execute();
$res = $stmt->get_result();
include '../includes/header.php';
?>
<div class="d-flex justify-content-between mb-3"><h3>Kelola Produk</h3><a href="produk_form.php" class="btn btn-success">+ Tambah Produk</a></div>
<div class="table-responsive"><table class="table table-bordered bg-white align-middle">
<thead class="table-success"><tr><th>#</th><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead><tbody>
<?php $no = 1; while ($p = $res->fetch_assoc()): ?>
<tr>
  <td><?= $no++ ?></td>
  <td><img src="<?= e(gambar_url($p['gambar'])) ?>" width="60" height="45" style="object-fit:cover" alt=""></td>
  <td><?= e($p['nama_produk']) ?></td><td><?= e($p['nama_kategori']) ?></td>
  <td><?= rupiah($p['harga']) ?></td><td><?= (int)$p['stok'] ?></td>
  <td class="text-nowrap">
    <a href="produk_form.php?id=<?= $p['id_produk'] ?>" class="btn btn-sm btn-warning">Edit</a>
    <form method="post" action="produk_hapus.php" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= $p['id_produk'] ?>"><button class="btn btn-sm btn-danger">Hapus</button>
    </form>
  </td>
</tr>
<?php endwhile; ?>
</tbody></table></div>
<?php include '../includes/footer.php'; ?>