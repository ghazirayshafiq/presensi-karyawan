# Employee Service API Documentation

Dokumentasi ini berisi endpoint API yang tersedia pada Employee Service.

## Menjalankan Server

1. Buka terminal di folder `Employee`.
2. Jalankan perintah berikut:

```bash
php artisan serve
```

3. Secara default service berjalan di:

```text
http://127.0.0.1:8000
```

Base path API:

```text
/api
```

## Standar Format Response

Semua endpoint mengembalikan format JSON yang seragam:

```json
{
  "status": "success|error",
  "message": "informasi hasil request",
  "data": {}
}
```

## Daftar Endpoint

### 1) Login

- Method: `POST`
- URL Path: `/api/login`
- Fungsi: Login user (admin/karyawan) dan menghasilkan JWT token.
- Payload/Body:

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

- Middleware: Public (tanpa login)

Contoh data response (`data`):

```json
{
  "access_token": "<jwt_token>",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### 2) Verifikasi Data Karyawan (Untuk Service Lain)

- Method: `GET`
- URL Path: `/api/employees/verify/{id}`
- Fungsi: Mengambil data minimal karyawan untuk kebutuhan validasi oleh Attendance Service.
- Payload/Body: Tidak ada
- Middleware: Public (tanpa login)

Contoh response `data`:

```json
{
  "id": 10,
  "name": "Budi Santoso",
  "role": "karyawan",
  "department": "IT"
}
```

### 3) List Data Karyawan

- Method: `GET`
- URL Path: `/api/employees`
- Fungsi:
  - Admin: melihat semua data karyawan.
  - Karyawan: hanya melihat data dirinya sendiri.
  - Mendukung filter department.
- Payload/Body: Tidak ada
- Query Parameter (opsional): `department`
  - Contoh: `/api/employees?department=IT`
- Middleware: `auth:api` (wajib login JWT)

### 4) Detail Satu Karyawan

- Method: `GET`
- URL Path: `/api/employees/{employee}`
- Fungsi:
  - Admin: melihat detail semua karyawan.
  - Karyawan: hanya melihat detail miliknya sendiri.
- Payload/Body: Tidak ada
- Middleware: `auth:api` (wajib login JWT)

### 5) Tambah Karyawan

- Method: `POST`
- URL Path: `/api/employees`
- Fungsi: Menambahkan data karyawan baru.
- Payload/Body:

```json
{
  "name": "Andi Wijaya",
  "email": "andi@example.com",
  "password": "password123",
  "role": "karyawan",
  "department": "Operations"
}
```

- Middleware:
  - `auth:api` (wajib login JWT)
  - `admin` (hanya admin)

Validasi utama:
- `email` wajib unik
- `password` minimal 8 karakter
- `role` hanya `admin` atau `karyawan`

### 6) Update Karyawan

- Method: `PUT`
- URL Path: `/api/employees/{employee}`
- Fungsi: Memperbarui data karyawan.
- Payload/Body (kirim field yang ingin diubah):

```json
{
  "name": "Andi Update",
  "email": "andi.update@example.com",
  "password": "password123",
  "role": "admin",
  "department": "Finance"
}
```

- Middleware:
  - `auth:api` (wajib login JWT)
  - `admin` (hanya admin)

Validasi utama:
- `email` harus unik (kecuali milik data yang sedang diupdate)
- jika `password` dikirim, minimal 8 karakter

### 7) Hapus Karyawan

- Method: `DELETE`
- URL Path: `/api/employees/{employee}`
- Fungsi: Menghapus data karyawan.
- Payload/Body: Tidak ada
- Middleware:
  - `auth:api` (wajib login JWT)
  - `admin` (hanya admin)

## Catatan Penggunaan Token JWT

Untuk endpoint yang membutuhkan login, kirim header:

```text
Authorization: Bearer <jwt_token>
```
