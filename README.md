# Tokoharu — Marketplace PHP Native + MySQLi + Bootstrap 5

Tokoharu adalah aplikasi web marketplace sederhana multi-kategori dengan tampilan pastel modern-minimalis. Penjual dapat menawarkan produk, dan pembeli dapat memesan melalui keranjang belanja. Kategori awal: Makanan, Pakaian, Skincare, dan Figure.

- Demo: https://lilgomiw.lovestoblog.com/marketplace/
- Repository: https://github.com/tatadorable/marketplace

## Fitur

- Katalog produk, pencarian, dan filter kategori
- Detail produk dan keranjang berbasis session
- Checkout dan penyimpanan transaksi menggunakan transaksi database (commit/rollback)
- Login penjual, CRUD produk, upload gambar, dan pengelolaan status pesanan
- Layout responsif untuk desktop dan perangkat mobile

## Teknologi

PHP Native, MySQL (MySQLi dengan prepared statement), Bootstrap 5, hosting InfinityFree.

## Menjalankan di lokal (XAMPP/Laragon)

1. Ekstrak folder `marketplace` ke direktori `htdocs`.
2. Buat database bernama `marketplace` melalui phpMyAdmin, lalu import `marketplace.sql` (berisi struktur tabel dan data awal).
3. Salin `config/config.example.php` menjadi `config/config.php` dan sesuaikan kredensial database. Untuk lokal XAMPP biasanya host `localhost`, user `root`, password kosong.
4. Buka `http://localhost/marketplace`.
5. Login penjual awal: `juju@123` / `password`. Segera ganti password sebelum website dipublikasikan.

## Deploy ke hosting (InfinityFree)

1. Buat database MySQL melalui panel hosting dan import `marketplace.sql` melalui phpMyAdmin.
2. Upload isi folder project ke document root hosting (umumnya `htdocs`).
3. Buat `config/config.php` di server berdasarkan `config/config.example.php`, lalu isi host, nama database, user, dan password dari panel hosting.
4. Atur `BASE_URL` menjadi `''` jika website berada langsung di root domain, atau isi path subfolder (misalnya `/marketplace`) jika diperlukan.
5. Pastikan folder `uploads` dapat ditulis oleh PHP (umumnya permission 755/775 sesuai ketentuan hosting).
6. Ganti password penjual awal dan uji katalog, keranjang, checkout, serta halaman admin.

## Keamanan konfigurasi

- Jangan mengunggah `config/config.php` ke GitHub atau membagikan file tersebut. File ini sudah dikecualikan lewat `.gitignore`.
- `config/koneksi.php` hanya berisi nilai bawaan untuk lokal. Jangan menulis password database asli di file tersebut.
- Jika mengunggah lewat fitur drag-and-drop di web GitHub, `.gitignore` tidak berlaku, jadi pastikan `config/config.php` tidak ikut terunggah.
- Repository ini tidak menyertakan kredensial database maupun hosting.

## Struktur penting

- `index.php`: katalog dan homepage Tokoharu
- `produk_detail.php`, `keranjang.php`, `checkout.php`, `pesanan_sukses.php`: alur belanja
- `login.php`, `logout.php`: login dan logout penjual
- `admin/`: pengelolaan produk (CRUD) dan pesanan
- `config/koneksi.php`: koneksi database MySQLi; `config/config.example.php`: contoh konfigurasi
- `includes/`: fungsi bantu (session, CSRF, format rupiah), header, dan footer
- `assets/css/tokoharu.css`: tema visual Tokoharu
- `uploads/`: gambar produk (`.htaccess` memblokir eksekusi PHP)
- `marketplace.sql`: skema dan data awal database
- `docs/PERANCANGAN.md`: dokumen perancangan (ERD, alur, mockup)
