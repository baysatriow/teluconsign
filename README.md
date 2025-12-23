# TeluConsign 🛍️

TeluConsign adalah platform e-commerce berbasis consignment (titip jual) yang dirancang khusus untuk memudahkan transaksi jual beli yang aman, transparan, dan terpercaya. Platform ini mengintegrasikan berbagai layanan pihak ketiga untuk memberikan pengalaman belanja yang premium dan modern.

## 🚀 Fitur Utama

- **Sistem Toko (My Shop)**: Pengguna dapat mendaftar sebagai penjual dan mengelola produk mereka sendiri.
- **Integrasi Pembayaran (Midtrans)**: Transaksi aman dengan berbagai metode pembayaran otomatis.
- **Integrasi Pengiriman (RajaOngkir)**: Perhitungan ongkos kirim real-time dan tracking pengiriman.
- **Notifikasi WhatsApp (Fonnte)**: Verifikasi OTP dan notifikasi transaksi langsung ke WhatsApp pengguna.
- **Fitur AI (Google Gemini)**: Integrasi kecerdasan buatan untuk asisten atau optimasi konten.
- **Manajemen Profil & Alamat**: Pengaturan data diri yang komprehensif dengan sistem verifikasi nomor telepon yang aman.
- **Panel Admin**: Dashboard khusus untuk mengelola user, kategori, dan memoderasi transaksi.

## 🛠️ Stack Teknologi

Aplikasi ini dibangun menggunakan arsitektur modern yang efisien:

- **Backend**: [Laravel 12.x](https://laravel.com/) (PHP)
- **Database**: MySQL / MariaDB
- **Frontend Core**: HTML5 & JavaScript (Vanilla JS)
- **CSS Framework**: [Tailwind CSS](https://tailwindcss.com/) (Load via CDN)
- **UI Components**: [Flowbite](https://flowbite.com/)
- **Utility Libraries**: 
  - [SweetAlert2](https://sweetalert2.github.io/) (Pop-up & Notifikasi)
  - [Animate.css](https://animate.style/) (Animasi)
  - [Google Fonts](https://fonts.google.com/) (Plus Jakarta Sans & Inter)

## 📦 Integrasi API

- **Midtrans**: Payment Gateway
- **RajaOngkir**: Shipping & Courier Data
- **Fonnte**: WhatsApp OTP & Messaging Service
- **Google Gemini**: AI Services

## ⚙️ Instalasi

Ikuti langkah-langkah berikut untuk menjalankan project di lingkungan lokal:

### 1. Persiapan Environment
Pastikan Anda sudah menginstal **PHP >= 8.2**, **Composer**, dan server database (seperti Laragon, XAMPP, atau Docker).

### 2. Clone Repository
```bash
git clone https://github.com/username/teluconsign.git
cd teluconsign
```

### 3. Instalasi Dependencies
Cukup jalankan composer karena frontend menggunakan CDN (tidak wajib `npm install` kecuali ingin modifikasi build tool).
```bash
composer install
```

### 4. Konfigurasi Environment
Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasinya:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan bagian database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=teluconsign
DB_USERNAME=root
DB_PASSWORD=
```

Serta konfigurasi API (Midtrans, RajaOngkir, Fonnte, Gemini) sesuai dengan key yang Anda miliki.

### 5. Migrasi & Seeding
Jalankan migrasi database untuk membuat tabel dan data awal:
```bash
php artisan migrate --seed
```

### 6. Menjalankan Aplikasi
```bash
php artisan serve
```
Aplikasi dapat diakses di `http://127.0.0.1:8000`.

---

## 📝 Catatan Penting
Aplikasi ini dioptimalkan untuk performa tinggi dengan meminimalkan beban di sisi client. Penggunaan Tailwind CSS via CDN memungkinkan deployment yang lebih cepat tanpa perlu mengelola folder `node_modules` yang besar di server development.

**Developed with ❤️ by TeluConsign Team**
