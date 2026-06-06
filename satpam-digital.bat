@echo off
REM ====================================================
REM  SATPAM DIGITAL - Dapoer Jiemas
REM  Dijalankan otomatis setiap menit oleh Windows Task Scheduler
REM  Perintah ini akan menjalankan semua scheduled tasks Laravel
REM ====================================================

C:\xampp2\php\php.exe c:\Users\RIDWAN\dapoer-jiemas\artisan schedule:run >> c:\Users\RIDWAN\dapoer-jiemas\storage\logs\satpam-digital.log 2>&1
