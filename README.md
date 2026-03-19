# Takar POS Laravel - Sistem Kasir, Manajemen Produk, dan Transaksi

Takar POS Laravel adalah aplikasi point of sale berbasis web untuk membantu toko, kafe, atau usaha F&B mengelola transaksi penjualan, produk, kategori, stok, dan rekap penjualan dalam satu sistem terintegrasi.

Proyek ini dirancang untuk memberikan pengalaman kasir yang cepat dan modern, lengkap dengan dashboard penjualan, checkout interaktif, cetak struk, riwayat transaksi, serta pengaturan toko yang dapat diubah dari panel admin.

---

## Fitur Utama

- Dashboard ringkas untuk memantau pendapatan hari ini, jumlah transaksi hari ini, total produk, total kategori, grafik penjualan 7 hari terakhir, produk terlaris, dan transaksi terbaru.
- Halaman POS interaktif dengan pencarian produk, filter kategori, keranjang belanja, input pembayaran, hitung kembalian otomatis, serta checkout cepat.
- Dukungan diskon transaksi dalam nominal rupiah maupun persentase.
- Dukungan pajak transaksi yang dapat diaktifkan atau dinonaktifkan dari pengaturan aplikasi.
- Manajemen kategori produk.
- Manajemen produk lengkap dengan SKU, kategori, harga jual, harga beli, stok, dan upload gambar produk.
- Stok produk otomatis berkurang setiap kali transaksi berhasil diproses.
- Riwayat transaksi lengkap dengan detail item, subtotal, diskon, pajak, total, pembayaran, dan kembalian.
- Filter transaksi berdasarkan kata kunci, tanggal awal, dan tanggal akhir.
- Export data transaksi ke file CSV UTF-8 yang siap dibuka di Excel.
- Cetak struk transaksi dari halaman kasir maupun halaman detail transaksi.
- Pengaturan toko meliputi nama toko, alamat, nomor telepon, pesan footer struk, pajak, dan tema tampilan.
- Hak akses berbasis role antara admin dan kasir.
- Profil pengguna untuk update nama, email, password, dan hapus akun.
- Tampilan admin responsif berbasis Inertia.js dan Vue.

---

## Modul Sistem

- Login dan autentikasi pengguna
- Dashboard
- POS / Kasir
- Keranjang dan checkout
- Cetak struk transaksi
- Manajemen kategori
- Manajemen produk
- Upload gambar produk
- Riwayat transaksi
- Detail transaksi
- Export transaksi CSV
- Pengaturan toko
- Profil pengguna

---

## Role Pengguna

- Admin
- Cashier / Kasir

Setiap role memiliki cakupan akses berbeda. Admin dapat mengakses seluruh modul termasuk kategori, produk, transaksi, export, dan pengaturan toko. Kasir difokuskan pada dashboard, transaksi POS, cetak struk, dan pengelolaan profil akun.

---

## Tech Stack

- Backend: Laravel 12
- Frontend: Vue 3 + Inertia.js 2
- Styling: Tailwind CSS
- Build Tool: Vite 7
- Database Lokal Default: SQLite
- Authentication Starter: Laravel Breeze
- Utility Routing Frontend: Ziggy

---

## Cara Menjalankan Secara Lokal

Opsi setup cepat:

```bash
composer run setup
php artisan migrate --seed
php artisan storage:link
```

Jika ingin setup manual, ikuti langkah berikut.

### 1. Clone repo ini

```bash
git clone https://github.com/ichzid/takar-pos.git
cd takar-pos
```

### 2. Install dependensi backend dan frontend

```bash
composer install
npm install
```

### 3. Siapkan file environment

```bash
cp .env.example .env
```

Jika menggunakan Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Lalu generate application key:

```bash
php artisan key:generate
```

### 4. Siapkan database SQLite

Buat file database SQLite:

```bash
touch database/database.sqlite
```

Jika menggunakan Windows PowerShell:

```powershell
New-Item -ItemType File -Path database/database.sqlite -Force
```

Pastikan konfigurasi database di file `.env` menggunakan SQLite, misalnya:

```env
DB_CONNECTION=sqlite
```

### 5. Jalankan migrasi, seeder, dan storage link

```bash
php artisan migrate --seed
php artisan storage:link
```

Seeder akan membuat data awal berupa akun pengguna, kategori, produk menu kafe, dan riwayat transaksi contoh.

### 6. Jalankan aplikasi

Opsi paling praktis:

```bash
composer run dev
```

Atau jalankan manual:

```bash
php artisan serve
npm run dev
```

Aplikasi akan berjalan di `http://localhost:8000` untuk server Laravel dan Vite untuk asset development.

---

## Akun Demo Seeder

Setelah menjalankan `php artisan migrate --seed`, akun demo yang tersedia adalah:

```txt
Admin
Email    : admin@example.com
Password : password

Kasir Utama
Email    : cashier@example.com
Password : password

Kasir Shift Pagi
Email    : kasir.pagi@example.com
Password : password

Kasir Shift Sore
Email    : kasir.sore@example.com
Password : password
```

---

## Data Awal Seeder

Seeder bawaan project ini menyiapkan:

- 9 kategori produk
- 44 produk contoh untuk kebutuhan kafe dan F&B
- 240 transaksi dummy historis
- Pengaturan toko default seperti nama toko, pesan struk, pajak 10%, dan tema aplikasi

---

## Catatan Penggunaan

- Upload gambar produk dibatasi untuk file gambar dengan ukuran maksimal 2 MB.
- Produk dapat dicari dari halaman POS maupun halaman manajemen produk berdasarkan nama atau SKU.
- Produk dengan stok habis tidak dapat ditambahkan ke keranjang.
- Stok akan otomatis dikurangi ketika checkout berhasil.
- Checkout akan ditolak jika jumlah pembayaran kurang dari total transaksi.
- Diskon dapat diinput dalam nominal rupiah atau persentase.
- Pajak default aplikasi adalah 10% dan dapat diubah dari menu pengaturan.
- Export transaksi menghasilkan file CSV dengan BOM UTF-8 agar lebih nyaman dibuka di Microsoft Excel.
- Jalankan `php artisan storage:link` jika ingin gambar produk dapat diakses dari browser.

---

## Testing

Untuk menjalankan pengujian:

```bash
php artisan test
```

Project ini sudah memiliki feature test untuk autentikasi, profil pengguna, dan alur dasar POS seperti akses role, halaman kasir, API produk, dan halaman struk transaksi.

---

## Pengembangan Lanjutan

Beberapa area yang masih bisa dikembangkan lebih lanjut:

- Dukungan multi-outlet atau multi-cabang.
- Manajemen supplier dan pembelian stok.
- Laporan laba rugi berdasarkan harga beli dan harga jual.
- Print struk langsung ke thermal printer.
- Dukungan metode pembayaran non-tunai.
- Void transaksi, retur, dan refund.
- Audit log aktivitas pengguna.
- Dashboard statistik yang lebih mendalam untuk pemilik usaha.

---

## Dukung Pengembangan

Jika proyek ini bermanfaat dan Anda ingin mendukung pengembangan selanjutnya, Anda dapat memberikan dukungan melalui Saweria:

https://saweria.co/ichzid

---

Dibuat untuk membantu operasional kasir, pengelolaan produk, dan pencatatan transaksi penjualan secara lebih rapi, modern, dan terintegrasi.
