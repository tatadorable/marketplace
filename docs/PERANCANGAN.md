# Dokumen Perancangan Web Marketplace Tradisional

## A. Analisis dan Perancangan Sistem

### a. Identifikasi Entitas
| No | Entitas | Keterangan |
|----|---------|------------|
| 1 | Penjual | Pemilik toko yang login dan mengelola produk |
| 2 | Kategori | Pengelompokan produk |
| 3 | Produk | Barang yang dijual |
| 4 | Pembeli | Data pemesan saat checkout |
| 5 | Pesanan | Header transaksi pemesanan |
| 6 | Detail Pesanan | Rincian produk pada tiap pesanan |

### b. Rancangan Database Relasional
**penjual**: `id_penjual` (PK), nama_toko, email (unique), password (hash), created_at

**kategori**: `id_kategori` (PK), nama_kategori

**produk**: `id_produk` (PK), `id_penjual` (FK -> penjual), `id_kategori` (FK -> kategori), nama_produk, deskripsi, harga, stok, gambar, status, created_at

**pembeli**: `id_pembeli` (PK), nama, email, telepon, alamat, created_at

**pesanan**: `id_pesanan` (PK), kode_pesanan (unique), `id_pembeli` (FK -> pembeli), tanggal, total, status

**detail_pesanan**: `id_detail` (PK), `id_pesanan` (FK -> pesanan), `id_produk` (FK -> produk), jumlah, harga_satuan, subtotal

**Relasi**
- penjual 1 : N produk
- kategori 1 : N produk
- pembeli 1 : N pesanan
- pesanan 1 : N detail_pesanan
- produk 1 : N detail_pesanan
- (pesanan M : N produk diselesaikan oleh tabel detail_pesanan)

### c. ERD
```mermaid
erDiagram
    PENJUAL ||--o{ PRODUK : menjual
    KATEGORI ||--o{ PRODUK : mengelompokkan
    PEMBELI ||--o{ PESANAN : membuat
    PESANAN ||--|{ DETAIL_PESANAN : memiliki
    PRODUK ||--o{ DETAIL_PESANAN : dipesan_dalam
    PENJUAL { int id_penjual PK
      string nama_toko
      string email
      string password }
    KATEGORI { int id_kategori PK
      string nama_kategori }
    PRODUK { int id_produk PK
      int id_penjual FK
      int id_kategori FK
      string nama_produk
      text deskripsi
      decimal harga
      int stok
      string gambar
      string status }
    PEMBELI { int id_pembeli PK
      string nama
      string email
      string telepon
      text alamat }
    PESANAN { int id_pesanan PK
      string kode_pesanan
      int id_pembeli FK
      datetime tanggal
      decimal total
      string status }
    DETAIL_PESANAN { int id_detail PK
      int id_pesanan FK
      int id_produk FK
      int jumlah
      decimal harga_satuan
      decimal subtotal }
```
(Tempel kode Mermaid di https://mermaid.live untuk mengekspor gambar ERD.)

### d. Alur Proses Transaksi
```mermaid
flowchart TD
    A([Mulai]) --> B[Pembeli membuka daftar produk]
    B --> C[Pilih produk, lihat detail]
    C --> D{Stok tersedia?}
    D -- Tidak --> B
    D -- Ya --> E[Tambah ke keranjang]
    E --> F{Belanja lagi?}
    F -- Ya --> B
    F -- Tidak --> G[Buka keranjang, ubah/hapus item]
    G --> H[Checkout: isi nama, email, telepon, alamat]
    H --> I{Data valid?}
    I -- Tidak --> H
    I -- Ya --> J[Mulai transaksi database]
    J --> K[Simpan pembeli dan pesanan]
    K --> L[Cek stok, simpan detail pesanan, kurangi stok]
    L --> M{Semua berhasil?}
    M -- Tidak --> N[Rollback, tampilkan pesan error]
    M -- Ya --> O[Commit, kosongkan keranjang]
    O --> P[Tampilkan halaman pesanan berhasil]
    P --> Q([Selesai])
```

### e. Mockup UI
**Daftar Produk**
```
+--------------------------------------------------------------+
| Pasar Online                  [Keranjang (2)] [Login Penjual]|
+--------------------------------------------------------------+
| Daftar Produk                                                |
| [ Cari produk...        ] [ Semua kategori v ] [ Cari ]      |
| +--------+ +--------+ +--------+ +--------+                  |
| | gambar | | gambar | | gambar | | gambar |                  |
| | Nama   | | Nama   | | Nama   | | Nama   |                  |
| | Rp xxx | | Rp xxx | | Rp xxx | | Rp xxx |                  |
| |[Detail]| |[Detail]| |[Detail]| |[Detail]|                  |
| +--------+ +--------+ +--------+ +--------+                  |
+--------------------------------------------------------------+
```
**Detail Produk**
```
+--------------------------------------------------------------+
| < Kembali                                                    |
| +-----------------+  [Kategori]                              |
| |                 |  Nama Produk                             |
| |     gambar      |  Penjual: Toko ...                       |
| |                 |  Rp xxx                                  |
| +-----------------+  Deskripsi ...   Stok: 50                |
|                      [ 1 ] [ + Keranjang ]                   |
+--------------------------------------------------------------+
```
**Keranjang Belanja**
```
+--------------------------------------------------------------+
| Keranjang Belanja                                            |
| Produk      | Harga   | Jumlah        | Subtotal | Aksi      |
| Kopi 250g   | 45.000  | [2][Ubah]     | 90.000   | [Hapus]   |
| Kaos        | 85.000  | [1][Ubah]     | 85.000   | [Hapus]   |
|                                  Total: Rp 175.000           |
| [Lanjut Belanja]                            [Checkout]       |
+--------------------------------------------------------------+
```
