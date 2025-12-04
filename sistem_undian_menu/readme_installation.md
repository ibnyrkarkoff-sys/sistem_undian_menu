# Sistem Undian Menu Harian - SMK Schuwenzel Paul

## Panduan Pemasangan (Installation Guide)

### Keperluan Sistem (System Requirements)
- XAMPP/WAMP/LAMP (PHP 7.4 atau lebih tinggi)
- MySQL Database
- Web Browser (Chrome, Firefox, Edge, dll)

### Langkah Pemasangan (Installation Steps)

#### 1. Pasang XAMPP
- Muat turun XAMPP dari https://www.apachefriends.org
- Install XAMPP pada komputer anda
- Pastikan Apache dan MySQL berjalan

#### 2. Setup Folder Projek
- Buka folder `C:\xampp\htdocs\` (Windows) atau `/opt/lampp/htdocs/` (Linux)
- Cipta folder baru bernama `sistem_undian_menu`
- Salin semua fail PHP ke dalam folder ini

#### 3. Setup Database
- Buka phpMyAdmin di browser: `http://localhost/phpmyadmin`
- Klik tab "SQL"
- Salin dan paste keseluruhan kod dari fail `database_setup.sql`
- Klik "Go" untuk menjalankan query
- Database `sistem_undian_menu` akan dicipta dengan semua jadual dan data sampel

#### 4. Konfigurasi Database (Optional)
- Buka fail `config.php`
- Edit jika perlu (biasanya tidak perlu diubah):
  ```php
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASS', '');
  define('DB_NAME', 'sistem_undian_menu');
  ```

#### 5. Akses Sistem
- Buka browser dan pergi ke: `http://localhost/sistem_undian_menu/`
- Anda akan melihat halaman log masuk

### Akaun Login Untuk Testing

#### Admin Accounts:
- **ID:** A001, **Password:** qwerty (Major Major)
- **ID:** A002, **Password:** kokkokpp (Say Gex)
- **ID:** A003, **Password:** goobert (Sum Ting Wong)
- **ID:** A004, **Password:** goobaaart (Wi Tu Lo)

#### Voter Accounts:
- **ID:** P001, **Password:** bingus (Amelia Clarke)
- **ID:** P002, **Password:** floppa (Charlotte Hayes)
- **ID:** P003, **Password:** wunkus (Genghis Khan)
- **ID:** P004, **Password:** wawa (George Whitmore)
- **ID:** P005, **Password:** slop (Harry Foster)

### Struktur Fail (File Structure)

```
sistem_undian_menu/
│
├── config.php                 # Database configuration
├── index.php                  # Login page
├── logout.php                 # Logout functionality
│
├── voter_dashboard.php        # Voter dashboard
├── voting_page.php            # Voting interface
│
├── admin_dashboard.php        # Admin dashboard
├── admin_voters.php           # Manage voters
├── admin_food.php             # Manage food items
├── admin_results.php          # View voting results
└── admin_reports.php          # Generate reports
```

### Fungsi Sistem (System Features)

#### Untuk Pengundi (Voters):
1. ✅ Log masuk ke sistem
2. ✅ Kemaskini profil peribadi
3. ✅ Undi menu untuk hari esok
4. ✅ Lihat sejarah undian

#### Untuk Admin:
1. ✅ Log masuk ke panel admin
2. ✅ Tambah/buang pengundi
3. ✅ Tambah/buang item menu
4. ✅ Lihat keputusan undian mengikut peratus
5. ✅ Cetak laporan undian
6. ✅ Jana laporan analisis

### Troubleshooting

**Masalah: Cannot connect to database**
- Pastikan MySQL service berjalan di XAMPP Control Panel
- Semak username/password dalam config.php
- Pastikan database telah dicipta

**Masalah: Page not found**
- Pastikan folder berada di dalam htdocs
- Semak URL: http://localhost/sistem_undian_menu/
- Pastikan Apache service berjalan

**Masalah: Session error**
- Pastikan session.save_path dapat ditulis
- Restart Apache service

### Nota Penting untuk SPM Project

1. **Dokumentasi Lengkap**: Sistem ini mengikut dokumentasi Fasa 1 dan Fasa 2 yang telah disediakan
2. **Database Normalization**: Database telah dinormalisasikan ke 3NF mengikut keperluan
3. **ERD**: Struktur database mengikut ERD yang telah direka
4. **User Interface**: Antara muka mesra pengguna dengan bahasa Melayu
5. **Print Functionality**: Keputusan dan laporan boleh dicetak

### Cadangan Penambahbaikan (Future Enhancements)

- Upload gambar sebenar untuk item menu
- Email notification untuk keputusan undian
- Multiple voting sessions per day
- Mobile responsive design
- Vote history analytics with charts
- Export results to Excel/PDF

### Sokongan (Support)

Jika ada masalah atau pertanyaan, sila rujuk kepada:
- Dokumentasi Fasa 1 dan Fasa 2
- phpMyAdmin untuk semak struktur database
- Error logs di XAMPP control panel

---

**Dicipta untuk projek SPM - SMK Schuwenzel Paul**
**Sistem Undian Menu Harian**
**Versi 1.0**