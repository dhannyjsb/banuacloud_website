# Banua Cloud Website

Website resmi Banua Cloud yang sekarang dijalankan sebagai aplikasi Laravel 13 dengan frontend Vue 3 dan Vite.

## Stack

- Laravel 13
- Vue 3
- Vite
- Tailwind CSS 4
- Pinia
- Vue Router
- MySQL

## Struktur Aplikasi

- Laravel menjadi web server utama, routing server, dan fondasi backend.
- Vue tetap dipakai untuk SPA frontend dan admin dashboard.
- Entry SPA dirender dari Blade di `resources/views/app.blade.php`.
- Frontend source tetap berada di `src/` dan dibuild lewat Laravel Vite ke `public/build`.

## Menjalankan Lokal

1. Copy env: gunakan `.env.example` sebagai dasar jika perlu.
2. Sesuaikan koneksi MySQL di `.env`:
   `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
3. Install dependency PHP dan JS:
   `composer install`
   `npm install`
4. Generate app key:
   `php artisan key:generate`
5. Jalankan migrasi jika database MySQL sudah siap:
   `php artisan migrate`
6. Jalankan aplikasi:
   `php artisan serve`
   `npm run dev`

Atau gunakan:

`composer run dev`

## Catatan

- Route frontend seperti `/`, `/admin`, dan halaman layanan sekarang dilayani oleh Laravel melalui catch-all route SPA.
- Build produksi frontend dihasilkan ke `public/build`.
- Konfigurasi auth admin saat ini masih memakai mode frontend/dev fallback dan belum dipindahkan penuh ke auth backend Laravel.
