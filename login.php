<?php
require 'config/koneksi.php';
require 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $stmt = $db->prepare("SELECT id_penjual, nama_toko, password FROM penjual WHERE email=?");
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['penjual'] = ['id' => $u['id_penjual'], 'toko' => $u['nama_toko']];
        redirect('/admin/produk.php');
    }
    flash('Email atau password salah.', 'danger');
}
$judul = 'Login Penjual';
include 'includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card shadow-sm"><div class="card-body">
  <h4>Login Penjual</h4>
  <form method="post">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
    <button class="btn btn-success w-100">Masuk</button>
  </form>
</div></div></div></div>
<?php include 'includes/footer.php'; ?>
