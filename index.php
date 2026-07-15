<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Beranda';

$conn = getConnection();

// Ambil semua event, urutkan yang terdekat tanggalnya duluan
$sql = "SELECT * FROM events ORDER BY tanggal_event ASC";
$result = $conn->query($sql);
$events = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Statistik ringkas untuk hero section
$totalEvent = count($events);
$totalSeminar = count(array_filter($events, fn($e) => $e['kategori'] === 'Seminar'));
$totalWorkshop = count(array_filter($events, fn($e) => $e['kategori'] === 'Workshop'));
$totalLomba = count(array_filter($events, fn($e) => $e['kategori'] === 'Lomba'));

$iconKategori = [
    'Seminar' => '🎤',
    'Workshop' => '🛠️',
    'Lomba' => '🏆',
    'Pelatihan' => '📘',
];

require_once __DIR__ . '/includes/header_public.php';
?>

<section class="hero">
    <h1>Temukan & Ikuti Kegiatan Kampus Terbaru</h1>
    <p>Smart Event Campus menghadirkan informasi seminar, workshop, lomba, dan pelatihan mahasiswa dalam satu tempat.</p>
    <div class="hero-badges">
        <span class="hero-badge">📢 Update Real-time</span>
        <span class="hero-badge">🎯 Mudah Diakses</span>
        <span class="hero-badge">🎓 Khusus Civitas Kampus</span>
    </div>
</section>

<div class="stats-row">
    <div class="stat-item">
        <div class="num"><?= $totalEvent ?></div>
        <div class="label">Total Event</div>
    </div>
    <div class="stat-item">
        <div class="num"><?= $totalSeminar ?></div>
        <div class="label">Seminar</div>
    </div>
    <div class="stat-item">
        <div class="num"><?= $totalWorkshop ?></div>
        <div class="label">Workshop</div>
    </div>
    <div class="stat-item">
        <div class="num"><?= $totalLomba ?></div>
        <div class="label">Lomba</div>
    </div>
</div>

<section class="section" id="events">
    <div class="section-head">
        <h2>Daftar Kegiatan</h2>
        <p>Pilih kategori untuk menyaring kegiatan yang ingin kamu ikuti</p>
    </div>

    <div class="filter-bar">
        <div class="filter-chip active" data-kategori="semua">Semua</div>
        <div class="filter-chip" data-kategori="Seminar">🎤 Seminar</div>
        <div class="filter-chip" data-kategori="Workshop">🛠️ Workshop</div>
        <div class="filter-chip" data-kategori="Lomba">🏆 Lomba</div>
        <div class="filter-chip" data-kategori="Pelatihan">📘 Pelatihan</div>
    </div>

    <?php if (empty($events)): ?>
        <div class="empty-state">
            <div class="icon">🗓️</div>
            <p>Belum ada event yang ditambahkan. Silakan cek lagi nanti.</p>
        </div>
    <?php else: ?>
        <div class="event-grid">
            <?php foreach ($events as $event): ?>
                <?php
                    $statusClass = 'status-' . strtolower(str_replace(' ', '-', $event['status']));
                    $tanggal = date('d M Y', strtotime($event['tanggal_event']));
                ?>
                <div class="event-card" data-kategori="<?= clean($event['kategori']) ?>">
                    <div class="event-thumb">
                        <?php if (!empty($event['gambar']) && file_exists(__DIR__ . '/assets/uploads/' . $event['gambar'])): ?>
                            <img src="<?= base_url('assets/uploads/' . $event['gambar']) ?>" alt="<?= clean($event['judul']) ?>">
                        <?php else: ?>
                            <?= $iconKategori[$event['kategori']] ?? '📅' ?>
                        <?php endif; ?>
                        <span class="event-cat-tag"><?= clean($event['kategori']) ?></span>
                    </div>
                    <div class="event-body">
                        <div class="event-date">📅 <?= $tanggal ?> &middot; ⏰ <?= date('H:i', strtotime($event['waktu_event'])) ?> WIB</div>
                        <h3><?= clean($event['judul']) ?></h3>
                        <p><?= clean($event['deskripsi']) ?></p>
                        <div class="event-meta">
                            <span>📍 <?= clean($event['lokasi']) ?></span>
                            <span class="status-badge <?= $statusClass ?>"><?= clean($event['status']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
$conn->close();
require_once __DIR__ . '/includes/footer_public.php';
?>
