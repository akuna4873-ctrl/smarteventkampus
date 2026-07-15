<?php
// Wajibkan login mahasiswa untuk mengakses halaman portal mahasiswa
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['mhs_id'])) {
    header('Location: ' . base_url('auth/login_mahasiswa.php'));
    exit;
}
