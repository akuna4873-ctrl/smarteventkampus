<?php
// Header ini dipakai di semua halaman publik (index.php, detail event, dll)
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? clean($pageTitle) . ' - ' . APP_NAME : APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="<?= base_url('index.php') ?>" class="brand">
            <span class="logo-dot">🎓</span>
            Smart Event Campus
        </a>
        <div class="nav-links">
            <a href="<?= base_url('index.php') ?>">Beranda</a>
            <a href="<?= base_url('index.php#events') ?>">Event</a>
            <?php if (isset($_SESSION['mhs_id'])): ?>
                <a href="<?= base_url('mahasiswa/index.php') ?>" class="btn btn-outline btn-sm">Portal Mahasiswa</a>
            <?php else: ?>
                <a href="<?= base_url('auth/login_mahasiswa.php') ?>">Login Mahasiswa</a>
                <a href="<?= base_url('auth/register.php') ?>" class="btn btn-outline btn-sm">Daftar Akun</a>
            <?php endif; ?>
            <a href="<?= base_url('auth/login.php') ?>" class="btn btn-primary btn-sm">Login Admin</a>
        </div>
    </div>
</nav>
