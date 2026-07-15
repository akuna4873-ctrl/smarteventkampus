<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Kalau sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: ' . base_url('dashboard/index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT id, username, password, nama_lengkap FROM admin WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id']    = $admin['id'];
                $_SESSION['admin_nama']  = $admin['nama_lengkap'];
                $_SESSION['admin_user']  = $admin['username'];

                header('Location: ' . base_url('dashboard/index.php'));
                exit;
            } else {
                $error = 'Password salah.';
            }
        } else {
            $error = 'Username tidak ditemukan.';
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
<title>Login Admin - Smart Event Campus</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="logo-dot" style="width:52px;height:52px;font-size:24px;">🎓</div>
        <h1>Login Admin</h1>
        <p class="sub">Masuk untuk mengelola event kampus</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= clean($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus placeholder="Masukkan username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Masukkan password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <p style="text-align:center; margin-top:20px; font-size:13px;">
            <a href="<?= base_url('index.php') ?>" style="color:var(--gray);">← Kembali ke Beranda</a>
        </p>
    </div>
</div>

</body>
</html>
