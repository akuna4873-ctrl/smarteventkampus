<?php
// Wajibkan login untuk mengakses halaman dashboard
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . base_url('auth/login.php'));
    exit;
}
