<?php
$pageTitle = 'Profil Saya';
require_once __DIR__ . '/../includes/header_mahasiswa.php';
require_once __DIR__ . '/../config/database.php';

$conn = getConnection();
$id = $_SESSION['mhs_id'];

$stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$mhs = $stmt->get_result()->fetch_assoc();
$stmt->close();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email        = trim($_POST['email']);
    $jurusan      = trim($_POST['jurusan']);
    $no_hp        = trim($_POST['no_hp']);
    $fotoBaru     = $mhs['foto'];

    $passwordBaru       = $_POST['password_baru'] ?? '';
    $passwordKonfirmasi = $_POST['password_konfirmasi'] ?? '';
    $passwordLama       = $_POST['password_lama'] ?? '';

    if ($nama_lengkap === '' || $email === '') {
        $error = 'Nama lengkap dan email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        // Cek email tidak dipakai mahasiswa lain
        $cek = $conn->prepare("SELECT id FROM mahasiswa WHERE email = ? AND id != ?");
        $cek->bind_param('si', $email, $id);
        $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            $error = 'Email sudah dipakai akun lain.';
        }
        $cek->close();

        if ($error === '' && isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ekstensi = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ekstensi, $allowed)) {
                $namaFotoBaru = 'mhs_' . $id . '_' . time() . '.' . $ekstensi;
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

        $passwordUntukSimpan = $mhs['password'];
        if ($error === '' && $passwordBaru !== '') {
            if (!password_verify($passwordLama, $mhs['password'])) {
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
            $stmt = $conn->prepare("UPDATE mahasiswa SET nama_lengkap=?, email=?, jurusan=?, no_hp=?, foto=?, password=? WHERE id=?");
            $stmt->bind_param('ssssssi', $nama_lengkap, $email, $jurusan, $no_hp, $fotoBaru, $passwordUntukSimpan, $id);

            if ($stmt->execute()) {
                $_SESSION['mhs_nama'] = $nama_lengkap;
                $_SESSION['mhs_foto'] = $fotoBaru;
                $success = 'Profil berhasil diperbarui!';
                $mhs['nama_lengkap'] = $nama_lengkap;
                $mhs['email'] = $email;
                $mhs['jurusan'] = $jurusan;
                $mhs['no_hp'] = $no_hp;
                $mhs['foto'] = $fotoBaru;
            } else {
                $error = 'Gagal menyimpan perubahan: ' . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>

<section class="section" style="padding-top:34px;">
    <div class="card-panel form-card" style="margin:0 auto;">
        <?php if ($error): ?>
            <div class="alert alert-error"><?= clean($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= clean($success) ?></div>
        <?php endif; ?>

        <div style="display:flex; align-items:center; gap:18px; margin-bottom:26px;">
            <?php if (!empty($mhs['foto']) && file_exists(__DIR__ . '/../assets/uploads/' . $mhs['foto'])): ?>
                <img src="<?= base_url('assets/uploads/' . $mhs['foto']) ?>" style="width:70px;height:70px;border-radius:50%;object-fit:cover;">
            <?php else: ?>
                <div class="avatar-circle" style="width:70px;height:70px;font-size:26px;"><?= strtoupper(substr($mhs['nama_lengkap'], 0, 1)) ?></div>
            <?php endif; ?>
            <div>
                <div style="font-weight:700; font-size:17px; color:var(--dark);"><?= clean($mhs['nama_lengkap']) ?></div>
                <div style="font-size:13px; color:var(--gray);">NIM: <?= clean($mhs['nim']) ?></div>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" required value="<?= clean($mhs['nama_lengkap']) ?>">
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required value="<?= clean($mhs['email']) ?>">
                </div>
                <div class="form-group">
                    <label>Jurusan</label>
                    <input type="text" name="jurusan" value="<?= clean($mhs['jurusan'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>No. HP</label>
                <input type="text" name="no_hp" placeholder="08xxxxxxxxxx" value="<?= clean($mhs['no_hp'] ?? '') ?>">
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
</section>

<?php $conn->close(); ?>

<script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>
