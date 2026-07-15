<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$conn = getConnection();

// Ambil nama file gambar dulu supaya bisa dihapus dari folder uploads
$stmt = $conn->prepare("SELECT gambar FROM events WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($event) {
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        if (!empty($event['gambar']) && file_exists(__DIR__ . '/../assets/uploads/' . $event['gambar'])) {
            unlink(__DIR__ . '/../assets/uploads/' . $event['gambar']);
        }
        $_SESSION['notif'] = ['type' => 'success', 'pesan' => 'Event berhasil dihapus.'];
    } else {
        $_SESSION['notif'] = ['type' => 'error', 'pesan' => 'Gagal menghapus event.'];
    }
    $stmt->close();
} else {
    $_SESSION['notif'] = ['type' => 'error', 'pesan' => 'Event tidak ditemukan.'];
}

$conn->close();
header('Location: ' . base_url('dashboard/event_list.php'));
exit;
