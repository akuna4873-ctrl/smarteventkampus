<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$conn = getConnection();
$id = $_SESSION['admin_id'];

$stmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email        = trim($_POST['email']);
    $no_hp        = trim($_POST['no_hp']);
    $fotoBaru     = $admin['foto'];

    $passwordBaru      = $_POST['password_baru'] ?? '';
    $passwordKonfirmasi = $_POST['password_konfirmasi'] ?? '';
    $passwordLama       = $_POST['password_lama'] ?? '';

    if ($nama_lengkap === '') {
        $error = 'Nama lengkap wajib diisi.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        // Upload foto baru kalau ada
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ekstensi = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ekstensi, $allowed)) {
                $namaFotoBaru = 'admin_' . $id . '_' . time() . '.' . $ekstensi;
                $tujuan = __DIR__ . '/../assets/uploads/' . $namaFotoBaru;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan)) {
                    if ($fotoBaru && file_exists(__DIR__ . '/../assets/uploads/' . $fotoBaru)) {
                        unlink(__DIR__ . '/../assets/uploads/' . $fotoBaru);
                    }
                    $fotoBaru = $namaFotoBaru;
                }
            } else {
                $error = 'Format foto harus jpg, jpeg, png, atau webp.';
            }
        }

        // Kalau mau ganti password, wajib isi password lama dengan benar
        $passwordUntukSimpan = $admin['password'];
        if ($error === '' && $passwordBaru !== '') {
            if (!password_verify($passwordLama, $admin['password'])) {
                $error = 'Password lama salah, gagal mengubah password.';
            } elseif (strlen($passwordBaru) < 6) {
                $error = 'Password baru minimal 6 karakter.';
            } elseif ($passwordBaru !== $passwordKonfirmasi) {
                $error = 'Konfirmasi password baru tidak sama.';
            } else {
                $passwordUntukSimpan = password_hash($passwordBaru, PASSWORD_BCRYPT);
            }
        }

        if ($error === '') {
            $stmt = $conn->prepare("UPDATE admin SET nama_lengkap=?, email=?, no_hp=?, foto=?, password=? WHERE id=?");
            $stmt->bind_param('sssssi', $nama_lengkap, $email, $no_hp, $fotoBaru, $passwordUntukSimpan, $id);

            if ($stmt->execute()) {
                $_SESSION['admin_nama'] = $nama_lengkap;
                $_SESSION['admin_foto'] = $fotoBaru;
                $success = 'Profil berhasil diperbarui!';
                $admin['nama_lengkap'] = $nama_lengkap;
                $admin['email'] = $email;
                $admin['no_hp'] = $no_hp;
                $admin['foto'] = $fotoBaru;
            } else {
                $error = 'Gagal menyimpan perubahan: ' . $conn->error;
            }
            $stmt->close();
        }
    }
}

$pageTitle = 'Profil Saya';
$activeMenu = 'profil';
require_once __DIR__ . '/../includes/header_dashboard.php';
?>

<div class="card-panel form-card">
    <?php if ($error): ?>
        <div class="alert alert-error"><?= clean($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= clean($success) ?></div>
    <?php endif; ?>

    <div style="display:flex; align-items:center; gap:18px; margin-bottom:26px;">
        <?php if (!empty($admin['foto']) && file_exists(__DIR__ . '/../assets/uploads/' . $admin['foto'])): ?>
            <img src="<?= base_url('assets/uploads/' . $admin['foto']) ?>" style="width:70px;height:70px;border-radius:50%;object-fit:cover;">
        <?php else: ?>
            <div class="avatar-circle" style="width:70px;height:70px;font-size:26px;"><?= strtoupper(substr($admin['nama_lengkap'], 0, 1)) ?></div>
        <?php endif; ?>
        <div>
            <div style="font-weight:700; font-size:17px; color:var(--dark);"><?= clean($admin['nama_lengkap']) ?></div>
            <div style="font-size:13px; color:var(--gray);">@<?= clean($admin['username']) ?></div>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nama Lengkap *</label>
            <input type="text" name="nama_lengkap" required value="<?= clean($admin['nama_lengkap']) ?>">
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="email@kampus.ac.id" value="<?= clean($admin['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>No. HP</label>
                <input type="text" name="no_hp" placeholder="08xxxxxxxxxx" value="<?= clean($admin['no_hp'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Foto Profil</label>
            <input type="file" name="foto" accept="image/*">
        </div>

        <hr style="border:none; border-top:1px dashed var(--border); margin:26px 0;">

        <p style="font-size:13.5px; font-weight:600; color:var(--dark); margin-bottom:14px;">🔒 Ganti Password (opsional, kosongkan kalau tidak ingin ganti)</p>

        <div class="form-group">
            <label>Password Lama</label>
            <input type="password" name="password_lama" placeholder="Isi kalau ingin ganti password">
        </div>
        <div class="form-row-2">
            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="password_baru" placeholder="Minimal 6 karakter">
            </div>
            <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="password_konfirmasi" placeholder="Ulangi password baru">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
    </form>
</div>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer_dashboard.php';
?>
