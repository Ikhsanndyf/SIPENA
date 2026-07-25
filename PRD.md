# PROJECT REQUIREMENT DESIGN (PRD)

## Sistem Pelaporan dan Penanganan Kendala Aplikasi

**Versi:** 1.1
**Jenis:** Mini Project Pembelajaran Junior Developer  
**Framework:** Laravel 12  
**Bahasa antarmuka:** Bahasa Indonesia

---

## 1. Ringkasan Proyek

Sistem Pelaporan dan Penanganan Kendala Aplikasi adalah aplikasi web untuk mencatat, memantau, dan menyelesaikan laporan kendala aplikasi. Pengguna biasa bertindak sebagai pelapor, sedangkan developer bertugas memeriksa, memproses, dan mendokumentasikan penyelesaian laporan.

Proyek ini dibuat untuk melatih kompetensi Junior Developer, meliputi analisis kebutuhan, pemrograman, basis data, debugging, testing, dokumentasi teknis, Git, dan presentasi hasil.

Aplikasi tidak ditujukan sebagai sistem produksi skala besar. Fokus utama adalah kejelasan alur, penggunaan Laravel standar, kualitas dasar kode, dan keterlacakan proses pengembangan.

---

## 2. Tujuan Proyek

### 2.1 Tujuan Utama

Membangun aplikasi pelaporan kendala yang memungkinkan:

1. Pelapor membuat dan memantau laporan kendala.
2. Developer memeriksa dan menangani laporan.
3. Sistem menyimpan status, prioritas, komentar, analisis, solusi, dan riwayat perubahan.
4. Seluruh proses dapat diuji dan didokumentasikan.

### 2.2 Tujuan Pembelajaran

Developer diharapkan mampu:

- memahami requirement sederhana;
- membuat aplikasi dengan struktur MVC;
- membuat database relasional;
- menerapkan validasi dan otorisasi;
- menggunakan Git workflow;
- melakukan debugging berdasarkan log dan pesan error;
- membuat pengujian manual dan otomatis;
- membuat dokumentasi teknis;
- mempresentasikan hasil pengembangan.

---

## 3. Ruang Lingkup

### 3.1 Fitur yang Harus Dibuat

- Registrasi.
- Login dan logout.
- Verifikasi email.
- Lupa dan reset password.
- Perubahan password.
- Pengelolaan profil.
- Penghapusan akun dalam kondisi yang diizinkan.
- Role pelapor dan developer.
- Dashboard pelapor.
- Dashboard developer.
- Pembuatan laporan kendala.
- Daftar laporan milik pelapor.
- Daftar seluruh laporan untuk developer.
- Detail laporan.
- Edit dan hapus laporan dalam kondisi tertentu.
- Pencarian dan filter laporan.
- Pengubahan prioritas.
- Pengubahan status.
- Catatan analisis developer.
- Solusi penyelesaian.
- Komentar.
- Riwayat perubahan status.
- Upload satu gambar lampiran.
- Penutupan laporan.
- Testing manual.
- Laravel Feature Test.
- Dokumentasi proyek.

### 3.2 Fitur yang Tidak Termasuk

AI coding assistant tidak boleh menambahkan fitur berikut tanpa instruksi eksplisit:

- aplikasi mobile;
- REST API publik;
- notifikasi bisnis laporan melalui email atau WhatsApp;
- integrasi pihak ketiga;
- real-time chat;
- WebSocket;
- multi-tenant;
- pembayaran;
- dashboard chart kompleks;
- multiple attachment;
- Filament;
- Livewire;
- DTO;
- repository pattern;
- microservices;
- event sourcing;
- package role dan permission;
- package activity log.

---

## 4. Tech Stack

### Backend

- PHP 8.3
- Laravel 12
- Eloquent ORM
- Laravel Validation
- Laravel Middleware
- Laravel Policy
- PHPUnit / Laravel Feature Test

### Frontend

- Blade
- HTML5
- Tailwind CSS
- JavaScript dasar
- Vite

### Database

- MySQL 8 atau MariaDB yang kompatibel
- Laravel Migration
- Seeder
- Factory

### Tools

- Laragon
- Visual Studio Code
- Composer
- Node.js dan NPM
- Git
- GitHub
- phpMyAdmin atau database client lain

---

## 5. Prinsip Pengembangan

AI coding assistant wajib mengikuti prinsip berikut:

1. Gunakan fitur bawaan Laravel selama masih mencukupi.
2. Gunakan MVC standar.
3. Gunakan Form Request untuk validasi.
4. Gunakan Policy untuk otorisasi data.
5. Gunakan enum PHP untuk role, status, dan prioritas.
6. Gunakan named route.
7. Gunakan route model binding.
8. Gunakan eager loading untuk mencegah N+1 query.
9. Gunakan database transaction untuk proses perubahan status.
10. Jangan menulis query di Blade.
11. Jangan menaruh business logic panjang di route.
12. Controller harus singkat dan mudah dibaca.
13. Nama class, method, variable, tabel, dan route menggunakan bahasa Inggris.
14. Teks antarmuka menggunakan Bahasa Indonesia.
15. Hindari overengineering.
16. Jangan menambahkan package tanpa alasan dan persetujuan.
17. Setiap fitur penting harus memiliki pengujian.
18. PRD adalah sumber aturan utama proyek.

---

## 6. Aktor Sistem

### 6.1 Pelapor

Pelapor dapat:

- registrasi;
- login dan logout;
- melihat dashboard pribadi;
- membuat laporan;
- melihat laporan miliknya;
- melihat detail laporan;
- mengubah laporan yang masih `submitted`;
- menghapus laporan yang masih `submitted`;
- menambahkan komentar;
- menutup laporan yang sudah `resolved`.

Pelapor tidak dapat:

- melihat laporan pengguna lain;
- mengubah prioritas;
- mengubah status penanganan;
- menulis analisis developer;
- menulis solusi;
- mengakses halaman developer.

### 6.2 Developer

Developer dapat:

- melihat semua laporan;
- mencari dan memfilter laporan;
- melihat detail laporan;
- menetapkan prioritas;
- mengubah status;
- menulis catatan analisis;
- menulis solusi;
- menambahkan komentar;
- melihat riwayat status;
- membuka kembali laporan `resolved`;
- menutup laporan yang telah selesai.

Developer tidak memiliki fitur hapus laporan melalui antarmuka utama.

### 6.3 Fitur Akun Bersama

Reporter dan developer dapat:

- memperbarui nama dan email pada profil;
- mengubah password;
- meminta reset password;
- menggunakan verifikasi email bawaan Laravel Breeze;
- menghapus akun jika belum memiliki relasi dengan data laporan, komentar, atau riwayat status.

Email untuk reset password dan verifikasi email merupakan bagian dari autentikasi bawaan Laravel Breeze, bukan notifikasi bisnis laporan.

Penghapusan akun wajib ditolak dengan pesan yang jelas jika akun telah memiliki laporan, komentar, atau riwayat status. Aturan ini menjaga integritas data dan mengikuti foreign key `restrict`.

---

## 7. Role Pengguna

Role yang tersedia:

```text
reporter
developer
```

Ketentuan:

- Role disimpan pada kolom `role` di tabel `users`.
- Default registrasi adalah `reporter`.
- Role `developer` hanya dibuat melalui seeder atau pengaturan langsung oleh administrator.
- Tidak menggunakan package role dan permission.

---

## 8. Status Laporan

Status yang tersedia:

```text
submitted
reviewed
in_progress
resolved
closed
rejected
```

### Definisi

- `submitted`: laporan baru dibuat.
- `reviewed`: laporan telah diperiksa.
- `in_progress`: laporan sedang ditangani.
- `resolved`: solusi telah diberikan.
- `closed`: laporan ditutup.
- `rejected`: laporan ditolak.

### Alur Normal

```text
submitted → reviewed → in_progress → resolved → closed
```

### Alur Penolakan

```text
submitted → rejected
reviewed → rejected
```

### Membuka Kembali

```text
resolved → in_progress
```

### Aturan

1. Laporan baru selalu `submitted`.
2. Hanya developer yang dapat mengubah status penanganan.
3. Status `resolved` wajib memiliki solusi.
4. Reporter dapat mengubah `resolved` menjadi `closed` untuk laporan miliknya.
5. Developer dapat menutup laporan yang sudah `resolved`.
6. Laporan `closed` tidak dapat diedit.
7. Laporan `rejected` tidak dapat diproses kembali pada versi awal.

---

## 9. Prioritas

Prioritas yang tersedia:

```text
low
medium
high
critical
```

Default laporan baru adalah `medium`.

- `low`: tidak mengganggu fungsi utama.
- `medium`: mengganggu sebagian fungsi.
- `high`: mengganggu fungsi penting.
- `critical`: menyebabkan gangguan serius atau sistem tidak dapat digunakan.

Hanya developer yang dapat mengubah prioritas.

---

## 10. Kategori Laporan

Kategori awal:

- Bug
- Permasalahan Akses
- Permasalahan Data
- Permasalahan Tampilan
- Permasalahan Performa
- Lainnya

Kategori dibuat melalui seeder. Tidak perlu halaman CRUD kategori pada versi awal.

---

## 11. Studi Kasus Utama

Seorang pegawai menggunakan aplikasi internal untuk memasukkan data pengguna. Ketika tombol simpan ditekan, sistem menampilkan error dan data tidak tersimpan.

Pelapor membuat laporan dengan informasi:

- judul masalah;
- kategori;
- halaman terkait;
- deskripsi;
- langkah reproduksi;
- hasil aktual;
- hasil yang diharapkan;
- dampak;
- browser atau perangkat;
- gambar lampiran opsional.

Developer kemudian:

1. Memeriksa laporan.
2. Menetapkan prioritas.
3. Mengubah status menjadi `reviewed`.
4. Melakukan analisis.
5. Mengubah status menjadi `in_progress`.
6. Memperbaiki masalah.
7. Melakukan testing.
8. Menulis solusi.
9. Mengubah status menjadi `resolved`.
10. Pelapor memeriksa dan menutup laporan.

---

## 12. User Story

### Pelapor

- Sebagai pengguna baru, saya ingin registrasi agar dapat membuat laporan.
- Sebagai pengguna, saya ingin login agar dapat mengakses laporan saya.
- Sebagai pelapor, saya ingin membuat laporan kendala agar developer dapat menanganinya.
- Sebagai pelapor, saya ingin melihat daftar laporan saya agar dapat memantau progres.
- Sebagai pelapor, saya ingin melihat detail laporan agar mengetahui status dan solusi.
- Sebagai pelapor, saya ingin mengubah laporan yang belum diproses.
- Sebagai pelapor, saya ingin menghapus laporan yang dibuat secara keliru.
- Sebagai pelapor, saya ingin menambahkan informasi melalui komentar.
- Sebagai pelapor, saya ingin menutup laporan yang sudah selesai.

### Developer

- Sebagai developer, saya ingin melihat seluruh laporan.
- Sebagai developer, saya ingin mencari dan memfilter laporan.
- Sebagai developer, saya ingin menetapkan prioritas.
- Sebagai developer, saya ingin mengubah status penanganan.
- Sebagai developer, saya ingin mencatat hasil analisis.
- Sebagai developer, saya ingin menulis solusi.
- Sebagai developer, saya ingin melihat riwayat status.

---

## 13. Kebutuhan Fungsional

| ID | Kebutuhan |
|---|---|
| FR-01 | Sistem menyediakan registrasi, login, dan logout. |
| FR-02 | Halaman internal hanya dapat diakses pengguna login. |
| FR-03 | Sistem membedakan role reporter dan developer. |
| FR-04 | Reporter dapat membuat laporan. |
| FR-05 | Sistem membuat nomor laporan unik otomatis. |
| FR-06 | Reporter hanya melihat laporan miliknya. |
| FR-07 | Developer melihat seluruh laporan. |
| FR-08 | Developer dapat mencari berdasarkan nomor, judul, atau pelapor. |
| FR-09 | Developer dapat memfilter status, prioritas, kategori, dan tanggal. |
| FR-10 | Reporter hanya dapat mengedit laporan submitted miliknya. |
| FR-11 | Reporter hanya dapat menghapus laporan submitted miliknya. |
| FR-12 | Developer mengubah status sesuai aturan transisi. |
| FR-13 | Developer mengubah prioritas. |
| FR-14 | Developer menyimpan catatan analisis. |
| FR-15 | Developer menyimpan solusi. |
| FR-16 | Reporter dan developer dapat berkomentar. |
| FR-17 | Setiap perubahan status disimpan dalam riwayat. |
| FR-18 | Laporan resolved dapat ditutup. |
| FR-19 | Semua input divalidasi pada server. |
| FR-20 | Daftar laporan menggunakan pagination. |
| FR-21 | Pengguna dapat memperbarui profil dan password miliknya. |
| FR-22 | Sistem menyediakan verifikasi email serta lupa dan reset password melalui fitur bawaan Laravel Breeze. |
| FR-23 | Pengguna dapat menghapus akun jika belum memiliki relasi dengan laporan, komentar, atau riwayat status. |
| FR-24 | Sistem menolak penghapusan akun yang masih memiliki relasi data dengan pesan yang jelas. |

---

## 14. Kebutuhan Nonfungsional

### Keamanan

- Password di-hash.
- Route internal menggunakan middleware autentikasi.
- Akses data menggunakan Policy.
- Form menggunakan CSRF protection.
- Upload divalidasi.
- Query menggunakan Eloquent atau query builder.

### Kinerja

- Pagination 10 data per halaman.
- Relasi menggunakan eager loading.
- Query tidak dijalankan berulang dari Blade.
- Lampiran maksimal 2 MB.

### Usability

- Antarmuka berbahasa Indonesia.
- Pesan validasi mudah dipahami.
- Status dan prioritas ditampilkan sebagai badge.
- Tombol hanya muncul jika pengguna memiliki izin.

### Maintainability

- Penamaan konsisten.
- Validasi menggunakan Form Request.
- Otorisasi menggunakan Policy.
- Service hanya digunakan untuk proses status.
- Komentar kode hanya untuk logika yang tidak jelas.

---

## 15. Struktur Database

### 15.1 Tabel `users`

| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | Primary key |
| name | VARCHAR(100) | Wajib |
| email | VARCHAR(150) | Wajib dan unik |
| email_verified_at | TIMESTAMP NULL | Opsional |
| password | VARCHAR(255) | Wajib |
| role | VARCHAR(20) | Default reporter |
| remember_token | VARCHAR(100) NULL | Bawaan Laravel |
| created_at | TIMESTAMP | Otomatis |
| updated_at | TIMESTAMP | Otomatis |

### 15.2 Tabel `categories`

| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | Primary key |
| name | VARCHAR(80) | Wajib dan unik |
| slug | VARCHAR(100) | Wajib dan unik |
| created_at | TIMESTAMP | Otomatis |
| updated_at | TIMESTAMP | Otomatis |

### 15.3 Tabel `reports`

| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | Primary key |
| report_number | VARCHAR(30) | Wajib dan unik |
| user_id | BIGINT UNSIGNED | Foreign key users |
| category_id | BIGINT UNSIGNED | Foreign key categories |
| title | VARCHAR(150) | Wajib |
| page_url | VARCHAR(255) NULL | Opsional |
| description | TEXT | Wajib |
| reproduction_steps | TEXT | Wajib |
| actual_result | TEXT | Wajib |
| expected_result | TEXT | Wajib |
| impact | TEXT NULL | Opsional |
| environment | VARCHAR(255) NULL | Opsional |
| attachment_path | VARCHAR(255) NULL | Opsional |
| status | VARCHAR(30) | Default submitted |
| priority | VARCHAR(20) | Default medium |
| analysis_note | TEXT NULL | Khusus developer |
| solution | TEXT NULL | Khusus developer |
| resolved_at | TIMESTAMP NULL | Saat resolved |
| closed_at | TIMESTAMP NULL | Saat closed |
| created_at | TIMESTAMP | Otomatis |
| updated_at | TIMESTAMP | Otomatis |

### 15.4 Tabel `comments`

| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | Primary key |
| report_id | BIGINT UNSIGNED | Foreign key reports |
| user_id | BIGINT UNSIGNED | Foreign key users |
| comment | TEXT | Wajib |
| created_at | TIMESTAMP | Otomatis |
| updated_at | TIMESTAMP | Otomatis |

### 15.5 Tabel `report_status_histories`

| Kolom | Tipe | Aturan |
|---|---|---|
| id | BIGINT UNSIGNED | Primary key |
| report_id | BIGINT UNSIGNED | Foreign key reports |
| changed_by | BIGINT UNSIGNED | Foreign key users |
| old_status | VARCHAR(30) NULL | Status sebelumnya |
| new_status | VARCHAR(30) | Status baru |
| note | VARCHAR(255) NULL | Catatan perubahan |
| created_at | TIMESTAMP | Otomatis |
| updated_at | TIMESTAMP | Otomatis |

---

## 16. Relasi Database

```text
User hasMany Reports
User hasMany Comments
User hasMany ReportStatusHistories through changed_by

Category hasMany Reports

Report belongsTo User
Report belongsTo Category
Report hasMany Comments
Report hasMany ReportStatusHistories

Comment belongsTo Report
Comment belongsTo User

ReportStatusHistory belongsTo Report
ReportStatusHistory belongsTo User through changed_by
```

### Aturan Foreign Key

- `reports.user_id`: restrict.
- `reports.category_id`: restrict.
- `comments.report_id`: cascade.
- `comments.user_id`: restrict.
- `report_status_histories.report_id`: cascade.
- `report_status_histories.changed_by`: restrict.

Fitur hapus akun bawaan Laravel Breeze dipertahankan dengan ketentuan:

- akun dapat dihapus jika belum memiliki laporan, komentar, atau riwayat status;
- akun yang telah memiliki relasi tersebut tidak dapat dihapus karena foreign key `restrict`;
- penolakan harus ditangani oleh aplikasi dan menampilkan pesan yang jelas, bukan hanya mengandalkan database exception;
- data laporan, komentar, dan riwayat status tidak boleh dihapus otomatis ketika akun dihapus.

---

## 17. Model, Enum, Policy, dan Request

### Model

```text
User
Category
Report
Comment
ReportStatusHistory
```

### Enum

```text
UserRole
ReportStatus
ReportPriority
```

### Policy

```text
ReportPolicy
CommentPolicy
```

### Form Request

```text
StoreReportRequest
UpdateReportRequest
UpdateReportStatusRequest
UpdateReportHandlingRequest
StoreCommentRequest
```

### Service

```text
ReportStatusService
```

---

## 18. Struktur Folder

```text
app/
├── Enums/
│   ├── ReportPriority.php
│   ├── ReportStatus.php
│   └── UserRole.php
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── ReportController.php
│   │   ├── ReportCommentController.php
│   │   └── Developer/
│   │       ├── DashboardController.php
│   │       └── ReportController.php
│   ├── Middleware/
│   │   └── EnsureUserIsDeveloper.php
│   └── Requests/
│       ├── StoreCommentRequest.php
│       ├── StoreReportRequest.php
│       ├── UpdateReportHandlingRequest.php
│       ├── UpdateReportRequest.php
│       └── UpdateReportStatusRequest.php
├── Models/
├── Policies/
└── Services/
    └── ReportStatusService.php
```

View:

```text
resources/views/
├── layouts/
│   └── app.blade.php
├── components/
├── dashboard/
│   └── index.blade.php
├── reports/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── show.blade.php
│   └── edit.blade.php
└── developer/
    ├── dashboard/
    │   └── index.blade.php
    └── reports/
        ├── index.blade.php
        └── show.blade.php
```

---

## 19. Routing

### Route Autentikasi dan Akun

Route autentikasi dan akun bawaan Laravel Breeze yang dipertahankan:

```text
GET    /register
POST   /register
GET    /login
POST   /login
POST   /logout
GET    /forgot-password
POST   /forgot-password
GET    /reset-password/{token}
POST   /reset-password
GET    /verify-email
GET    /verify-email/{id}/{hash}
POST   /email/verification-notification
GET    /confirm-password
POST   /confirm-password
PUT    /password
GET    /profile
PATCH  /profile
DELETE /profile
```

Route profil, perubahan password, verifikasi email, dan logout menggunakan middleware autentikasi sesuai kebutuhan route bawaan Laravel Breeze. Penghapusan akun juga mengikuti batasan relasi data pada bagian 16.

### Route Pelapor

```text
GET    /dashboard
GET    /reports
GET    /reports/create
POST   /reports
GET    /reports/{report}
GET    /reports/{report}/edit
PUT    /reports/{report}
DELETE /reports/{report}
POST   /reports/{report}/comments
PATCH  /reports/{report}/close
```

### Route Developer

Prefix:

```text
/developer
```

Route:

```text
GET   /developer/dashboard
GET   /developer/reports
GET   /developer/reports/{report}
PATCH /developer/reports/{report}/status
PATCH /developer/reports/{report}/handling
POST  /developer/reports/{report}/comments
```

Semua route internal menggunakan middleware `auth`. Route developer juga menggunakan middleware role developer.

---

## 20. Dashboard

### Dashboard Pelapor

Menampilkan data milik pengguna login:

- total laporan;
- jumlah submitted;
- jumlah in progress;
- jumlah resolved;
- lima laporan terbaru.

### Dashboard Developer

Menampilkan:

- total laporan;
- jumlah submitted;
- jumlah reviewed;
- jumlah in progress;
- jumlah resolved;
- jumlah critical;
- lima laporan terbaru;
- lima laporan high atau critical.

Tidak perlu chart pada versi awal.

---

## 21. Form Laporan

Field:

```text
category_id
title
page_url
description
reproduction_steps
actual_result
expected_result
impact
environment
attachment
```

### Validasi

```text
category_id: required, exists:categories,id

title: required, string, min:5, max:150

page_url: nullable, string, max:255

description: required, string, min:20, max:5000

reproduction_steps: required, string, min:10, max:5000

actual_result: required, string, min:10, max:3000

expected_result: required, string, min:10, max:3000

impact: nullable, string, max:2000

environment: nullable, string, max:255

attachment: nullable, image, mimes:jpg,jpeg,png,webp, max:2048
```

---

## 22. Nomor Laporan

Format:

```text
INC-YYYYMM-XXXX
```

Contoh:

```text
INC-202607-0001
```

Aturan:

1. Dibuat otomatis.
2. Tidak diinput pengguna.
3. Harus unik.
4. Dapat menggunakan ID laporan sebagai dasar urutan.
5. Dibuat setelah model tersimpan.
6. Gunakan transaction bila diperlukan.
7. Jangan menggunakan random string sebagai nomor utama.

---

## 23. Edit dan Hapus

Reporter hanya dapat mengedit atau menghapus laporan jika:

```text
report.user_id == auth()->id()
report.status == submitted
```

Tombol tidak boleh ditampilkan jika tidak memiliki izin. Pemeriksaan backend melalui Policy tetap wajib.

---

## 24. Proses Penanganan Developer

Halaman detail developer menampilkan:

- nomor laporan;
- data pelapor;
- kategori;
- isi laporan;
- lampiran;
- status;
- prioritas;
- analisis;
- solusi;
- komentar;
- riwayat status.

Developer dapat:

1. Mengubah prioritas.
2. Menulis catatan analisis.
3. Menulis solusi.
4. Mengubah status.
5. Menambahkan komentar.

Perubahan status wajib melalui `ReportStatusService`.

Service bertanggung jawab untuk:

- memvalidasi transisi;
- memperbarui status;
- mengisi `resolved_at`;
- mengisi `closed_at`;
- menyimpan riwayat;
- menjalankan database transaction.

---

## 25. Aturan Transisi Status

```php
submitted => [reviewed, rejected]
reviewed => [in_progress, rejected]
in_progress => [resolved]
resolved => [in_progress, closed]
closed => []
rejected => []
```

Transisi berikut harus ditolak:

```text
submitted → resolved
closed → in_progress
rejected → reviewed
```

Status `resolved` hanya valid jika `solution` terisi.

---

## 26. Riwayat Status

Setiap perubahan status menyimpan:

- `report_id`;
- `changed_by`;
- `old_status`;
- `new_status`;
- `note`;
- waktu perubahan.

Riwayat:

- ditampilkan dari terbaru ke terlama;
- tidak dapat diedit;
- tidak dapat dihapus melalui antarmuka.

---

## 27. Komentar

Aturan komentar:

- hanya pengguna yang memiliki akses ke laporan;
- wajib diisi;
- minimal 2 karakter;
- maksimal 2000 karakter;
- tidak dapat diedit;
- tidak dapat dihapus pada versi awal;
- menampilkan nama dan waktu pengirim.

---

## 28. Upload Lampiran

- Satu gambar per laporan.
- Opsional.
- Format JPG, JPEG, PNG, atau WEBP.
- Maksimal 2 MB.
- Disimpan pada storage publik.
- Gunakan nama file hasil generate.
- Jalankan `php artisan storage:link`.
- File dihapus ketika laporan dihapus.
- Tidak membuat multiple upload.

---

## 29. Pencarian, Filter, dan Pagination

### Pencarian

- nomor laporan;
- judul;
- nama pelapor.

### Filter

- status;
- prioritas;
- kategori;
- tanggal awal;
- tanggal akhir.

### Pengurutan

```text
created_at descending
```

### Pagination

```text
10 data per halaman
```

Gunakan `withQueryString()` agar filter tetap tersimpan saat pagination.

---

## 30. Otorisasi

`ReportPolicy` minimal memiliki method:

```text
viewAny
view
create
update
delete
close
handle
```

Aturan:

- reporter hanya melihat laporan sendiri;
- developer melihat seluruh laporan;
- reporter hanya mengedit dan menghapus laporan submitted miliknya;
- reporter hanya menutup laporan resolved miliknya;
- developer menangani seluruh laporan;
- akses tidak sah menghasilkan HTTP 403.

Menyembunyikan tombol saja tidak cukup. Backend wajib melakukan pemeriksaan Policy.

---

## 31. Middleware Developer

Buat middleware:

```text
EnsureUserIsDeveloper
```

Middleware memastikan:

- pengguna telah login;
- role pengguna adalah developer;
- akses tidak sah menghasilkan HTTP 403 atau redirect dengan pesan yang sesuai.

Tidak menggunakan package permission.

---

## 32. Seeder

### CategorySeeder

Membuat kategori awal.

### DeveloperSeeder

Membuat akun developer contoh.

Data sensitif dapat diambil dari `.env`:

```text
DEVELOPER_NAME
DEVELOPER_EMAIL
DEVELOPER_PASSWORD
```

Jangan menulis password produksi pada repository publik.

### DatabaseSeeder

Memanggil:

```text
CategorySeeder
DeveloperSeeder
```

---

## 33. Antarmuka

Prinsip UI:

- sederhana;
- bersih;
- responsif;
- mudah dipahami;
- fokus pada fungsi.

Komponen utama:

- navbar;
- sidebar developer opsional;
- statistik card;
- tabel laporan;
- badge status;
- badge prioritas;
- form;
- flash message;
- pagination;
- empty state.

Warna status harus konsisten. Detail warna dapat disesuaikan selama mudah dibedakan.

---

## 34. Error Handling dan Logging

### Error Handling

1. Validasi menggunakan Form Request.
2. Gunakan exception bawaan Laravel untuk 403 dan 404.
3. Gunakan transaction untuk perubahan status.
4. Tangani kegagalan upload.
5. Jangan menampilkan error sensitif pada production.
6. Jangan menggunakan `try-catch` pada semua method tanpa alasan.
7. Jangan menelan exception tanpa logging.

### Logging

Gunakan log untuk:

- kegagalan upload;
- kegagalan perubahan status;
- exception saat menyimpan proses penanganan;
- kejadian tidak normal yang perlu diperiksa.

Tidak membuat sistem activity log khusus.

---

## 35. Testing Manual

Buat file:

```text
docs/manual-test-cases.md
```

Skenario minimal:

1. Registrasi berhasil.
2. Email duplikat ditolak.
3. Login berhasil.
4. Password salah ditolak.
5. Reporter membuat laporan valid.
6. Field wajib kosong ditolak.
7. Reporter melihat laporan sendiri.
8. Reporter tidak dapat melihat laporan orang lain.
9. Reporter mengedit laporan submitted.
10. Reporter tidak dapat mengedit laporan in progress.
11. Reporter menghapus laporan submitted.
12. Developer melihat seluruh laporan.
13. Reporter tidak dapat membuka halaman developer.
14. Developer mengubah prioritas.
15. Developer mengubah status valid.
16. Transisi tidak valid ditolak.
17. Resolved tanpa solusi ditolak.
18. Komentar berhasil ditambahkan.
19. Riwayat status tercatat.
20. Reporter menutup laporan resolved.
21. Pengguna memperbarui profil.
22. Pengguna mengubah password.
23. Pengguna meminta reset password.
24. Pengguna memverifikasi email.
25. Pengguna tanpa relasi data menghapus akun.
26. Pengguna dengan relasi laporan tidak dapat menghapus akun.
27. Password salah menolak penghapusan akun.

Format test case:

```text
ID
Nama Skenario
Prasyarat
Langkah Pengujian
Data Uji
Hasil yang Diharapkan
Hasil Aktual
Status
Catatan
```

---

## 36. Automated Testing

Test minimal:

```text
AuthenticationTest
EmailVerificationTest
PasswordResetTest
PasswordUpdateTest
ProfileTest
ReportCreationTest
ReportAuthorizationTest
ReportUpdateTest
ReportStatusTest
ReportCommentTest
DeveloperAccessTest
```

Cakupan wajib:

- laporan dapat dibuat;
- validasi laporan bekerja;
- pengguna tidak dapat melihat laporan orang lain;
- laporan non-submitted tidak dapat diedit reporter;
- non-developer tidak dapat membuka halaman developer;
- developer dapat menjalankan transisi valid;
- transisi tidak valid ditolak;
- resolved membutuhkan solusi;
- perubahan status membuat riwayat;
- reporter dapat menutup laporan resolved;
- verifikasi dan reset password bekerja;
- profil dan password dapat diperbarui;
- akun tanpa relasi data dapat dihapus;
- akun dengan relasi data tidak dapat dihapus.

---

## 37. Dokumentasi

File minimal:

```text
README.md
PRD.md
docs/
├── database-design.md
├── manual-test-cases.md
├── bug-reports.md
└── presentation-outline.md
```

README memuat:

- deskripsi proyek;
- tech stack;
- kebutuhan sistem;
- instalasi;
- konfigurasi `.env`;
- migration dan seeder;
- cara menjalankan aplikasi;
- cara menjalankan test;
- akun developer lokal;
- ringkasan fitur.

---

## 38. Git Workflow

Branch:

```text
main
develop
feature/*
fix/*
docs/*
test/*
```

Contoh:

```text
feature/authentication
feature/report-management
feature/developer-dashboard
feature/report-status
fix/report-authorization
test/report-feature-tests
docs/project-documentation
```

Commit convention:

```text
type: deskripsi singkat
```

Type:

```text
feat
fix
docs
test
refactor
style
chore
```

Contoh:

```text
feat: add report submission feature
fix: prevent reporter from viewing another report
test: add report status transition tests
docs: add installation guide
```

Satu commit harus fokus pada satu perubahan.

---

## 39. Tahapan Implementasi

### Tahap 1 — Inisialisasi

1. Buat proyek Laravel 12.
2. Konfigurasi database.
3. Jalankan aplikasi.
4. Inisialisasi Git.
5. Buat repository.
6. Buat branch `develop`.
7. Commit awal.

### Tahap 2 — Autentikasi dan Role

1. Pasang autentikasi Blade.
2. Tambahkan kolom role.
3. Buat enum role.
4. Buat seeder developer.
5. Buat middleware developer.
6. Buat test akses role.

### Tahap 3 — Database

1. Buat migration semua tabel.
2. Buat model dan relasi.
3. Buat enum status dan prioritas.
4. Buat seeder kategori.
5. Buat factory.
6. Jalankan migration dan seeder.

### Tahap 4 — Fitur Pelapor

1. Dashboard pelapor.
2. Daftar laporan.
3. Form tambah.
4. Validasi.
5. Nomor laporan.
6. Detail laporan.
7. Edit.
8. Hapus.
9. Policy.
10. Test authorization.

### Tahap 5 — Fitur Developer

1. Dashboard developer.
2. Daftar seluruh laporan.
3. Pencarian dan filter.
4. Detail penanganan.
5. Prioritas.
6. Analisis.
7. Solusi.
8. Perubahan status.
9. Status service.
10. Riwayat status.
11. Feature Test.

### Tahap 6 — Komentar

1. Form komentar.
2. Validasi.
3. Otorisasi.
4. Tampilan komentar.
5. Test komentar.

### Tahap 7 — UI

1. Rapikan layout.
2. Tambahkan badge.
3. Tambahkan flash message.
4. Tambahkan empty state.
5. Pastikan responsif.

### Tahap 8 — Debugging

1. Jalankan seluruh alur.
2. Periksa log.
3. Uji akses tidak sah.
4. Uji upload.
5. Uji filter dan pagination.
6. Perbaiki bug pada branch `fix/*`.
7. Dokumentasikan bug.

### Tahap 9 — Testing

1. Buat test manual.
2. Buat Feature Test.
3. Jalankan seluruh test.
4. Perbaiki test gagal.
5. Catat hasil.

### Tahap 10 — Dokumentasi dan Presentasi

1. Lengkapi README.
2. Buat database design.
3. Buat bug report.
4. Buat outline presentasi.
5. Uji demo.
6. Merge ke `develop`.
7. Uji ulang.
8. Merge ke `main`.

---

## 40. Skenario Debugging Latihan

### Bug 1 — Akses Laporan Pengguna Lain

Gejala: reporter dapat membuka URL laporan pengguna lain.

Penyelesaian:

1. Reproduksi bug.
2. Periksa controller dan route model binding.
3. Buat atau perbaiki `ReportPolicy`.
4. Tambahkan authorize.
5. Buat Feature Test.
6. Pastikan HTTP 403.
7. Dokumentasikan bug.

### Bug 2 — Status Langsung Resolved

Gejala: `submitted` dapat langsung menjadi `resolved`.

Penyelesaian:

1. Periksa logika status.
2. Terapkan daftar transisi.
3. Pindahkan logika ke service.
4. Tolak transisi tidak valid.
5. Tambahkan test.

### Bug 3 — Resolved Tanpa Solusi

Penyelesaian:

1. Tambahkan validasi kondisional.
2. Solusi wajib saat status resolved.
3. Tambahkan test.
4. Tampilkan pesan validasi jelas.

### Bug 4 — N+1 Query

Penyelesaian:

1. Periksa query daftar laporan.
2. Gunakan `with(['user', 'category'])`.
3. Uji ulang jumlah query.
4. Dokumentasikan perbaikan.

### Bug 5 — File Tidak Valid

Penyelesaian:

1. Periksa Form Request.
2. Validasi MIME dan ukuran.
3. Uji file bukan gambar dan file lebih dari 2 MB.
4. Pastikan ditolak.

### Bug 6 — Filter Hilang Saat Pagination

Penyelesaian:

1. Reproduksi bug.
2. Gunakan `withQueryString()`.
3. Uji kombinasi filter.
4. Dokumentasikan.

---

## 41. Definition of Done

Sebuah fitur dianggap selesai jika:

- sesuai requirement;
- struktur data benar;
- validasi tersedia;
- otorisasi tersedia;
- UI dapat digunakan;
- alur normal berhasil;
- alur gagal ditangani;
- tidak ada error penting pada log;
- test relevan dibuat;
- kode mengikuti standar;
- commit jelas;
- dokumentasi diperbarui.

---

## 42. Acceptance Criteria Proyek

Proyek selesai jika:

1. Registrasi dan login bekerja.
2. Role reporter dan developer berjalan.
3. Reporter dapat membuat laporan.
4. Reporter hanya melihat laporan sendiri.
5. Reporter dapat edit dan hapus laporan submitted.
6. Developer melihat seluruh laporan.
7. Pencarian dan filter bekerja.
8. Prioritas dapat diubah developer.
9. Status mengikuti alur.
10. Transisi tidak valid ditolak.
11. Resolved wajib memiliki solusi.
12. Riwayat status tersimpan.
13. Komentar bekerja.
14. Reporter dapat menutup laporan resolved.
15. Upload gambar tervalidasi.
16. Pagination bekerja.
17. Policy mencegah akses tidak sah.
18. Test utama berhasil.
19. README tersedia.
20. Tidak ada fitur di luar scope.
21. Verifikasi email serta lupa dan reset password bekerja.
22. Pengguna dapat memperbarui profil dan password.
23. Pengguna tanpa relasi data dapat menghapus akun.
24. Penghapusan akun yang memiliki relasi data ditolak tanpa menghapus data terkait.

---

## 43. Batasan untuk AI Coding Assistant

AI coding assistant wajib:

1. Membaca seluruh `PRD.md` sebelum membuat kode.
2. Mengerjakan satu tahap dalam satu waktu.
3. Tidak membuat seluruh aplikasi sekaligus.
4. Tidak mengubah tech stack.
5. Tidak menggunakan Filament atau Livewire.
6. Tidak menggunakan DTO atau repository pattern.
7. Tidak menambahkan package tanpa persetujuan.
8. Tidak membuat API tanpa instruksi.
9. Tidak menambah fitur di luar scope.
10. Tidak mengubah nama tabel, role, status, atau prioritas.
11. Tidak menyimpan business logic di Blade.
12. Tidak menaruh seluruh logika di controller.
13. Tidak menggunakan raw SQL jika Eloquent mencukupi.
14. Tidak mengabaikan validasi dan otorisasi.
15. Tidak membuat multiple attachment.
16. Tidak membuat notifikasi.
17. Tidak mengubah `.env` secara otomatis.
18. Tidak menulis password asli ke repository.
19. Tidak menghapus test gagal agar build terlihat berhasil.
20. Menjelaskan setiap file yang dibuat atau diubah.
21. Menyebutkan command yang harus dijalankan.
22. Menyatakan setiap asumsi.
23. Memberikan langkah pengujian.
24. Berhenti setelah tahap yang diminta selesai.

---

## 44. Format Instruksi untuk AI Coding Assistant

```text
Baca dan patuhi seluruh PRD.md.

Kerjakan hanya Tahap [nomor dan nama tahap].

Sebelum menulis kode:
1. Jelaskan tujuan tahap.
2. Sebutkan file yang dibuat atau diubah.
3. Sebutkan command yang harus dijalankan.
4. Jelaskan alur implementasi.

Saat menulis kode:
1. Gunakan Laravel 12.
2. Gunakan MVC standar.
3. Gunakan Form Request untuk validasi.
4. Gunakan Policy untuk otorisasi.
5. Jangan menambahkan fitur di luar PRD.
6. Jangan menggunakan package tambahan.

Setelah menulis kode:
1. Jelaskan cara menguji.
2. Berikan checklist hasil.
3. Sebutkan kemungkinan error.
4. Berhenti setelah tahap selesai.
```

---

## 45. Prompt Awal untuk AI Coding Assistant

```text
Anda bertindak sebagai Senior Laravel Developer yang membimbing Junior Developer.

Baca seluruh file PRD.md sebelum memberikan solusi. PRD.md adalah sumber aturan utama proyek. Jangan mengubah requirement, tech stack, struktur database, role, status, prioritas, atau ruang lingkup tanpa instruksi eksplisit.

Gunakan Laravel 12, PHP 8.3, MySQL, Blade, Tailwind CSS, JavaScript dasar, Git, dan PHPUnit.

Gunakan MVC standar Laravel. Hindari overengineering. Jangan menggunakan Filament, Livewire, DTO, repository pattern, microservices, atau package tambahan yang tidak diperlukan.

Kerjakan proyek secara bertahap. Pada setiap tahap:
1. Jelaskan tujuan.
2. Sebutkan file yang dibuat atau diubah.
3. Berikan command yang diperlukan.
4. Tulis kode lengkap yang relevan.
5. Jelaskan alur kode.
6. Berikan langkah pengujian.
7. Berikan checklist selesai.
8. Berhenti setelah tahap tersebut selesai.

Jangan melanjutkan ke tahap berikutnya tanpa instruksi.
```

---

## 46. Hasil Akhir yang Diharapkan

Hasil proyek:

- aplikasi Laravel yang dapat dijalankan;
- autentikasi dua role;
- fitur pelaporan;
- fitur penanganan;
- riwayat status;
- komentar;
- pencarian dan filter;
- testing manual;
- automated test;
- dokumentasi teknis;
- repository Git terstruktur;
- bahan presentasi.

Proyek harus menunjukkan pemahaman mengenai:

- analisis kebutuhan;
- struktur aplikasi;
- basis data;
- validasi;
- otorisasi;
- debugging;
- testing;
- dokumentasi;
- version control;
- komunikasi teknis.

---

## 47. Riwayat Perubahan PRD

| Versi | Tanggal | Perubahan | Alasan |
|---|---|---|---|
| 1.0 | 2026-07-24 | Dokumen awal | Inisialisasi mini project |
| 1.1 | 2026-07-24 | Mempertahankan fitur autentikasi dan manajemen akun bawaan Laravel Breeze serta menetapkan batas penghapusan akun | Menjaga fitur pendukung yang sudah tersedia tanpa mengorbankan integritas data SIPENA |

Setiap perubahan requirement wajib dicatat pada bagian ini sebelum implementasi dilakukan.
