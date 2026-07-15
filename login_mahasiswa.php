<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['mhs_id'])) {
    header('Location: ' . base_url('mahasiswa/index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim      = trim($_POST['nim'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nim === '' || $password === '') {
        $error = 'NIM dan password wajib diisi.';
    } else {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT id, nim, nama_lengkap, password FROM mahasiswa WHERE nim = ?");
        $stmt->bind_param('s', $nim);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $mhs = $result->fetch_assoc();
            if (password_verify($password, $mhs['password'])) {
                $_SESSION['mhs_id']   = $mhs['id'];
                $_SESSION['mhs_nama'] = $mhs['nama_lengkap'];
                $_SESSION['mhs_nim']  = $mhs['nim'];

                header('Location: ' . base_url('mahasiswa/index.php'));
                exit;
            } else {
                $error = 'Password salah.';
            }
        } else {
            $error = 'NIM tidak ditemukan. Silakan daftar dulu.';
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Mahasiswa - Smart Event Campus</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="logo-dot" style="width:52px;height:52px;font-size:24px;">🎓</div>
        <h1>Login Mahasiswa</h1>
        <p class="sub">Masuk untuk melihat & mengikuti event kampus</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= clean($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>NIM</label>
                <input type="text" name="nim" required autofocus placeholder="Masukkan NIM">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Masukkan password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <p style="text-align:center; margin-top:20px; font-size:13px;">
            Belum punya akun? <a href="<?= base_url('auth/register.php') ?>" style="color:var(--primary); font-weight:600;">Daftar di sini</a>
        </p>
        <p style="text-align:center; margin-top:8px; font-size:13px;">
            <a href="<?= base_url('index.php') ?>" style="color:var(--gray);">← Kembali ke Beranda</a>
        </p>
    </div>
</div>

</body>
</html>
