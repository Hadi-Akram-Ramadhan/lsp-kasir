# KasirDoy - Sistem Kasir Restoran

Sistem kasir restoran modern dengan fitur manajemen meja, pesanan, transaksi, dan laporan.

## Fitur Utama

### 1. Manajemen Pengguna
- Multi-role: Administrator, Waiter, Kasir, dan Owner
- Manajemen pengguna (CRUD)
- Sistem login/logout
- Pembatasan akses berdasarkan role

### 2. Manajemen Meja
- Status meja (tersedia, terisi, reserved)
- Manajemen reservasi
- Tampilan layout meja
- Update status meja otomatis

### 3. Manajemen Pesanan
- Buat pesanan baru
- Tambah item ke pesanan
- Update status pesanan
- Batalkan pesanan
- Riwayat pesanan

### 4. Manajemen Produk
- Tambah/edit/hapus produk
- Manajemen stok
- Peringatan stok menipis
- Kategori produk

### 5. Transaksi
- Proses pembayaran
- Multiple metode pembayaran
- Cetak struk
- Riwayat transaksi

### 6. Shift Kasir
- Buka/tutup shift
- Catatan shift
- Laporan per shift

### 7. Laporan
- Laporan penjualan harian
- Laporan produk terlaris
- Laporan pendapatan
- Export laporan ke PDF

### 8. Notifikasi
- Notifikasi pesanan baru
- Peringatan stok menipis
- Notifikasi sistem
- Mark as read

## Teknologi yang Digunakan

- PHP 8.0+
- MySQL 5.7+
- Bootstrap 5.3
- jQuery
- Chart.js
- TCPDF (untuk cetak struk)

## Instalasi

1. Clone repository
```bash
git clone https://github.com/Hadi-Akram-Ramadhan/lsp-kasir.git
```

2. Import database
jalanin aja setup.php, database dan tabel otomatis kebuat

3. Konfigurasi database
- Buka `config/database.php`
- Sesuaikan kredensial database

4. Setup web server
- Pastikan web server (Apache/Nginx) sudah terinstall
- Point document root ke folder project
- Aktifkan mod_rewrite untuk Apache

5. Akses aplikasi
- Buka browser
- Akses `http://localhost/kasirdoy`
- Login dengan kredensial default:
  - Username: admin
  - Password: admin123

## Struktur Database

### Tabel Utama
- `users` - Data pengguna
- `tables` - Data meja
- `products` - Data produk
- `orders` - Data pesanan
- `order_items` - Item pesanan
- `transactions` - Data transaksi
- `shifts` - Data shift kasir
- `notifications` - Data notifikasi
- `activity_logs` - Log aktivitas

## Role dan Hak Akses

### Administrator
- Manajemen pengguna
- Manajemen meja
- Manajemen produk
- Akses semua fitur

### Waiter
- Manajemen pesanan
- Manajemen meja
- Manajemen produk
- Lihat laporan

### Kasir
- Proses pembayaran
- Manajemen shift
- Cetak struk
- Lihat laporan

### Owner
- Lihat laporan
- Lihat notifikasi
- Akses terbatas

## Kontribusi

1. Fork repository
2. Buat branch baru (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -am 'Tambah fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## Changelog

### v1.0.0 (2024-03-20)
- Initial release
- Fitur dasar manajemen restoran
- Multi-role user system
- Sistem notifikasi
- Laporan dasar

//kalo lupa pw login, liat hash yang ada di protect.php
