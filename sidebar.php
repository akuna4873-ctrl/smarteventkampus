<?php
// $activeMenu dikirim dari halaman pemanggil untuk menandai menu aktif
$activeMenu = $activeMenu ?? '';
?>
<aside class="sidebar" id="sidebar">
    <a href="<?= base_url('dashboard/index.php') ?>" class="brand">
        <span class="logo-dot">🎓</span>
        Smart Event
    </a>

    <ul class="side-menu">
        <li><a href="<?= base_url('dashboard/index.php') ?>" class="<?= $activeMenu === 'dashboard' ? 'active' : '' ?>">
            <span class="icon">📊</span> Dashboard
        </a></li>
        <li><a href="<?= base_url('dashboard/event_list.php') ?>" class="<?= $activeMenu === 'event' ? 'active' : '' ?>">
            <span class="icon">📅</span> Kelola Event
        </a></li>
        <li><a href="<?= base_url('dashboard/event_add.php') ?>" class="<?= $activeMenu === 'tambah' ? 'active' : '' ?>">
            <span class="icon">➕</span> Tambah Event
        </a></li>
    </ul>

    <div class="sidebar-footer">
        <ul class="side-menu">
            <li><a href="<?= base_url('index.php') ?>" target="_blank"><span class="icon">🌐</span> Lihat Website</a></li>
            <li><a href="<?= base_url('auth/logout.php') ?>"><span class="icon">🚪</span> Logout</a></li>
        </ul>
    </div>
</aside>
