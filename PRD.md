# PROJECT REQUIREMENT DESIGN (PRD)

## Sistem Pelaporan dan Penanganan Kendala Aplikasi (SIPENA)

**Versi:** 1.2
**Jenis:** Mini Project Pembelajaran Junior Developer
**Acuan utama:** Modul Mini Project Junior Developer KPK
**Framework:** Laravel 12
**Bahasa antarmuka:** Bahasa Indonesia

---

## 1. Ringkasan Proyek

SIPENA adalah aplikasi web untuk mencatat, mengelompokkan, menindaklanjuti, dan mendokumentasikan laporan kendala pada aplikasi internal.

Pelapor membuat dan memantau tiket miliknya. Developer memeriksa seluruh tiket, menentukan prioritas dan PIC, melakukan analisis, mengubah status, menulis solusi, serta memantau riwayat penanganan. Pelapor mengonfirmasi penyelesaian setelah developer menyatakan tiket menunggu konfirmasi.

Proyek ini merupakan latihan terstruktur Junior Developer yang mencakup SDLC, Laravel MVC, basis data relasional, Git workflow, debugging, testing, dokumentasi, dan presentasi.

PRD ini menerjemahkan modul latihan menjadi spesifikasi implementasi. Jika ditemukan perbedaan konsep, modul latihan menjadi acuan dan perubahan wajib dicatat pada riwayat PRD.

---

## 2. Tujuan

### 2.1 Tujuan Sistem

- Menyediakan media pelaporan kendala aplikasi yang terstruktur.
- Membantu developer mengelola prioritas, PIC, dan status tiket.
- Menyimpan analisis, solusi, komentar, lampiran, dan riwayat status.
- Membatasi akses sesuai role dan kepemilikan tiket.
- Menyediakan ringkasan operasional melalui dashboard.

### 2.2 Tujuan Pembelajaran

- Membaca dan menerjemahkan requirement.
- Menggunakan MVC standar Laravel.
- Merancang migration, relasi, seeder, dan factory.
- Menerapkan validasi, middleware, dan policy.
- Mengelola workflow status dalam transaction.
- Melakukan debugging berdasarkan bukti.
- Membuat testing manual dan otomatis.
- Menggunakan branch, commit, dan pull request.
- Menulis dokumentasi dan mempresentasikan hasil.

---

## 3. Ruang Lingkup

### 3.1 Fitur Wajib

- Registrasi, login, logout, dan fitur akun Laravel Breeze.
- Role `reporter` dan `developer`.
- Master aplikasi internal.
- Pembuatan dan pengelolaan tiket kendala.
- Nomor tiket otomatis.
- Daftar dan detail tiket.
- Akses tiket berdasarkan role dan kepemilikan.
- Penetapan prioritas dan PIC developer.
- Workflow status yang tervalidasi.
- Catatan analisis dan solusi.
- Komentar.
- Riwayat perubahan status.
- Satu lampiran opsional per tiket.
- Pencarian, filter, dan pagination.
- Dashboard ringkas.
- Testing manual dan otomatis.
- Simulasi debugging.
- Dokumentasi teknis dan presentasi.

### 3.2 Fitur Akun Breeze yang Dipertahankan

- Verifikasi email.
- Lupa dan reset password.
- Perubahan password.
- Pengelolaan profil.
- Penghapusan akun dalam kondisi yang diizinkan.

Email verifikasi dan reset password adalah bagian autentikasi, bukan notifikasi bisnis tiket.

### 3.3 Di Luar Scope

- aplikasi mobile;
- REST API publik;
- notifikasi bisnis email atau WhatsApp;
- integrasi pihak ketiga;
- real-time chat atau WebSocket;
- multi-tenant;
- pembayaran;
- dashboard chart kompleks;
- multiple attachment;
- role `admin` atau `coordinator` terpisah;
- Filament atau Livewire;
- package role/permission;
- package activity log;
- DTO, repository pattern, microservices, atau event sourcing.

---

## 4. Tech Stack

### Backend

- PHP 8.3
- Laravel 12
- Eloquent ORM
- Laravel Validation dan Form Request
- Laravel Middleware dan Policy
- PHPUnit / Laravel Feature Test

### Frontend

- Blade
- HTML5
- Tailwind CSS
- Alpine.js bawaan Breeze
- JavaScript dasar
- Vite

### Database dan Tools

- MySQL 8 atau MariaDB kompatibel
- Laravel Migration, Seeder, dan Factory
- Laragon
- Composer
- Node.js dan npm
- Git dan GitHub

---

## 5. Prinsip Pengembangan

1. Gunakan fitur bawaan Laravel selama mencukupi.
2. Gunakan MVC standar dan hindari overengineering.
3. Gunakan Form Request untuk validasi fitur domain.
4. Gunakan Policy untuk akses data.
5. Gunakan middleware untuk area berdasarkan role.
6. Gunakan PHP enum untuk role, status, prioritas, dan kategori.
7. Gunakan named route dan route model binding.
8. Gunakan eager loading untuk mencegah N+1.
9. Gunakan transaction untuk perubahan status dan history.
10. Jangan menulis query atau business logic di Blade.
11. Controller harus singkat dan mudah dibaca.
12. Service hanya digunakan untuk workflow status.
13. Penamaan kode dan database menggunakan Bahasa Inggris.
14. Teks antarmuka menggunakan Bahasa Indonesia.
15. Semua akses tidak sah harus diperiksa di backend.
16. Setiap fitur penting memiliki test berhasil dan gagal.
17. Jangan menambah package tanpa persetujuan.
18. PRD adalah spesifikasi implementasi dan modul latihan adalah acuan konsep.

---

## 6. Aktor dan Hak Akses

### 6.1 Reporter

Reporter dapat:

- registrasi, login, dan logout;
- mengelola akun;
- melihat dashboard pribadi;
- membuat tiket;
- melihat tiket miliknya;
- mengubah atau menghapus tiket miliknya yang masih `new`;
- menambahkan komentar pada tiket miliknya;
- mengonfirmasi penyelesaian tiket dari `waiting_confirmation` menjadi `resolved`.

Reporter tidak dapat:

- melihat tiket reporter lain;
- mengakses area developer;
- menetapkan PIC;
- mengubah prioritas;
- menulis analisis atau solusi;
- menjalankan workflow status developer.

### 6.2 Developer

Developer dapat:

- melihat seluruh tiket;
- mencari dan memfilter tiket;
- melihat dashboard operasional;
- menetapkan PIC developer;
- mengubah prioritas;
- menulis analisis dan solusi;
- mengubah status sesuai transition map;
- menambahkan komentar;
- melihat riwayat status.

Developer tidak memiliki fitur hapus tiket melalui antarmuka utama dan tidak melakukan konfirmasi akhir `waiting_confirmation → resolved`.

### 6.3 Perspektif Koordinator

User story koordinator pada modul dipenuhi oleh dashboard developer. Versi pertama tidak menambahkan role `coordinator` atau `admin`.

---

## 7. Role Pengguna

```text
reporter
developer
```

Ketentuan:

- role disimpan di `users.role`;
- registrasi selalu menghasilkan `reporter`;
- request registrasi tidak boleh menentukan role;
- developer hanya dibuat melalui seeder atau pengaturan terkontrol;
- tidak menggunakan package permission.

---

## 8. Istilah Domain

Istilah utama dalam kode mengikuti modul:

```text
Ticket      = laporan kendala
Application = aplikasi internal yang terdampak
Assignee    = developer/PIC
```

Antarmuka boleh menggunakan kata “Laporan”, tetapi nama class, tabel, route, dan test menggunakan `Ticket`.

---

## 9. Status Tiket

```text
new
analyzed
in_progress
waiting_confirmation
resolved
rejected
```

Definisi:

- `new`: tiket baru dibuat.
- `analyzed`: developer telah memeriksa tiket.
- `in_progress`: tiket sedang ditangani.
- `waiting_confirmation`: developer telah memberi solusi dan menunggu konfirmasi reporter.
- `resolved`: reporter mengonfirmasi kendala selesai.
- `rejected`: tiket ditolak.

Transition map:

```php
new => [analyzed, rejected]
analyzed => [in_progress, rejected]
in_progress => [waiting_confirmation, rejected]
waiting_confirmation => [in_progress, resolved]
resolved => []
rejected => []
```

Aturan:

1. Tiket baru selalu `new`.
2. Developer menangani transisi hingga `waiting_confirmation`.
3. `waiting_confirmation` harus memiliki solusi.
4. Hanya reporter pemilik yang mengubah `waiting_confirmation` menjadi `resolved`.
5. Developer dapat membuka kembali `waiting_confirmation` menjadi `in_progress`.
6. `resolved` dan `rejected` merupakan status akhir versi pertama.
7. Transisi ilegal wajib ditolak di backend.

---

## 10. Prioritas

```text
low
medium
high
critical
```

- default tiket baru adalah `medium`;
- hanya developer yang dapat mengubah prioritas;
- `critical` menunjukkan gangguan paling serius.

Contoh validasi pembuatan tiket pada modul mencantumkan input `priority`. Untuk menjaga konsistensi dengan tabel hak akses dan FR-05 pada modul, aturan bisnis yang digunakan adalah priority awal `medium` dari sistem dan perubahan priority hanya oleh developer.

Pengurutan critical di posisi teratas merupakan simulasi perubahan kebutuhan setelah versi awal, bukan aturan urutan MVP.

---

## 11. Kategori

Kategori disimpan sebagai string pada `tickets.category` dan direpresentasikan dengan `TicketCategory`:

```text
bug
access
data
display
other
```

Tidak membuat tabel atau CRUD kategori pada versi pertama.

---

## 12. Master Aplikasi

Tabel `applications` menyimpan aplikasi internal yang dapat dilaporkan.

Data seed:

- Sistem Kepegawaian
- Sistem Persuratan
- Dashboard Monitoring

Tidak membuat halaman CRUD aplikasi pada versi pertama.

---

## 13. User Story

- Sebagai reporter, saya ingin membuat tiket agar kendala dapat ditindaklanjuti.
- Sebagai reporter, saya ingin memantau tiket milik saya.
- Sebagai reporter, saya ingin memberikan komentar tambahan.
- Sebagai reporter, saya ingin mengonfirmasi bahwa kendala telah selesai.
- Sebagai developer, saya ingin melihat seluruh tiket.
- Sebagai developer, saya ingin menentukan prioritas dan PIC.
- Sebagai developer, saya ingin memfilter tiket untuk menentukan pekerjaan berikutnya.
- Sebagai developer, saya ingin mencatat analisis dan solusi.
- Sebagai developer, saya ingin melihat riwayat perubahan.
- Sebagai koordinator, saya ingin melihat ringkasan operasional melalui dashboard developer.

---

## 14. Kebutuhan Fungsional

| ID | Kebutuhan |
|---|---|
| FR-01 | Pengguna dapat registrasi, login, dan logout. |
| FR-02 | Reporter dapat membuat tiket kendala. |
| FR-03 | Reporter hanya dapat melihat tiket miliknya. |
| FR-04 | Developer dapat melihat seluruh tiket. |
| FR-05 | Developer dapat menetapkan prioritas dan PIC. |
| FR-06 | Developer dapat mengubah status sesuai transition map. |
| FR-07 | Pengguna dapat berkomentar pada tiket yang boleh diakses. |
| FR-08 | Sistem menyimpan riwayat perubahan status. |
| FR-09 | Developer dapat mengisi analisis dan solusi. |
| FR-10 | Tiket dapat dicari, difilter, dan dipaginasi. |
| FR-11 | Dashboard menampilkan ringkasan tiket. |
| FR-12 | Reporter dapat mengonfirmasi penyelesaian. |
| FR-13 | Sistem membuat nomor tiket unik otomatis. |
| FR-14 | Reporter dapat mengubah dan menghapus tiket `new` miliknya. |
| FR-15 | Sistem menerima satu lampiran opsional yang tervalidasi. |
| FR-16 | Pengguna dapat mengelola profil dan password. |
| FR-17 | Sistem menyediakan verifikasi dan reset password Breeze. |
| FR-18 | Akun tanpa relasi domain dapat dihapus. |
| FR-19 | Penghapusan akun dengan relasi domain ditolak dengan pesan jelas. |

---

## 15. Kebutuhan Nonfungsional

### Keamanan

- password di-hash;
- route internal menggunakan `auth`;
- area developer menggunakan middleware role;
- akses tiket menggunakan Policy;
- form menggunakan CSRF;
- upload divalidasi;
- output pengguna menggunakan escaping Blade;
- secret tidak disimpan di repository;
- akses ilegal menghasilkan HTTP 403.

### Kinerja

- pagination 10 data;
- relasi menggunakan eager loading;
- tidak ada query database di Blade;
- lampiran maksimal 2 MB.

### Usability dan Maintainability

- antarmuka Bahasa Indonesia;
- pesan validasi jelas;
- status dan prioritas menggunakan badge konsisten;
- tombol mengikuti otorisasi;
- penamaan konsisten;
- README dapat digunakan untuk instalasi dari awal.

---

## 16. Struktur Database

### 16.1 `users`

| Kolom | Aturan |
|---|---|
| id | primary key |
| name | varchar(100) |
| email | varchar(150), unique |
| email_verified_at | nullable |
| password | varchar(255) |
| role | varchar(20), default `reporter` |
| remember_token | nullable |
| timestamps | otomatis |

### 16.2 `applications`

| Kolom | Aturan |
|---|---|
| id | primary key |
| name | varchar(100), unique |
| slug | varchar(120), unique |
| timestamps | otomatis |

### 16.3 `tickets`

| Kolom | Aturan |
|---|---|
| id | primary key |
| ticket_number | varchar(30), unique |
| reporter_id | foreign key users |
| application_id | foreign key applications |
| assigned_to | foreign key users, nullable |
| title | varchar(150) |
| category | varchar(30) |
| priority | varchar(20), default `medium` |
| status | varchar(30), default `new` |
| description | text |
| reproduction_steps | text, nullable |
| analysis_notes | text, nullable |
| resolution_notes | text, nullable |
| resolved_at | timestamp, nullable |
| timestamps | otomatis |

### 16.4 `ticket_comments`

| Kolom | Aturan |
|---|---|
| id | primary key |
| ticket_id | foreign key tickets |
| user_id | foreign key users |
| comment | text |
| timestamps | otomatis |

### 16.5 `ticket_status_histories`

| Kolom | Aturan |
|---|---|
| id | primary key |
| ticket_id | foreign key tickets |
| changed_by | foreign key users |
| from_status | varchar(30), nullable |
| to_status | varchar(30) |
| notes | varchar(255), nullable |
| timestamps | otomatis |

### 16.6 `attachments`

| Kolom | Aturan |
|---|---|
| id | primary key |
| ticket_id | foreign key tickets, unique |
| original_name | varchar(255) |
| file_path | varchar(255) |
| mime_type | varchar(100) |
| file_size | unsigned integer |
| timestamps | otomatis |

`ticket_id` dibuat unique agar satu tiket maksimal memiliki satu lampiran pada versi pertama.

---

## 17. Relasi dan Foreign Key

```text
User hasMany reportedTickets melalui reporter_id
User hasMany assignedTickets melalui assigned_to
User hasMany ticketComments
User hasMany ticketStatusHistories melalui changed_by

Application hasMany Tickets

Ticket belongsTo reporter
Ticket belongsTo application
Ticket belongsTo assignee
Ticket hasMany comments
Ticket hasMany statusHistories
Ticket hasOne attachment
```

Aturan delete:

- `tickets.reporter_id`: restrict;
- `tickets.application_id`: restrict;
- `tickets.assigned_to`: null on delete;
- `ticket_comments.ticket_id`: cascade;
- `ticket_comments.user_id`: restrict;
- `ticket_status_histories.ticket_id`: cascade;
- `ticket_status_histories.changed_by`: restrict;
- `attachments.ticket_id`: cascade.

Akun yang menjadi reporter, pengirim komentar, atau pengubah status tidak dapat dihapus. Akun yang hanya menjadi PIC dapat dihapus dan `assigned_to` berubah menjadi null.

### 17.1 Latihan Query Database

Latihan wajib dari modul:

```sql
SELECT ticket_number, title, status, priority
FROM tickets
WHERE priority IN ('high', 'critical')
  AND status NOT IN ('resolved', 'rejected')
ORDER BY created_at ASC;

SELECT status, COUNT(*) AS total
FROM tickets
GROUP BY status;
```

Hasil latihan dan penjelasan mengenai nullable PIC, restrict, cascade, serta null-on-delete dicatat dalam `docs/database-design.md`.

---

## 18. Class Utama

### Model

```text
User
Application
Ticket
TicketComment
TicketStatusHistory
Attachment
```

### Enum

```text
UserRole
TicketStatus
TicketPriority
TicketCategory
```

### Policy

```text
TicketPolicy
TicketCommentPolicy
```

### Form Request

```text
StoreTicketRequest
UpdateTicketRequest
UpdateTicketHandlingRequest
UpdateTicketStatusRequest
StoreTicketCommentRequest
```

### Service

```text
TicketStatusService
```

---

## 19. Struktur Folder Domain

```text
app/
├── Enums/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── TicketController.php
│   │   ├── TicketCommentController.php
│   │   └── Developer/
│   │       ├── DashboardController.php
│   │       └── TicketController.php
│   ├── Middleware/
│   │   └── EnsureUserIsDeveloper.php
│   └── Requests/
├── Models/
├── Policies/
└── Services/
    └── TicketStatusService.php

resources/views/
├── tickets/
├── dashboard/
├── developer/
│   ├── dashboard/
│   └── tickets/
├── components/
└── layouts/
```

---

## 20. Routing

### Reporter

```text
GET    /dashboard
GET    /tickets
GET    /tickets/create
POST   /tickets
GET    /tickets/{ticket}
GET    /tickets/{ticket}/edit
PUT    /tickets/{ticket}
DELETE /tickets/{ticket}
POST   /tickets/{ticket}/comments
PATCH  /tickets/{ticket}/confirm
```

### Developer

```text
GET   /developer/dashboard
GET   /developer/tickets
GET   /developer/tickets/{ticket}
PATCH /developer/tickets/{ticket}/handling
PATCH /developer/tickets/{ticket}/status
POST  /developer/tickets/{ticket}/comments
```

Semua route internal memakai `auth`. Route developer memakai `['auth', 'developer']`.

Route akun Breeze yang telah tersedia tetap dipertahankan.

---

## 21. Dashboard

### Reporter

- total tiket miliknya;
- jumlah `new`;
- jumlah `in_progress`;
- jumlah `waiting_confirmation`;
- jumlah `resolved`;
- lima tiket terbaru.

### Developer

- total seluruh tiket;
- jumlah per status;
- jumlah prioritas `critical`;
- jumlah tiket tanpa PIC;
- lima tiket terbaru;
- lima tiket prioritas `high` atau `critical`.

Dashboard developer memenuhi kebutuhan ringkasan koordinator tanpa role baru.

---

## 22. Form Tiket dan Validasi

Field reporter:

```text
application_id
title
category
description
reproduction_steps
attachment
```

Validasi:

```text
application_id: required, exists:applications,id
title: required, string, min:5, max:150
category: required, enum TicketCategory
description: required, string, min:20, max:5000
reproduction_steps: nullable, string, max:5000
attachment: nullable, image, mimes:jpg,jpeg,png,webp, max:2048
```

Reporter tidak menginput status, prioritas, PIC, analisis, atau solusi. Nilai awal priority adalah `medium` dan status adalah `new`.

---

## 23. Nomor Tiket

Format:

```text
TCK-YYYYMM-XXXX
```

Contoh:

```text
TCK-202607-0001
```

Ketentuan:

- dibuat otomatis oleh sistem;
- tidak berasal dari request pengguna;
- unik;
- menggunakan ID tiket sebagai urutan latihan;
- pembuatan tiket dan nomor dilakukan secara aman dalam transaction;
- risiko concurrency didokumentasikan;
- tidak menggunakan random string sebagai nomor utama.

Kolom `ticket_number` boleh nullable pada insert awal, kemudian diisi dari ID dalam transaction dan tetap memiliki unique index.

---

## 24. Pengelolaan Tiket Reporter

Reporter dapat mengubah atau menghapus tiket jika:

```text
ticket.reporter_id == auth()->id()
ticket.status == new
```

Policy tetap wajib meskipun tombol disembunyikan. Lampiran ikut dihapus ketika tiket dihapus.

---

## 25. Penanganan Developer

Developer dapat menyimpan:

- `assigned_to`;
- `priority`;
- `analysis_notes`;
- `resolution_notes`;
- status baru;
- catatan perubahan status.

Ketentuan:

- `assigned_to` nullable dan harus menunjuk user role developer;
- priority harus termasuk `TicketPriority`;
- reporter tidak boleh mengubah field penanganan;
- `resolution_notes` wajib sebelum `waiting_confirmation`;
- perubahan status wajib melalui `TicketStatusService`.

---

## 26. TicketStatusService

Service bertanggung jawab untuk:

- memvalidasi transition map;
- membedakan transisi developer dan konfirmasi reporter;
- memastikan solusi tersedia sebelum `waiting_confirmation`;
- memperbarui status;
- mengisi `resolved_at` saat status menjadi `resolved`;
- menyimpan history;
- menjalankan database transaction;
- melempar exception yang dapat ditangani secara jelas.

---

## 27. Riwayat Status

Setiap perubahan menyimpan:

```text
ticket_id
changed_by
from_status
to_status
notes
created_at
```

Riwayat ditampilkan dari terbaru, tidak dapat diedit, dan tidak dapat dihapus melalui UI.

Pembuatan tiket mencatat history awal dengan `from_status = null` dan `to_status = new`.

---

## 28. Komentar

- hanya pengguna yang memiliki akses ke tiket;
- wajib, minimal 2 dan maksimal 2000 karakter;
- menampilkan nama dan waktu pengirim;
- tidak dapat diedit atau dihapus pada versi pertama.

---

## 29. Lampiran

- satu gambar per tiket;
- opsional;
- JPG, JPEG, PNG, atau WEBP;
- maksimal 2 MB;
- disimpan pada public storage dengan nama generated;
- metadata disimpan di tabel `attachments`;
- file lama dihapus ketika diganti;
- file dihapus ketika tiket dihapus;
- tidak membuat multiple upload.

---

## 30. Pencarian, Filter, dan Pagination

Pencarian:

- nomor tiket;
- judul;
- nama reporter.

Filter:

- aplikasi;
- status;
- prioritas;
- kategori;
- PIC;
- tanggal awal dan akhir.

Default urutan MVP:

```text
created_at descending
```

Pagination 10 data dan menggunakan `withQueryString()`.

---

## 31. Otorisasi

`TicketPolicy` minimal:

```text
viewAny
view
create
update
delete
confirm
handle
```

Aturan:

- reporter hanya melihat tiket miliknya;
- developer melihat seluruh tiket;
- reporter hanya update/delete tiket `new` miliknya;
- reporter hanya mengonfirmasi tiket `waiting_confirmation` miliknya;
- developer menangani seluruh tiket;
- akses ilegal menghasilkan 403.

---

## 32. Middleware Developer

`EnsureUserIsDeveloper` membatasi area developer.

```text
guest + auth middleware → redirect login
reporter → 403
developer → request diteruskan
```

Tidak menggunakan package permission.

---

## 33. Seeder

### ApplicationSeeder

Membuat tiga aplikasi internal dari modul.

### DeveloperSeeder

Membuat akun developer dari konfigurasi:

```text
DEVELOPER_NAME
DEVELOPER_EMAIL
DEVELOPER_PASSWORD
```

Seeder harus idempotent dan tidak menyimpan password produksi di repository.

### DatabaseSeeder

Memanggil:

```text
ApplicationSeeder
DeveloperSeeder
```

---

## 34. Antarmuka

- sederhana, bersih, responsif;
- navbar dan navigasi sesuai role;
- statistik card;
- tabel tiket;
- badge status dan prioritas;
- informasi aplikasi dan PIC;
- flash message;
- validation error;
- pagination;
- empty state.

---

## 35. Error Handling dan Logging

- gunakan Form Request;
- gunakan exception bawaan untuk 403 dan 404;
- gunakan transaction pada workflow;
- jangan menelan exception;
- jangan menampilkan detail sensitif pada production;
- log kegagalan upload, perubahan status, dan kejadian abnormal;
- `APP_DEBUG=false` pada production;
- tidak membuat activity log khusus.

---

## 36. Testing Manual

Dokumentasikan minimal skenario berikut pada `docs/manual-test-cases.md`:

1. Registrasi, login, dan logout.
2. Reporter membuat tiket valid.
3. Deskripsi terlalu pendek ditolak.
4. Nomor tiket dan status awal terbentuk.
5. Reporter hanya melihat tiket sendiri.
6. Akses tiket reporter lain menghasilkan 403.
7. Reporter mengubah dan menghapus tiket `new`.
8. Reporter tidak dapat mengubah tiket yang sudah dianalisis.
9. Developer melihat seluruh tiket.
10. Developer menetapkan prioritas dan PIC.
11. PIC harus ber-role developer.
12. Developer mengubah status valid.
13. Transisi ilegal ditolak.
14. History status tersimpan.
15. Developer mengisi analisis dan solusi.
16. Reporter mengonfirmasi `waiting_confirmation`.
17. Komentar berhasil dan akses komentar aman.
18. Pencarian, filter, dan pagination bekerja.
19. Lampiran tidak valid ditolak.
20. Akun dengan relasi domain tidak dapat dihapus.

Format:

```text
ID
Nama Skenario
Prasyarat
Langkah
Data Uji
Hasil Diharapkan
Hasil Aktual
Status
Catatan
```

---

## 37. Automated Testing

Test minimal:

```text
AuthenticationTest
DeveloperAccessTest
ApplicationSeederTest
TicketCreationTest
TicketAuthorizationTest
TicketUpdateTest
TicketHandlingTest
TicketStatusTest
TicketCommentTest
ProfileTest
```

Cakupan wajib:

- status awal dan nomor tiket;
- validasi input;
- akses kepemilikan dan 403;
- role developer;
- prioritas dan PIC;
- transition map;
- solusi sebelum `waiting_confirmation`;
- history dalam transaction;
- konfirmasi reporter;
- search/filter/pagination;
- akun dengan relasi domain tidak dapat dihapus.

Target modul minimal 5 automated test terlampaui.

---

## 38. Debugging Wajib

Minimal enam bug modul harus disimulasikan dan didokumentasikan:

| ID | Gejala | Arah Perbaikan |
|---|---|---|
| BUG-001 | Reporter melihat tiket pengguna lain | Policy dan test 403 |
| BUG-002 | Judul lebih dari 150 diterima | Form Request |
| BUG-003 | History tidak tersimpan | Transaction dan relasi |
| BUG-004 | Filter hilang saat pagination | `withQueryString()` |
| BUG-005 | Tiket resolved kembali ke new | Transition map |
| BUG-006 | Halaman error ketika PIC null | Null-safe/optional relation |

Setiap bug memiliki reproduksi, expected/actual result, root cause, fix, retest, dan regression test.

---

## 39. Dokumentasi

File minimal:

```text
README.md
PRD.md
CHANGELOG.md
docs/
├── database-design.md
├── backlog.md
├── manual-test-cases.md
├── bug-reports.md
├── user-guide.md
├── operational-log.md
├── daily-learning-log.md
├── presentation-outline.md
└── evidence/

.github/
└── pull_request_template.md
```

README memuat deskripsi, fitur, teknologi, instalasi, environment, migration/seeder, akun demo, testing, struktur proyek, dan known issues.

Folder `evidence` menyimpan screenshot fitur dan pengujian yang diperlukan untuk latihan.

`backlog.md` mencatat prioritas dan estimasi pekerjaan. `operational-log.md` mencatat issue dukungan, dampak, tindakan, dan status. `daily-learning-log.md` mencatat materi yang dipahami, kendala, dan tindakan berikutnya.

---

## 40. Git Workflow

```text
main
develop
feature/*
fix/*
test/*
docs/*
```

Alur:

```text
feature/fix/test/docs → pull request ke develop
develop → main setelah milestone stabil
```

Commit:

```text
feat: deskripsi Bahasa Indonesia
fix: deskripsi Bahasa Indonesia
test: deskripsi Bahasa Indonesia
docs: deskripsi Bahasa Indonesia
refactor: deskripsi Bahasa Indonesia
chore: deskripsi Bahasa Indonesia
```

Satu branch dan commit harus memiliki tujuan yang jelas.

Contoh branch sesuai modul:

```text
feature/authentication
feature/ticket-management
feature/status-history
feature/comment-filter
test/ticket-feature-test
fix/unauthorized-ticket-access
```

---

## 41. Tahapan Implementasi

1. Analisis requirement, scope, backlog, dan repository.
2. Setup Laravel, Breeze, konfigurasi, role, seeder developer, dan middleware.
3. Enum domain, ERD, migration, model, factory, dan seeder aplikasi.
4. CRUD tiket reporter, nomor tiket, lampiran, dan Policy.
5. Area developer, prioritas, PIC, analisis, solusi, dan workflow.
6. Komentar, history, pencarian, filter, dan pagination.
7. Dashboard, validasi, error handling, dan UI.
8. Simulasi enam bug dan regression test.
9. Testing manual dan otomatis.
10. Dokumentasi, bukti, changelog, demo, dan presentasi.

Setiap tahap diselesaikan dan diuji sebelum lanjut.

Setiap hari latihan mencatat materi yang dipahami, kendala, serta tindakan berikutnya pada `docs/daily-learning-log.md`.

---

## 42. Simulasi Perubahan Setelah MVP

Setelah versi awal selesai, kerjakan sebagai mini sprint terpisah:

1. Tiket `critical` yang belum selesai tampil sebelum prioritas lain.
2. Urutan kedua memakai `created_at` terlama.
3. Status `rejected` mewajibkan notes minimal 10 karakter.
4. Request tanpa notes ditolak dan status tidak berubah.
5. Semua regression test tetap lulus.

Perubahan dibuat melalui issue, branch, test, self-review, pull request, dan pembaruan dokumentasi.

---

## 43. Definition of Done

Fitur selesai jika:

- sesuai requirement modul dan PRD;
- migration dan relasi benar;
- validasi dan otorisasi tersedia;
- alur berhasil dan gagal ditangani;
- UI dapat digunakan;
- test relevan lulus;
- tidak ada error penting pada log;
- commit dan pull request jelas;
- dokumentasi diperbarui;
- pembuat dapat menjelaskan keputusan teknisnya.

---

## 44. Acceptance Criteria Proyek

1. Autentikasi dan dua role bekerja.
2. Master aplikasi tersedia.
3. Reporter dapat membuat tiket dengan nomor otomatis dan status `new`.
4. Reporter hanya melihat tiket miliknya.
5. Developer melihat seluruh tiket.
6. Developer dapat menetapkan prioritas dan PIC.
7. PIC hanya user developer.
8. Workflow mengikuti transition map.
9. Transisi ilegal ditolak.
10. Analisis dan solusi tersimpan.
11. History tersimpan bersama perubahan status.
12. Reporter dapat mengonfirmasi `waiting_confirmation`.
13. Komentar bekerja sesuai hak akses.
14. Lampiran tervalidasi.
15. Pencarian, filter, dan pagination bekerja.
16. Dashboard reporter dan developer bekerja.
17. Policy mencegah IDOR.
18. Enam bug modul didokumentasikan.
19. Testing manual dan otomatis lulus.
20. README, changelog, user guide, dan bahan presentasi tersedia.
21. Instalasi dapat dilakukan dari repository kosong.
22. Tidak ada fitur di luar scope.

---

## 45. Batasan untuk AI Coding Assistant

AI coding assistant wajib:

1. Membaca modul dan PRD sebelum memberi implementasi.
2. Menjadikan modul sebagai acuan konsep.
3. Mengerjakan satu sub-tahap dalam satu waktu.
4. Menjelaskan tujuan, file, command, alur, dan cara test.
5. Tidak mengubah istilah domain, tabel, role, enum, atau workflow tanpa instruksi.
6. Tidak membuat seluruh aplikasi sekaligus.
7. Tidak mengubah tech stack.
8. Tidak menambahkan package tanpa persetujuan.
9. Tidak membuat API atau fitur di luar scope.
10. Tidak mengubah `.env` otomatis tanpa izin.
11. Tidak menyimpan secret.
12. Tidak menghapus test gagal untuk menyembunyikan masalah.
13. Menggunakan Form Request, Policy, middleware, enum, dan transaction sesuai kebutuhan.
14. Menyebutkan asumsi dan langkah pengujian.
15. Berhenti setelah sub-tahap yang diminta selesai.

---

## 46. Hasil Akhir

- aplikasi SIPENA yang dapat dijalankan;
- autentikasi reporter dan developer;
- master aplikasi internal;
- CRUD dan workflow tiket;
- prioritas dan PIC;
- komentar, history, filter, dashboard, dan lampiran;
- testing manual dan otomatis;
- enam simulasi debugging;
- repository Git dengan branch dan PR yang tertib;
- dokumentasi teknis;
- backlog dan log belajar;
- log dukungan operasional;
- bukti screenshot;
- presentasi maksimal tujuh menit.

---

## 47. Riwayat Perubahan PRD

| Versi | Tanggal | Perubahan | Alasan |
|---|---|---|---|
| 1.0 | 2026-07-24 | Dokumen awal | Inisialisasi mini project |
| 1.1 | 2026-07-24 | Mempertahankan fitur autentikasi dan akun Breeze | Menjaga fitur pendukung yang telah tersedia |
| 1.2 | 2026-07-25 | Menyelaraskan seluruh konsep domain, database, workflow, testing, dan dokumentasi dengan modul latihan | Modul Mini Project Junior Developer KPK menjadi acuan utama pelatihan |

Setiap perubahan requirement wajib dicatat sebelum implementasi.
