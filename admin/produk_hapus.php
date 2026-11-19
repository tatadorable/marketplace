<?php
require '../config/koneksi.php';
require '../includes/functions.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/produk.php');
csrf_check();
$id = (int)($_POST['id'] ?? 0);
$idPenjual = (int)$_SESSION['penjual']['id'];
try {
    $stmt = $db->prepare("DELETE FROM produk WHERE id_produk=? AND id_penjual=?");
    $stmt->bind_param('ii', $id, $idPenjual);
    $stmt->execute();
    flash('Produk dihapus.');
} catch (mysqli_sql_exception $e) {
    // Produk sudah pernah dipesan (terikat foreign key) -> nonaktifkan saja
    $stmt = $db->prepare("UPDATE produk SET status='nonaktif' WHERE id_produk=? AND id_penjual=?");
    $stmt->bind_param('ii', $id, $idPenjual);
    $stmt->execute();
    flash('Produk sudah pernah dipesan, jadi dinonaktifkan (tidak dihapus).', 'warning');
}
redirect('/admin/produk.php');
