<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$conn = getConnection();

// Notifikasi hasil aksi sebelumnya (tambah/edit/hapus)
$notif = $_SESSION['notif'] ?? null;
unset($_SESSION['notif']);

$events = $conn->query("SELECT * FROM events ORDER BY tanggal_event DESC")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Kelola Event';
$activeMenu = 'event';
require_once __DIR__ . '/../includes/header_dashboard.php';
?>

<?php if ($notif): ?>
    <div class="alert alert-<?= $notif['type'] ?>"><?= clean($notif['pesan']) ?></div>
<?php endif; ?>

<div class="card-panel">
    <div class="table-toolbar">
        <h2>Semua Event (<?= count($events) ?>)</h2>
        <div style="display:flex; gap:10px;">
            <div class="search-box">
                <input type="text" id="searchTable" placeholder="🔍 Cari event...">
            </div>
            <a href="<?= base_url('dashboard/event_add.php') ?>" class="btn btn-primary btn-sm">➕ Tambah Event</a>
        </div>
    </div>

    <?php if (empty($events)): ?>
        <div class="empty-state">
            <div class="icon">🗓️</div>
            <p>Belum ada data event.</p>
        </div>
    <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $e): ?>
            <tr>
                <td>
                    <?php if (!empty($e['gambar']) && file_exists(__DIR__ . '/../assets/uploads/' . $e['gambar'])): ?>
                        <img class="thumb-mini" src="<?= base_url('assets/uploads/' . $e['gambar']) ?>" alt="">
                    <?php else: ?>
                        <div class="thumb-mini"></div>
                    <?php endif; ?>
                </td>
                <td><?= clean($e['judul']) ?></td>
                <td><?= clean($e['kategori']) ?></td>
                <td><?= date('d M Y', strtotime($e['tanggal_event'])) ?></td>
                <td><?= clean($e['lokasi']) ?></td>
                <td><span class="status-badge status-<?= strtolower(str_replace(' ','-',$e['status'])) ?>"><?= clean($e['status']) ?></span></td>
                <td>
                    <div class="action-btns">
                        <a href="<?= base_url('dashboard/event_edit.php?id=' . $e['id']) ?>" class="btn btn-outline btn-sm">✏️ Edit</a>
                        <a href="<?= base_url('dashboard/event_delete.php?id=' . $e['id']) ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirmDelete('<?= clean($e['judul']) ?>')">🗑️ Hapus</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer_dashboard.php';
?>
