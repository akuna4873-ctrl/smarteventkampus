<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth_check_mahasiswa.php';

$pageTitle = $pageTitle ?? 'Portal Mahasiswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= clean($pageTitle) ?> - Smart Event Campus</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="<?= base_url('mahasiswa/index.php') ?>" class="brand">
            <span class="logo-dot">🎓</span>
            Smart Event Campus
        </a>
        <div class="nav-links">
            <a href="<?= base_url('mahasiswa/index.php') ?>">Beranda</a>
            <div class="user-chip">
                <div class="avatar-circle"><?= strtoupper(substr($_SESSION['mhs_nama'], 0, 1)) ?></div>
                <span><?= clean($_SESSION['mhs_nama']) ?></span>
            </div>
            <a href="<?= base_url('auth/logout_mahasiswa.php') ?>" class="btn btn-outline btn-sm">Logout</a>
        </div>
    </div>
</nav>
