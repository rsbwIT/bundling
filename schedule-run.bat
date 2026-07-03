@echo off
cd /d C:\xampp\htdocs\bundling2
C:\xampp\php\php.exe artisan schedule:run >> C:\xampp\htdocs\bundling2\storage\logs\schedule.log 2>&1
