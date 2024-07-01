## CARA INSTAL


### 1 Kloning proyek Anda
### 2Buka folder aplikasi yang sudah anda clone dengan terminal atau cmd "user@windows:folder\casemixRSBW:>"
### 3 Jalankan composer install di cmd atau terminal Anda "user@windows:folder\casemixRSBW:>composer install"
### Salin .env.examplefile ke .envfolder root. Anda dapat mengetik copy .env.example .envjika menggunakan command prompt Windows atau cp .env.example .envjika menggunakan terminal, Ubuntu Buka .envfile Anda dan ubah nama database ( DB_DATABASE) menjadi apa pun yang Anda miliki, kolom nama pengguna ( DB_USERNAME) dan kata sandi ( DB_PASSWORD) sesuai dengan konfigurasi Anda.
### 4 Jalankan perintah php artisan key:generate di cmd atau terminal Anda "user@windows:folder\casemixRSBW:>php artisan key:generate"
### 5 Jalankan php artisan storage:link di cmd atau terminal Anda "user@windows:folder\casemixRSBW:>php artisan storage:link"
### jalankan perintah php artisan serve untuk uji coba user@windows:folder\casemixRSBW:>php artisan serve"
###    Kunjungi http://localhost:8000/

### Kujungin dokumentasi laravel
- [Simple, fast routing engine](https://laravel.com).

