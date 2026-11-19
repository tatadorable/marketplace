# Tokoharu — Marketplace PHP Native + MySQLi + Bootstrap 5

Tokoharu adalah marketplace multi-kategori dengan tampilan pastel modern-minimalis. Kategori awal: Makanan, Pakaian, Skincare, dan Figure.

## Fitur
- Katalog produk, pencarian, dan filter kategori
- Detail produk dan keranjang berbasis session
- Checkout dan penyimpanan transaksi menggunakan transaksi database
- Login penjual, CRUD produk, upload gambar, dan pengelolaan status pesanan
- Layout responsif untuk desktop dan perangkat mobile

## Menjalankan di lokal (XAMPP/Laragon)
1. Ekstrak folder `marketplace` ke direktori `htdocs`.
2. Untuk instalasi baru, buat database bernama `marketplace` melalui phpMyAdmin, lalu import `database.sql`. Jika memakai database lama, backup dahulu lalu tinjau/jalankan `database_update_tokoharu.sql`; sesuaikan pemetaan kategori dengan produk yang sudah ada.
3. Salin `config/config.example.php` menjadi `config/config.php` dan sesuaikan kredensial database. Untuk lokal XAMPP biasanya host `localhost`, user `root`, password kosong.
4. Buka `http://localhost/marketplace`.
5. Login penjual awal: `admin@toko.test` / `password`. Segera ganti password sebelum website dipublikasikan.

## Deploy ke hosting
1. Buat database melalui panel hosting dan import `database.sql` melalui phpMyAdmin.
2. Upload isi folder project ke document root hosting (umumnya `htdocs`).
3. Buat `config/config.php` di server berdasarkan `config/config.example.php`, lalu isi host, nama database, user, dan password dari panel hosting.
4. Atur `BASE_URL` menjadi `''` jika website berada langsung di root domain, atau isi path subfolder jika diperlukan.
5. Pastikan folder `uploads` dapat ditulis oleh PHP (umumnya permission 755/775 sesuai ketentuan hosting).
6. Ganti password penjual awal dan uji katalog, keranjang, checkout, serta halaman admin.

## Keamanan konfigurasi
- Jangan mengunggah `config/config.php` ke GitHub atau membagikan file tersebut.
- `.gitignore` sudah mengecualikan `config/config.php` dan isi folder `uploads`.
- ZIP distribusi ini tidak menyertakan kredensial database hosting.

## Struktur penting
- `index.php`: katalog dan homepage Tokoharu
- `produk_detail.php`, `keranjang.php`, `checkout.php`: alur belanja
- `admin/`: pengelolaan produk dan pesanan
- `assets/css/tokoharu.css`: tema visual Tokoharu
- `database.sql`: skema dan data awal
