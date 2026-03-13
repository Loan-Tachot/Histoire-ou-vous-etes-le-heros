@echo off
echo Starting PHP built-in server...
cd /d "%~dp0"
php -S localhost:8000
pause
