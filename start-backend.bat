@echo off
cd /d "%~dp0"
echo Starting PHP backend on http://127.0.0.1:8000
echo Press Ctrl+C to stop.
php -S 127.0.0.1:8000 -t backend backend/index.php
