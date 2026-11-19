<?php
require 'config/koneksi.php';
require 'includes/functions.php';
if (empty($_SESSION['cart'])) redirect('/keranjang.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nama = trim($_POST['nama'] ?? ''); $email = trim($_POST['email'] ?? '');
    $telp = trim($_POST['telepon'] ?? ''); $alamat = trim($_POST['alamat'] ?? '');
    if ($nama === '' || $telp === '' || $alamat === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('Lengkapi data dengan benar.', 'danger');
    } else {
        try {
            $db->begin_transaction();
            $stmtPembeli = $db->prepare("INSERT INTO pembeli (nama,email,telepon,alamat) VALUES (?,?,?,?)");
            $stmtPembeli->bind_param('ssss', $nama, $email, $telp, $alamat);
            $stmtPembeli->execute();
            $idPembeli = $db->insert_id;

            $kode = 'INV' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
            $nol = 0;
            $stmtPesanan = $db->prepare("INSERT INTO pesanan (kode_pesanan,id_pembeli,total) VALUES (?,?,?)");
            $stmtPesanan->bind_param('sii', $kode, $idPembeli, $nol);
            $stmtPesanan->execute();
            $idPesanan = $db->insert_id;

            $total = 0;
            $sel = $db->prepare("SELECT nama_produk, harga, stok FROM produk WHERE id_produk=? AND status='aktif' FOR UPDATE");
            $det = $db->prepare("INSERT INTO detail_pesanan (id_pesanan,id_produk,jumlah,harga_satuan,subtotal) VALUES (?,?,?,?,?)");
            $upd = $db->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk=?");
            foreach ($_SESSION['cart'] as $idp => $qty) {
                $idp = (int)$idp; $qty = (int)$qty;
                $sel->bind_param('i', $idp); $sel->execute();
                $p = $sel->get_result()->fetch_assoc();
                if (!$p || $p['stok'] < $qty) throw new Exception('Stok tidak mencukupi untuk: ' . ($p['nama_produk'] ?? "produk #$idp"));
                $sub = $p['harga'] * $qty; $total += $sub;
                $det->bind_param('iiiii', $idPesanan, $idp, $qty, $p['harga'], $sub); $det->execute();
                $upd->bind_param('ii', $qty, $idp); $upd->execute();
            }
            $fin = $db->prepare("UPDATE pesanan SET total=? WHERE id_pesanan=?");
            $fin->bind_param('ii', $total, $idPesanan); $fin->execute();
            $db->commit();
            unset($_SESSION['cart']);
            redirect('/pesanan_sukses.php?kode=' . urlencode($kode));
        } catch (Throwable $ex) {
            $db->rollback();
            flash($ex instanceof mysqli_sql_exception ? 'Terjadi kesalahan saat menyimpan pesanan.' : $ex->getMessage(), 'danger');
        }
    }
}
$judul = 'Checkout';
include 'includes/header.php';
?>
<h3 class="mb-3">Checkout</h3>
<div class="card shadow-sm" style="max-width:600px"><div class="card-body">
<form method="post">
  <?= csrf_field() ?>
  <div class="mb-3"><label class="form-label">Nama Lengkap</label><input name="nama" class="form-control" required value="<?= e($_POST['nama'] ?? '') ?>"></div>
  <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>"></div>
  <div class="mb-3"><label class="form-label">No. Telepon</label><input name="telepon" class="form-control" required value="<?= e($_POST['telepon'] ?? '') ?>"></div>
  <div class="mb-3"><label class="form-label">Alamat Pengiriman</label><textarea name="alamat" class="form-control" rows="3" required><?= e($_POST['alamat'] ?? '') ?></textarea></div>
  <button class="btn btn-success">Buat Pesanan</button>
  <a href="keranjang.php" class="btn btn-link">Kembali</a>
</form></div></div>
<?php include 'includes/footer.php'; ?>
