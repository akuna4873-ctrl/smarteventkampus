<?php
require_once __DIR__ . '/../config/config.php';

unset($_SESSION['mhs_id'], $_SESSION['mhs_nama'], $_SESSION['mhs_nim']);

header('Location: ' . base_url('auth/login_mahasiswa.php'));
exit;
