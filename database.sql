-- =========================================================
-- Database: smart_event_campus
-- Deskripsi: Struktur tabel untuk aplikasi Smart Event Campus
-- =========================================================

-- Hapus dulu tabel lama kalau ada (supaya tidak bentrok saat import ulang)
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS mahasiswa;

CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE mahasiswa (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nim VARCHAR(30) NOT NULL UNIQUE,
  nama_lengkap VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  jurusan VARCHAR(100) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(150) NOT NULL,
  kategori ENUM('Seminar','Workshop','Lomba','Pelatihan') NOT NULL,
  deskripsi TEXT NOT NULL,
  tanggal_event DATE NOT NULL,
  waktu_event TIME NOT NULL,
  lokasi VARCHAR(150) NOT NULL,
  penyelenggara VARCHAR(150) DEFAULT NULL,
  gambar VARCHAR(255) DEFAULT NULL,
  status ENUM('Akan Datang','Berlangsung','Selesai') NOT NULL DEFAULT 'Akan Datang',
  created_by INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES admin(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Akun admin default (sudah langsung bisa dipakai untuk login):
-- Username : Rizki
-- Password : ucup123
-- Password di bawah ini sudah dalam bentuk hash aman (bcrypt),
-- BUKAN teks polos, jadi aman disimpan di database.
INSERT INTO admin (username, password, nama_lengkap) VALUES
('Rizki', '$2b$12$fduBAWnistcQphNuuWwhf.M0E/uCrkCrSctl7zT0wLVM2m9ixfyIq', 'Rizki');

-- Catatan:
-- Kalau nanti mau tambah admin lain, tetap bisa pakai setup_admin.php
-- supaya passwordnya otomatis ter-hash dengan aman.
