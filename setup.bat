@echo off
echo ========================================================
echo  SETUP & INSTALLASI LAUNDRY EXPRESS (LARAVEL)
echo ========================================================
echo.

if not exist ".env" (
    echo [1/4] Membuat file konfigurasi .env dari .env.example...
    copy .env.example .env
) else (
    echo [1/4] File .env sudah ada.
)

if not exist "vendor" (
    echo [2/4] Menginstall dependensi composer...
    call composer install
) else (
    echo [2/4] Folder vendor sudah ada.
)

echo [3/4] Men-generate APP_KEY Laravel...
call php artisan key:generate

echo [4/4] Menjalankan migrasi basis data dan seeder...
echo Pastikan MySQL pada XAMPP sudah dinyalakan!
call php artisan migrate --seed

echo.
echo ========================================================
echo  INSTALASI SELESAI!
echo ========================================================
echo  Akses Web Pelanggan : http://127.0.0.1:8000
echo  Akses Panel Admin   : http://127.0.0.1:8000/login
echo  Akun Login Admin    : admin / admin123
echo.
echo  Untuk menjalankan server, ketik:
echo  php artisan serve
echo ========================================================
pause
