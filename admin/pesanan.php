<?php
require '../config/koneksi.php';
require '../includes/functions.php';
require_login();
$judul = 'Data Pesanan';

$statusList = ['baru', 'diproses', 'dikirim', 'selesai'];
$warna = [
    'baru'     => 'secondary',
    'diproses' => 'warning text-dark',
    'dikirim'  => 'info text-dark',
    'selesai'  => 'success',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id_pesanan'] ?? 0);
    $status = $_POST['status'] ?? '';
    if ($id > 0 && in_array($status, $statusList, true)) {
        $up = $db->prepare("UPDATE pesanan SET status = ? WHERE id_pesanan = ?");
        $up->bind_param('si', $status, $id);
        $up->execute();
        flash('Status pesanan diperbarui menjadi "' . $status . '".');
    } else {
        flash('Status tidak valid.', 'danger');
    }
    redirect('/admin/pesanan.php');
}

$res = $db->query("SELECT ps.*, pb.nama, pb.telepon,
    (SELECT GROUP_CONCAT(CONCAT(p.nama_produk,' x',d.jumlah) SEPARATOR ', ')
       FROM detail_pesanan d JOIN produk p ON p.id_produk=d.id_produk WHERE d.id_pesanan=ps.id_pesanan) AS item
    FROM pesanan ps JOIN pembeli pb ON pb.id_pembeli=ps.id_pembeli ORDER BY ps.id_pesanan DESC");
include '../includes/header.php';
?>
<h3 class="mb-3">Data Pesanan</h3>
<div class="table-responsive"><table class="table table-bordered bg-white align-middle">
<thead class="table-success"><tr><th>Kode</th><th>Tanggal</th><th>Pembeli</th><th>Item</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php while ($r = $res->fetch_assoc()): ?>
<tr>
  <td><?= e($r['kode_pesanan']) ?></td>
  <td><?= e($r['tanggal']) ?></td>
  <td><?= e($r['nama']) ?><br><small><?= e($r['telepon']) ?></small></td>
  <td><?= e($r['item']) ?></td>
  <td><?= rupiah($r['total']) ?></td>
  <td><span class="badge bg-<?= $warna[$r['status']] ?? 'secondary' ?>"><?= e(ucfirst($r['status'])) ?></span></td>
  <td>
    <form method="post" class="d-flex gap-1">
      <?= csrf_field() ?>
      <input type="hidden" name="id_pesanan" value="<?= (int)$r['id_pesanan'] ?>">
      <select name="status" class="form-select form-select-sm">
        <?php foreach ($statusList as $s): ?>
          <option value="<?= $s ?>" <?= $r['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-success btn-sm">Simpan</button>
    </form>
  </td>
</tr>
<?php endwhile; ?>
</tbody></table></div>
<?php include '../includes/footer.php'; ?>