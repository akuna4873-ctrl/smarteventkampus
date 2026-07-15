<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$conn = getConnection();
$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    $_SESSION['notif'] = ['type' => 'error', 'pesan' => 'Event tidak ditemukan.'];
    header('Location: ' . base_url('dashboard/event_list.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul         = trim($_POST['judul']);
    $kategori      = $_POST['kategori'];
    $deskripsi     = trim($_POST['deskripsi']);
    $tanggal_event = $_POST['tanggal_event'];
    $waktu_event   = $_POST['waktu_event'];
    $lokasi        = trim($_POST['lokasi']);
    $penyelenggara = trim($_POST['penyelenggara']);
    $status        = $_POST['status'];
    $namaGambar    = $event['gambar'];

    if ($judul === '' || $deskripsi === '' || $tanggal_event === '' || $lokasi === '') {
        $error = 'Semua kolom bertanda * wajib diisi.';
    } else {
        // Jika ada gambar baru diupload, ganti gambar lama
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $ekstensi = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ekstensi, $allowed)) {
                $namaGambarBaru = 'event_' . time() . '_' . rand(100, 999) . '.' . $ekstensi;
                $tujuan = __DIR__ . '/../assets/uploads/' . $namaGambarBaru;
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $tujuan)) {
                    // Hapus gambar lama kalau ada
                    if ($namaGambar && file_exists(__DIR__ . '/../assets/uploads/' . $namaGambar)) {
                        unlink(__DIR__ . '/../assets/uploads/' . $namaGambar);
                    }
                    $namaGambar = $namaGambarBaru;
                }
            } else {
                $error = 'Format gambar harus jpg, jpeg, png, atau webp.';
            }
        }

        if ($error === '') {
            $stmt = $conn->prepare("UPDATE events SET judul=?, kategori=?, deskripsi=?, tanggal_event=?, waktu_event=?, lokasi=?, penyelenggara=?, gambar=?, status=? WHERE id=?");
            $stmt->bind_param('sssssssssi', $judul, $kategori, $deskripsi, $tanggal_event, $waktu_event, $lokasi, $penyelenggara, $namaGambar, $status, $id);

            if ($stmt->execute()) {
                $_SESSION['notif'] = ['type' => 'success', 'pesan' => 'Event berhasil diperbarui!'];
                header('Location: ' . base_url('dashboard/event_list.php'));
                exit;
            } else {
                $error = 'Gagal memperbarui data: ' . $conn->error;
            }
            $stmt->close();
        }
    }
}

$pageTitle = 'Edit Event';
$activeMenu = 'event';
require_once __DIR__ . '/../includes/header_dashboard.php';
?>

<div class="card-panel form-card">
    <?php if ($error): ?>
        <div class="alert alert-error"><?= clean($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Judul Event *</label>
            <input type="text" name="judul" required value="<?= clean($_POST['judul'] ?? $event['judul']) ?>">
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Kategori *</label>
                <select name="kategori" required>
                    <?php foreach (['Seminar','Workshop','Lomba','Pelatihan'] as $k): ?>
                        <option value="<?= $k ?>" <?= $event['kategori'] === $k ? 'selected' : '' ?>><?= $k ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Status *</label>
                <select name="status" required>
                    <?php foreach (['Akan Datang','Berlangsung','Selesai'] as $s): ?>
                        <option value="<?= $s ?>" <?= $event['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Deskripsi *</label>
            <textarea name="deskripsi" rows="4" required><?= clean($_POST['deskripsi'] ?? $event['deskripsi']) ?></textarea>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Tanggal Event *</label>
                <input type="date" name="tanggal_event" required value="<?= $event['tanggal_event'] ?>">
            </div>
            <div class="form-group">
                <label>Waktu Event *</label>
                <input type="time" name="waktu_event" required value="<?= substr($event['waktu_event'], 0, 5) ?>">
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Lokasi *</label>
                <input type="text" name="lokasi" required value="<?= clean($event['lokasi']) ?>">
            </div>
            <div class="form-group">
                <label>Penyelenggara</label>
                <input type="text" name="penyelenggara" value="<?= clean($event['penyelenggara'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Gambar Event</label>
            <?php if (!empty($event['gambar']) && file_exists(__DIR__ . '/../assets/uploads/' . $event['gambar'])): ?>
                <img src="<?= base_url('assets/uploads/' . $event['gambar']) ?>" style="max-height:140px; border-radius:8px; margin-bottom:10px;">
                <p style="font-size:12.5px; color:var(--gray); margin-bottom:10px;">Gambar saat ini. Upload baru untuk menggantinya.</p>
            <?php endif; ?>
            <input type="file" name="gambar" id="gambarInput" accept="image/*">
            <img id="imgPreview" style="display:none; margin-top:10px; max-height:160px; border-radius:8px;">
        </div>

        <div style="display:flex; gap:10px; margin-top:10px;">
            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
            <a href="<?= base_url('dashboard/event_list.php') ?>" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer_dashboard.php';
?>
