# DOKUMENTASI SISTEM MANAJEMEN AKUN (UTS PEMROGRAMAN WEB LANJUT)

**Program Studi:** D4 Teknik Informatika  
**Jurusan:** Teknik Informatika dan Komputer - Politeknik Negeri Jakarta  
**Mata Kuliah:** Pemrograman Web Lanjut  
**Dosen Pengampu:** Asep Taufik Muharram, S.Kom., M.Kom. & Ikbal Maulana, S.Tr.Kom.  
**Tahun Akademik:** 2026/2027  

---

## 1. Deskripsi Sistem

### 1.1 Latar Belakang & Tujuan
Sistem Manajemen Akun ini dikembangkan sebagai solusi pengelolaan otentikasi, otorisasi, dan hak akses pengguna dalam lingkungan institusi pendidikan/organisasi. Sistem ini bertujuan untuk:
1. Menyediakan repositori terpusat untuk akun pengguna (Dosen, Mahasiswa, Administrator, dan Staff).
2. Mengelola kategori tipe akun (*Account Type*) dengan relasi satu-ke-satu (*one-to-one*) terhadap akun.
3. Mengelola hak aksi sistem (*Actions / Permissions*) seperti Create, Read, Update, Delete.
4. Menjamin keamanan sistem melalui autentikasi berbasis session, enkripsi kata sandi menggunakan algoritma **Bcrypt**, serta integritas data historis melalui mekanisme **Soft Delete**.

### 1.2 Pendekatan Arsitektur (Native PHP MVC)
Sistem ini dibangun secara murni (*pure native*) menggunakan pola arsitektur **Model-View-Controller (MVC)** tanpa framework pihak ketiga (tanpa Laravel atau CodeIgniter), dengan alur kerja:
- **Front Controller (`index.php`):** Gerbang tunggal yang menangani parsing URI, rute dinamis, *Auth Guard*, serta pengecekan metode HTTP (GET vs POST).
- **Model (`models/`):** Bertanggung jawab atas manipulasi database menggunakan PHP Data Objects (PDO) dan *Prepared Statements* untuk mencegah serangan *SQL Injection*.
- **View (`views/`):** Menyajikan antarmuka grafis pengguna (UI) modern berbasis Bootstrap 5 dan Bootstrap Icons.
- **Controller (`controllers/`):** Menjembatani request HTTP pengguna, melakukan validasi bisnis, memanggil model, dan memuat view yang sesuai.

---

## 2. Kebutuhan Sistem

### 2.1 Lingkungan Pengembangan & Kebutuhan Pendukung
- **Bahasa Pemrograman:** PHP 8.1+
- **Database Server:** MySQL 8.0+ / MariaDB 10.4+
- **Web Server:** Apache (Laragon / XAMPP) dengan modul `mod_rewrite` aktif, atau PHP Built-in Server CLI (`php -S`).
- **Composer:** Digunakan untuk pustaka `ramsey/uuid` (versi ^4.9) guna men-generate identifier unik UUID v4.

### 2.2 Aturan & Desain Basis Data
Sesuai instruksi soal ujian:
1. **Format Nama Database:**  
   `PBL_{JURUSAN}_{ANGKATAN}_{KELAS}_{NAMA}`  
   *Contoh implementasi:* `PBL_TI_2025_A_RAJA` (didukung juga database fallback `tik_pbl`).
2. **Primary Key:** Seluruh tabel menggunakan **UUID v4 (`CHAR(36)`)** sebagai primary key.
3. **Kolom Wajib:** Setiap tabel memiliki atribut penjejakan waktu:
   - `created_at DATETIME` (Waktu pencatatan data)
   - `updated_at DATETIME` (Waktu pembaruan data)
   - `deleted_at DATETIME` (Penanda soft delete, bernilai `NULL` jika aktif)

### 2.3 Skema Tabel & Relasi

#### A. Tabel `account_type`
Menyimpan referensi kategori peran pengguna.
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `CHAR(36)` | Primary Key (UUID) |
| `name` | `VARCHAR(128)` | Nama tipe akun (misal: Admin, Dosen, Mahasiswa) |
| `description` | `TEXT` | Penjelasan peran & wewenang |
| `created_at` | `DATETIME` | Waktu dibuat |
| `updated_at` | `DATETIME` | Waktu diupdate |
| `deleted_at` | `DATETIME` | Penanda soft delete |

#### B. Tabel `actions`
Menyimpan hak jenis aksi sistem.
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `CHAR(36)` | Primary Key (UUID) |
| `name` | `VARCHAR(128)` | Nama aksi (Create, Read, Update, Delete) |
| `description` | `TEXT` | Deskripsi fungsionalitas aksi |
| `created_at` | `DATETIME` | Waktu dibuat |
| `updated_at` | `DATETIME` | Waktu diupdate |
| `deleted_at` | `DATETIME` | Penanda soft delete |

#### C. Tabel `accounts`
Menyimpan data akun pengguna dengan relasi 1:1 ke `account_type`.
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `CHAR(36)` | Primary Key (UUID) |
| `name` | `VARCHAR(128)` | Nama lengkap pengguna |
| `email` | `VARCHAR(128)` | Email unik pengguna |
| `password` | `TEXT` | Hash kata sandi (Bcrypt) |
| `account_type_id` | `CHAR(36)` | Foreign Key ke `account_type.id` |
| `status` | `VARCHAR(128)` | Status akun ('Aktif' / 'Nonaktif') |
| `identification_number` | `VARCHAR(128)` | Nomor identitas resmi (NIM / NIP) |
| `identification_type` | `ENUM('NIM', 'NIP')` | Jenis identitas resmi |
| `created_at` | `DATETIME` | Waktu dibuat |
| `updated_at` | `DATETIME` | Waktu diupdate |
| `deleted_at` | `DATETIME` | Penanda soft delete |

---

## 3. Proses Pembuatan Fitur, Logika & Algoritma

### 3.1 Mekanisme Routing & Front Controller (`index.php`)
- URL dipetakan dengan pola `/{resource}/{id?}/{action?}`.
- Segmen URL diterjemahkan secara otomatis ke controller:
  - `/accounts` ➔ `Accounts::index()`
  - `/accounts/create` ➔ `Accounts::create()`
  - `/accounts/store` ➔ `Accounts::store()` (POST)
  - `/accounts/{id}/edit` ➔ `Accounts::edit($id)`
  - `/accounts/{id}/update` ➔ `Accounts::update($id)` (POST)
  - `/accounts/{id}/delete` ➔ `Accounts::delete($id)` (POST)
- **Proteksi HTTP Method:** Operasi mutasi data (`store`, `update`, `delete`) wajib berjenis `POST`. Akses via `GET` akan memicu error `404 - Action not found`.

### 3.2 Keamanan & Autentikasi (`Auth.php` & `index.php`)
1. **Auth Guard:** Setiap request diverifikasi. Jika session `$_SESSION['user']` belum ada dan pengguna mengakses selain rute `/auth`, pengguna otomatis dialihkan ke halaman login.
2. **Login Verification:**
   - Pencarian record berdasarkan email menggunakan `AccountsModel::getByEmail()`.
   - Pencocokan password menggunakan `password_verify($password, $user['password'])`.
   - Jika cocok, simpan data identitas akun ke `$_SESSION['user']`.
3. **Logout:** Melakukan `session_destroy()` dan me-redirect kembali ke login.

### 3.3 Logika Soft Delete
Seluruh penghapusan data tidak menghapus baris tabel secara fisik (*hard delete*), melainkan memperbarui stempel waktu:
```sql
UPDATE {table} SET deleted_at = NOW() WHERE id = ? AND deleted_at IS NULL;
```
Ketika data diambil, query menambahkan klausa penyeleksi:
```sql
SELECT * FROM {table} WHERE deleted_at IS NULL;
```
Hal ini melindungi riwayat data dan mencegah *broken foreign key* secara tidak sengaja.

### 3.4 Logika Pencarian Multi-Kolom
Pencarian dilakukan secara fleksibel menggunakan parameter `?q=keyword` dengan memanfaatkan prepared statements:
- Pada **Accounts**: mencari kecocokan pada `name`, `email`, `identification_number`, atau nama tipe akun `account_type.name`.
- Pada **Account Types & Actions**: mencari kecocokan pada `name` atau `description`.

---

## 4. Panduan Demo & Pengujian Aplikasi

### 4.1 Menjalankan Aplikasi
1. Buka terminal pada folder proyek:
   ```bash
   composer serve
   ```
2. Buka browser pada alamat: `http://localhost:5000` (atau `http://localhost/pwl-sample-master/pwl-sample-master`).

### 4.2 Skenario Pengujian Fitur
1. **Pengujian Halaman Login:**
   - Akses rute `/` ➔ Sistem otomatis mendeteksi belum login dan mengarahkan ke `/auth`.
   - Masukkan akun bawaan:
     - **Email:** `admin@pnj.ac.id`
     - **Password:** `admin123`
   - Klik **Login**. Sistem berhasil masuk ke halaman utama **Manajemen Akun**.
2. **Pengujian Modul Akun (`/accounts`):**
   - **Tampil Data:** Memperlihatkan daftar akun dengan badge tipe akun, nomor identitas, dan status.
   - **Pencarian:** Ketik nama "dummy" pada kotak pencarian lalu klik "Cari". Hasil akan memfilter data secara instan. Klik tombol reset untuk mengembalikan seluruh data.
   - **Tambah Akun:** Klik "+ Tambah Akun", isi form (Nama, Email unik, Password, Pilih Tipe Akun, Status, Jenis Identitas NIM/NIP, Nomor Identitas), klik "Simpan".
   - **Ubah Akun:** Klik tombol edit (pensil kuning), ubah data, klik "Simpan".
   - **Hapus Akun:** Klik tombol sampah merah. Konfirmasi dialog akan muncul, dan data akan di-*soft delete*.
3. **Pengujian Modul Tipe Akun (`/account-types`):**
   - Akses menu sidebar "Tipe Akun".
   - Uji penambahan tipe akun baru, pencarian, pengeditan deskripsi, dan penghapusan.
4. **Pengujian Modul Jenis Aksi (`/actions`):**
   - Akses menu sidebar "Jenis Aksi".
   - Uji CRUD pada master jenis aksi (*Create, Read, Update, Delete*).
5. **Pengujian Logout:**
   - Klik tombol **Logout** di pojok kanan atas topbar.
   - Session terhapus dan sistem kembali ke form login.
