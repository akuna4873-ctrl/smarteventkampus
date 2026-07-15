<?php
/**
 * SETUP ADMIN PERTAMA
 * ------------------------------------------
 * File ini dipakai SEKALI SAJA untuk membuat akun admin pertama
 * dengan password yang otomatis di-hash secara aman.
 *
 * CARA PAKAI:
 * 1. Upload semua file project ke hosting & import database.sql
 * 2. Buka file ini lewat browser, contoh:
 *    https://namadomainkamu.com/setup_admin.php
 * 3. Isi form username, nama lengkap, dan password
 * 4. Klik "Buat Akun Admin"
 * 5. SETELAH BERHASIL, HAPUS FILE INI DARI HOSTING (wajib, demi keamanan)
 */

require_once 'config/database.php';

$pesan = '';
$sukses = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nama     = trim($_POST['nama_lengkap']);
    $password = $_POST['password'];

    if ($username === '' || $nama === '' || $password === '') {
        $pesan = 'Semua kolom wajib diisi.';
    } else {
        $conn = getConnection();

        $cek = $conn->prepare("SELECT id FROM admin WHERE username = ?");
        $cek->bind_param('s', $username);
        $cek->execute();
        $hasil = $cek->get_result();

        if ($hasil->num_rows > 0) {
            $pesan = 'Username sudah dipakai, silakan pilih username lain.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO admin (username, password, nama_lengkap) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $username, $hash, $nama);

            if ($stmt->execute()) {
                $sukses = true;
                $pesan = 'Akun admin berhasil dibuat! Sekarang HAPUS file setup_admin.php dari hosting, lalu login di halaman auth/login.php';
            } else {
                $pesan = 'Terjadi kesalahan: ' . $conn->error;
            }
            $stmt->close();
        }
        $cek->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Setup Admin - Smart Event Campus</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    * { box-sizing: border-box; }
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 20px;
    }
    .box {
        background: #fff;
        padding: 36px;
        border-radius: 16px;
        max-width: 420px;
        width: 100%;
        box-shadow: 0 20px 50px rgba(0,0,0,0.25);
    }
    h1 { font-size: 20px; color: #1e1b4b; margin-bottom: 6px; }
    p.sub { color: #6b7280; font-size: 14px; margin-bottom: 22px; }
    label { font-size: 13px; font-weight: 600; color: #374151; display: block; margin-bottom: 6px; margin-top: 14px; }
    input {
        width: 100%; padding: 11px 14px; border-radius: 8px;
        border: 1px solid #d1d5db; font-size: 14px;
    }
    input:focus { outline: none; border-color: #7c3aed; }
    button {
        width: 100%; margin-top: 22px; padding: 12px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: #fff; border: none; border-radius: 8px;
        font-size: 15px; font-weight: 600; cursor: pointer;
    }
    button:hover { opacity: 0.92; }
    .pesan {
        margin-top: 16px; padding: 12px 14px; border-radius: 8px;
        font-size: 13.5px;
    }
    .sukses { background: #d1fae5; color: #065f46; }
    .gagal { background: #fee2e2; color: #991b1b; }
    .warn { margin-top: 18px; font-size: 12px; color: #b45309; background: #fffbeb; padding: 10px 12px; border-radius: 8px; }
</style>
</head>
<body>
<div class="box">
    <h1>🚀 Setup Admin Pertama</h1>
    <p class="sub">Smart Event Campus — buat akun admin untuk login ke dashboard.</p>

    <?php if ($pesan): ?>
        <div class="pesan <?= $sukses ? 'sukses' : 'gagal' ?>"><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>

    <?php if (!$sukses): ?>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required placeholder="contoh: admin">

        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" required placeholder="contoh: Administrator Kampus">

        <label>Password</label>
        <input type="password" name="password" required placeholder="minimal 6 karakter">

        <button type="submit">Buat Akun Admin</button>
    </form>
    <?php else: ?>
        <a href="auth/login.php" style="display:block;text-align:center;margin-top:10px;color:#4f46e5;font-weight:600;text-decoration:none;">Lanjut ke Halaman Login →</a>
    <?php endif; ?>

    <div class="warn">⚠️ Setelah akun admin berhasil dibuat, segera hapus file <b>setup_admin.php</b> ini dari hosting agar tidak disalahgunakan orang lain.</div>
</div>
</body>
</html>
