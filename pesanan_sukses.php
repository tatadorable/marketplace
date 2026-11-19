<?php
require 'config/koneksi.php';
require 'includes/functions.php';
$kode = $_GET['kode'] ?? '';
$stmt = $db->prepare("SELECT ps.*, pb.nama, pb.alamat FROM pesanan ps JOIN pembeli pb ON pb.id_pembeli=ps.id_pembeli WHERE ps.kode_pesanan=?");
$stmt->bind_param('s', $kode); $stmt->execute();
$ps = $stmt->get_result()->fetch_assoc();
if (!$ps) redirect('/index.php');
$stmt = $db->prepare("SELECT d.*, p.nama_produk FROM detail_pesanan d JOIN produk p ON p.id_produk=d.id_produk WHERE d.id_pesanan=?");
$stmt->bind_param('i', $ps['id_pesanan']); $stmt->execute();
$det = $stmt->get_result();
$judul = 'Pesanan Berhasil';
include 'includes/header.php';
?>
<div class="alert alert-success"><strong>Pesanan berhasil dibuat!</strong> Kode pesanan: <?= e($ps['kode_pesanan']) ?></div>
<p>Atas nama <strong><?= e($ps['nama']) ?></strong><br>Dikirim ke: <?= e($ps['alamat']) ?></p>
<table class="table bg-white table-bordered"><thead class="table-success"><tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>
<?php while ($d = $det->fetch_assoc()): ?>
<tr><td><?= e($d['nama_produk']) ?></td><td><?= $d['jumlah'] ?></td><td><?= rupiah($d['harga_satuan']) ?></td><td><?= rupiah($d['subtotal']) ?></td></tr>
<?php endwhile; ?>
</tbody><tfoot><tr><th colspan="3" class="text-end">Total</th><th><?= rupiah($ps['total']) ?></th></tr></tfoot></table>
<a href="index.php" class="btn btn-success">Kembali ke Beranda</a>
<?php include 'includes/footer.php'; ?>
