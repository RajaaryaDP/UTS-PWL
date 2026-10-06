# UTS Pemrograman Web Lanjut (PWL) - Sistem Manajemen Akun

Proyek ini merupakan implementasi **Native PHP MVC Skeleton** untuk Ujian Tengah Semester (UTS) mata kuliah **Pemrograman Web Lanjut**, Jurusan Teknik Informatika dan Komputer - Politeknik Negeri Jakarta.

## 🚀 Fitur Utama
1. **Autentikasi Pengguna:** Login (dengan enkripsi Bcrypt & Session) serta Logout.
2. **Manajemen Akun (`accounts`):** Tambah, Edit, Hapus (Soft Delete), Tampil Daftar Akun, dan Pencarian multi-kolom (Nama, Email, NIM/NIP, Tipe Akun).
3. **Manajemen Tipe Akun (`account_type`):** Tambah, Edit, Hapus (Soft Delete), dan Pencarian Tipe Akun (Relasi 1:1 dengan Akun).
4. **Manajemen Jenis Aksi (`actions`):** Tambah, Edit, Hapus (Soft Delete), dan Pencarian Master Aksi / Hak Akses.
5. **Keamanan & Standar Basis Data:**
   - Primary Key menggunakan **UUID v4 (`CHAR(36)`)** di seluruh tabel.
   - Kolom penjejakan wajib: `created_at`, `updated_at`, dan `deleted_at` (Soft Delete).
   - Format database: `PBL_TI_2025_A_RAJA` (dan fallback `tik_pbl`).

---

## 🛠️ Persyaratan Lingkungan
- PHP 8.1+
- Composer
- MySQL / MariaDB (Laragon / XAMPP)

---

## 📦 Setup & Instalasi

1. **Instal Dependensi:**
   ```bash
   composer install
   ```

2. **Setup Basis Data:**
   Eksekusi file SQL yang telah disiapkan di `docs/database_uts.sql` ke MySQL Anda:
   ```bash
   mysql -u root -p PBL_TI_2025_A_RAJA < docs/database_uts.sql
   ```
   *(File SQL sudah mencakup skema tabel `account_type`, `actions`, `accounts`, serta **seed data** yang 100% identik dengan contoh mock-up pada lembar soal UTS).*

3. **Konfigurasi Database:**
   Pastikan konfigurasi di `config/database.php` sesuai dengan kredensial MySQL lokal Anda.

---

## ▶️ Menjalankan Aplikasi

Jalankan perintah berikut di terminal:
```bash
composer serve
```
Buka browser di alamat:
```
http://localhost:5000
```

---

## 🔑 Kredensial Akun Pengujian (Seed Data)
| Peran | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@pnj.ac.id` | `admin123` | NIP: `520000000000000746` (Status: Aktif) |
| **Dosen (Dummy)** | `iam@balbalcode.my.id` | `password123` | NIM: `201012121` (Status: Nonaktif) |

---

## 📑 Dokumentasi Lengkap
Laporan dan dokumentasi lengkap sesuai kriteria penilaian UTS (Deskripsi Sistem, Kebutuhan Sistem, Proses Pembuatan Fitur, dan Panduan Demo) tersedia di file:
👉 **[DOKUMENTASI_UTS.md](file:///c:/laragon/www/pwl-sample-master/pwl-sample-master/DOKUMENTASI_UTS.md)**
