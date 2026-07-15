<?php
/**
 * KONFIGURASI UMUM APLIKASI
 * Smart Event Campus
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Zona waktu
date_default_timezone_set('Asia/Jakarta');

// Nama aplikasi
define('APP_NAME', 'Smart Event Campus');

/**
 * BASE_URL otomatis mendeteksi folder project.
 * Jadi tidak perlu diubah manual walau di-hosting di folder berbeda,
 * KECUALI kamu ingin memaksanya manual (tinggal isi string di bawah).
 */
$autoBase = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'])));
if ($autoBase === '/' || $autoBase === '.') {
    $autoBase = '';
}
define('BASE_URL', $autoBase);

// Fungsi bantu untuk membuat link asset/halaman dengan base url yang benar
function base_url($path = '')
{
    return BASE_URL . '/' . ltrim($path, '/');
}

// Fungsi bantu untuk membersihkan input (mencegah XSS dasar)
function clean($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
