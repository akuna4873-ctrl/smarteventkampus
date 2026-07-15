# 🎓 Smart Event Campus

Website informasi kegiatan kampus (seminar, workshop, lomba, pelatihan) lengkap dengan
dashboard admin untuk mencatat, mengubah, menghapus, dan melihat data event.

## 📁 Struktur Folder

```
smart-event-campus/
├── config/
│   ├── database.php       -> koneksi database
│   └── config.php         -> session, base url, fungsi bantu
├── includes/
│   ├── header_public.php  -> navbar halaman publik
│   ├── footer_public.php  -> footer halaman publik
│   ├── header_dashboard.php
│   ├── footer_dashboard.php
│   ├── sidebar.php        -> menu sidebar dashboard
│   └── auth_check.php     -> proteksi halaman dashboard
├── assets/
│   ├── css/style.css      -> semua styling
│   ├── js/script.js       -> interaksi (filter, konfirmasi hapus, dll)
│   └── uploads/           -> gambar event yang diupload
├── auth/
│   ├── login.php              -> login admin
│   ├── logout.php             -> logout admin
│   ├── register.php           -> registrasi akun mahasiswa
│   ├── login_mahasiswa.php    -> login mahasiswa
│   └── logout_mahasiswa.php   -> logout mahasiswa
├── mahasiswa/
│   └── index.php              -> portal mahasiswa (lihat semua event)
├── dashboard/
│   ├── index.php          -> ringkasan statistik
│   ├── event_list.php     -> daftar & kelola event
│   ├── event_add.php      -> tambah event (Create)
│   ├── event_edit.php     -> edit event (Update)
│   └── event_delete.php   -> hapus event (Delete)
├── index.php               -> halaman utama publik
├── setup_admin.php         -> buat akun admin pertama (HAPUS setelah dipakai)
└── database.sql            -> struktur tabel database
```