@echo off
title Sistema de Inventario y Ventas - Servidor Local
echo ========================================================
echo   Iniciando Sistema de Inventario y Ventas
echo ========================================================
echo.

:: Verificar si MySQL de XAMPP est? corriendo, si no, iniciarlo
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL ya esta en ejecucion.
) else (
    echo [i] Iniciando MySQL desde XAMPP...
    start /B "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
    timeout /t 3 /nobreak >nul
)

echo.
echo ========================================================
echo   Servidor web listo en: http://localhost:8000/3DS/sistema_inventario_ventas/
echo   Credenciales de acceso por defecto:
echo     - Usuario: admin
echo     - Password: admin123
echo ========================================================
echo.
echo Presiona Ctrl+C para detener el servidor.
echo.

:: Abrir el navegador autom?ticamente
start http://localhost:8000/3DS/sistema_inventario_ventas/

:: Iniciar el servidor embebido de PHP
"C:\xampp\php\php.exe" -S localhost:8000 -t "%~dp0"
pause
