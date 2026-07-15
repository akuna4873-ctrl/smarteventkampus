<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check_mahasiswa.php';
require_once __DIR__ . '/../config/database.php';

$conn = getConnection();
$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$event) {
    header('Location: ' . base_url('mahasiswa/index.php'));
    exit;
}

$iconKategori = [
    'Seminar' => '🎤',
    'Workshop' => '🛠️',
    'Lomba' => '🏆',
    'Pelatihan' => '📘',
];
$statusClass = 'status-' . strtolower(str_replace(' ', '-', $event['status']));

$pageTitle = $event['judul'];
require_once __DIR__ . '/../includes/header_mahasiswa.php';
?>

<section class="section" style="padding-top:30px;">
    <div style="max-width:800px; margin:0 auto;">

        <a href="<?= base_url('mahasiswa/index.php') ?>" style="display:inline-flex; align-items:center; gap:6px; color:var(--gray); font-size:13.5px; font-weight:600; margin-bottom:18px;">← Kembali ke Daftar Event</a>

        <div class="card-panel" style="overflow:hidden;">
            <div class="event-thumb" style="height:260px; font-size:70px;">
                <?php if (!empty($event['gambar']) && file_exists(__DIR__ . '/../assets/uploads/' . $event['gambar'])): ?>
                    <img src="<?= base_url('assets/uploads/' . $event['gambar']) ?>" alt="<?= clean($event['judul']) ?>">
                <?php else: ?>
                    <?= $iconKategori[$event['kategori']] ?? '📅' ?>
                <?php endif; ?>
                <span class="event-cat-tag"><?= clean($event['kategori']) ?></span>
            </div>

            <div style="padding:28px 30px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:14px; flex-wrap:wrap; margin-bottom:16px;">
                    <h1 style="font-size:23px; color:var(--dark); line-height:1.4;"><?= clean($event['judul']) ?></h1>
                    <span class="status-badge <?= $statusClass ?>" style="white-space:nowrap;"><?= clean($event['status']) ?></span>
                </div>

                <div class="summary-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); margin-bottom:26px;">
                    <div class="summary-card" style="padding:16px;">
                        <div class="summary-icon" style="width:40px;height:40px;font-size:17px;">📅</div>
                        <div>
                            <div class="num" style="font-size:14.5px;"><?= date('d M Y', strtotime($event['tanggal_event'])) ?></div>
                            <div class="label">Tanggal</div>
                        </div>
                    </div>
                    <div class="summary-card" style="padding:16px;">
                        <div class="summary-icon" style="width:40px;height:40px;font-size:17px;">⏰</div>
                        <div>
                            <div class="num" style="font-size:14.5px;"><?= date('H:i', strtotime($event['waktu_event'])) ?> WIB</div>
                            <div class="label">Waktu</div>
                        </div>
                    </div>
                    <div class="summary-card" style="padding:16px;">
                        <div class="summary-icon" style="width:40px;height:40px;font-size:17px;">📍</div>
                        <div>
                            <div class="num" style="font-size:14.5px;"><?= clean($event['lokasi']) ?></div>
                            <div class="label">Lokasi</div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($event['penyelenggara'])): ?>
                    <p style="font-size:13.5px; color:var(--gray); margin-bottom:18px;">
                        <strong style="color:var(--dark);">Penyelenggara:</strong> <?= clean($event['penyelenggara']) ?>
                    </p>
                <?php endif; ?>

                <h3 style="font-size:15px; color:var(--dark); margin-bottom:10px;">Deskripsi Kegiatan</h3>
                <p style="font-size:14px; color:#374151; line-height:1.8; white-space:pre-line;"><?= clean($event['deskripsi']) ?></p>
            </div>
        </div>
    </div>
</section>

<script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>
