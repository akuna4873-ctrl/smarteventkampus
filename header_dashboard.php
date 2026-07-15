<?php
/**
 * Header dashboard: membuka <html>, memuat CSS, sidebar, dan topbar.
 * Variabel yang bisa dikirim dari halaman pemanggil:
 * - $pageTitle   : judul halaman (tampil di tab & topbar)
 * - $activeMenu  : menu sidebar yang sedang aktif
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth_check.php';

$pageTitle = $pageTitle ?? 'Dashboard';
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

<div class="dash-wrapper">
    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <div style="display:flex; align-items:center; gap:14px;">
                <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
                <h1><?= clean($pageTitle) ?></h1>
            </div>
            <div class="user-chip">
                <div class="avatar-circle"><?= strtoupper(substr($_SESSION['admin_nama'] ?? 'A', 0, 1)) ?></div>
                <span><?= clean($_SESSION['admin_nama'] ?? 'Admin') ?></span>
            </div>
        </div>
        <div class="dash-body">
