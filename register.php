<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Kalau sudah login sebagai mahasiswa, langsung arahkan ke portal mahasiswa
if (isset($_SESSION['mhs_id'])) {
    header('Location: ' . base_url('mahasiswa/index.php'));
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim       = trim($_POST['nim'] ?? '');
    $nama      = trim($_POST['nama_lengkap'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $jurusan   = trim($_POST['jurusan'] ?? '');
    $password  = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';

    if ($nim === '' || $nama === '' || $email === '' || $password === '') {
        $error = 'Kolom bertanda * wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $conn = getConnection();

        $cek = $conn->prepare("SELECT id FROM mahasiswa WHERE nim = ? OR email = ?");
        $cek->bind_param('ss', $nim, $email);
        $cek->execute();
        $hasil = $cek->get_result();

        if ($hasil->num_rows > 0) {
            $error = 'NIM atau email sudah terdaftar. Silakan login.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO mahasiswa (nim, nama_lengkap, email, jurusan, password) VALUES (?,?,?,?,?)");
            $stmt->bind_param('sssss', $nim, $nama, $email, $jurusan, $hash);

            if ($stmt->execute()) {
                $success = 'Registrasi berhasil! Silakan login menggunakan NIM dan password kamu.';
            } else {
                $error = 'Gagal mendaftar: ' . $conn->error;
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Akun Mahasiswa - Smart Event Campus</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card" style="max-width:460px;">
        <div class="logo-dot" style="width:52px;height:52px;font-size:24px;">🎓</div>
        <h1>Daftar Akun Mahasiswa</h1>
        <p class="sub">Buat akun untuk melihat & mengikuti event kampus</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= clean($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= clean($success) ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-row-2">
                <div class="form-group">
                    <label>NIM *</label>
                    <input type="text" name="nim" required placeholder="contoh: 2110511001" value="<?= isset($_POST['nim']) ? clean($_POST['nim']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Jurusan</label>
                    <input type="text" name="jurusan" placeholder="contoh: Teknik Informatika" value="<?= isset($_POST['jurusan']) ? clean($_POST['jurusan']) : '' ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" required placeholder="Nama lengkap kamu" value="<?= isset($_POST['nama_lengkap']) ? clean($_POST['nama_lengkap']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required placeholder="nama@email.com" value="<?= isset($_POST['email']) ? clean($_POST['email']) : '' ?>">
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password *</label>
                    <input type="password" name="konfirmasi_password" required placeholder="Ulangi password">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Daftar Sekarang</button>
        </form>
        <?php else: ?>
            <a href="<?= base_url('auth/login_mahasiswa.php') ?>" class="btn btn-primary btn-block">Lanjut ke Halaman Login →</a>
        <?php endif; ?>

        <p style="text-align:center; margin-top:20px; font-size:13px;">
            Sudah punya akun? <a href="<?= base_url('auth/login_mahasiswa.php') ?>" style="color:var(--primary); font-weight:600;">Login di sini</a>
        </p>
        <p style="text-align:center; margin-top:8px; font-size:13px;">
            <a href="<?= base_url('index.php') ?>" style="color:var(--gray);">← Kembali ke Beranda</a>
        </p>
    </div>
</div>

</body>
</html>
