@echo off
title Menjalankan Server Hotel - Aurelia Hotels
echo ========================================================
echo   AURELIA HOTELS & RESORTS - SISTEM RESERVASI HOTEL
echo ========================================================
echo.
echo Memulai MySQL MariaDB (XAMPP)...
start "" /B "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone

echo Memulai Apache Web Server (XAMPP)...
start "" /B "C:\xampp\apache\bin\httpd.exe"

echo.
echo Server telah berhasil dijalankan!
echo Silakan buka browser Anda di:
echo http://localhost/reservasi-hotel/
echo.
echo Akun Admin Hotel:
echo Username: admin
echo Password: admin123
echo URL Admin: http://localhost/reservasi-hotel/admin/
echo.
pause
