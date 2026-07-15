<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

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

    if ($judul === '' || $deskripsi === '' || $tanggal_event === '' || $lokasi === '') {
        $error = 'Semua kolom bertanda * wajib diisi.';
    } else {
        $namaGambar = null;

        // Proses upload gambar jika ada
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $ekstensi = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ekstensi, $allowed)) {
                $namaGambar = 'event_' . time() . '_' . rand(100, 999) . '.' . $ekstensi;
                $tujuan = __DIR__ . '/../assets/uploads/' . $namaGambar;
                move_uploaded_file($_FILES['gambar']['tmp_name'], $tujuan);
            } else {
                $error = 'Format gambar harus jpg, jpeg, png, atau webp.';
            }
        }

        if ($error === '') {
            $conn = getConnection();
            $stmt = $conn->prepare("INSERT INTO events (judul, kategori, deskripsi, tanggal_event, waktu_event, lokasi, penyelenggara, gambar, status, created_by) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('sssssssssi', $judul, $kategori, $deskripsi, $tanggal_event, $waktu_event, $lokasi, $penyelenggara, $namaGambar, $status, $_SESSION['admin_id']);

            if ($stmt->execute()) {
                $_SESSION['notif'] = ['type' => 'success', 'pesan' => 'Event berhasil ditambahkan!'];
                header('Location: ' . base_url('dashboard/event_list.php'));
                exit;
            } else {
                $error = 'Gagal menyimpan data: ' . $conn->error;
            }
            $stmt->close();
            $conn->close();
        }
    }
}

$pageTitle = 'Tambah Event';
$activeMenu = 'tambah';
require_once __DIR__ . '/../includes/header_dashboard.php';
?>

<div class="card-panel form-card">
    <?php if ($error): ?>
        <div class="alert alert-error"><?= clean($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Judul Event *</label>
            <input type="text" name="judul" required placeholder="contoh: Seminar Nasional Teknologi AI" value="<?= isset($_POST['judul']) ? clean($_POST['judul']) : '' ?>">
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Kategori *</label>
                <select name="kategori" required>
                    <option value="Seminar">Seminar</option>
                    <option value="Workshop">Workshop</option>
                    <option value="Lomba">Lomba</option>
                    <option value="Pelatihan">Pelatihan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status *</label>
                <select name="status" required>
                    <option value="Akan Datang">Akan Datang</option>
                    <option value="Berlangsung">Berlangsung</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Deskripsi *</label>
            <textarea name="deskripsi" rows="4" required placeholder="Jelaskan detail kegiatan..."><?= isset($_POST['deskripsi']) ? clean($_POST['deskripsi']) : '' ?></textarea>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Tanggal Event *</label>
                <input type="date" name="tanggal_event" required>
            </div>
            <div class="form-group">
                <label>Waktu Event *</label>
                <input type="time" name="waktu_event" required>
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Lokasi *</label>
                <input type="text" name="lokasi" required placeholder="contoh: Aula Gedung Rektorat">
            </div>
            <div class="form-group">
                <label>Penyelenggara</label>
                <input type="text" name="penyelenggara" placeholder="contoh: BEM Fakultas Ilmu Komputer">
            </div>
        </div>

        <div class="form-group">
            <label>Gambar Event (opsional)</label>
            <input type="file" name="gambar" id="gambarInput" accept="image/*">
            <img id="imgPreview" style="display:none; margin-top:10px; max-height:160px; border-radius:8px;">
        </div>

        <div style="display:flex; gap:10px; margin-top:10px;">
            <button type="submit" class="btn btn-primary">💾 Simpan Event</button>
            <a href="<?= base_url('dashboard/event_list.php') ?>" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer_dashboard.php'; ?>
