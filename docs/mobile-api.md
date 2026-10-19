# Retail Point Of sale Mobile API

Base URL lokal:

```text
http://127.0.0.1:8000/api/mobile/v1
```

Header standar setelah login:

```text
Authorization: Bearer {access_token}
Accept: application/json
Content-Type: application/json
Idempotency-Key: optional-unique-key-for-checkout
```

## Akun dan Flow

Super Admin login tanpa company code. Admin company dan kasir wajib login dengan `company_code`.

```json
POST /auth/login
{
  "email": "superadmin@example.test",
  "password": "password"
}
```

```json
POST /auth/login
{
  "email": "admin@example.test",
  "password": "password",
  "company_code": "NEXORA001",
  "device_name": "Android POS 1"
}
```

Flow Super Admin:

1. Login tanpa company code.
2. Create company di `/admin/companies`.
3. Sistem generate company code dari nama company dan nomor unik.
4. Set license, due date, billing status, limit user, dan fitur aktif.
5. Buat atau assign Admin Company untuk company tersebut.

Flow Admin Company:

1. Login pakai company code.
2. Lihat status license dan fitur.
3. Buat store, role, permission, user kasir.
4. Assign kasir ke store dan shift.
5. Kelola master data POS.

Flow Kasir:

1. Login pakai company code.
2. Buka shift.
3. Ambil catalog produk/customer/payment method.
4. Checkout dengan idempotency key.
5. Save held bill, restore, merge, atau split preview.
6. Tutup shift dan lihat report shift.

## Auth dan Token

| Method | Endpoint | Fungsi |
|---|---|---|
| POST | `/auth/login` | Login dan membuat access token + refresh token |
| POST | `/auth/refresh` | Refresh access token |
| GET | `/auth/me` | Profile aktif + license snapshot |
| POST | `/auth/logout` | Revoke token aktif |
| GET | `/auth/devices` | List device/token login user |
| DELETE | `/auth/devices/{id}` | Revoke device tertentu |

Access token berlaku default 30 hari, refresh token 60 hari. Policy ini bisa dinaikkan nanti ke setting admin kalau perlu UI expiry per company.

## Permission Helper

```text
GET /admin/mobile-menu?company_id={id}
```

Response ini dipakai mobile untuk render menu:

```json
{
  "success": true,
  "data": {
    "menus": [
      {
        "name": "Products",
        "feature_key": "products",
        "can_view": true,
        "can_create": true,
        "can_update": true,
        "can_delete": false,
        "routes": ["admin-panel/products/listing", "admin-panel/products/add"]
      }
    ]
  }
}
```

Aturan akses:

```text
Employee Permission <= Admin Permission <= Company Feature
```

Fitur yang mati tidak boleh tampil di menu mobile dan endpoint harus ditolak.

## Upload API

Gunakan `multipart/form-data`.

| Method | Endpoint | Body |
|---|---|---|
| POST | `/admin/uploads/company-logo` | `company_id`, `logo` |
| POST | `/admin/uploads/users/{id}/profile-image` | `company_id`, `image` |
| POST | `/admin/uploads/products/{id}/image` | `company_id`, `image` |

Untuk non Super Admin, `company_id` diabaikan dan selalu memakai company user login.

## POS API

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/stores` | List store company |
| GET | `/categories` | List kategori |
| GET | `/products` | List produk |
| GET | `/customers` | List customer |
| POST | `/customers` | Create customer cepat dari POS |
| GET | `/payment-methods` | List metode pembayaran |
| POST | `/pos/shifts/open` | Buka shift kasir |
| POST | `/pos/shifts/close` | Tutup shift |
| GET | `/pos/shifts/report` | Report shift |
| POST | `/pos/checkout` | Checkout transaksi |
| POST | `/pos/sales/{id}/void` | Void/refund approval sederhana |
| GET | `/pos/held-bills` | List held bill |
| POST | `/pos/held-bills` | Save bill/table bill |
| POST | `/pos/held-bills/merge` | Merge beberapa held bill |
| POST | `/pos/held-bills/{id}/restore` | Restore held bill |
| DELETE | `/pos/held-bills/{id}` | Hapus held bill |
| POST | `/pos/split-preview` | Hitung validasi split bill |

Checkout wajib pakai idempotency untuk mobile supaya double tap tidak membuat transaksi dobel:

```json
POST /pos/checkout
{
  "idempotency_key": "android-pos-001-20260817-0001",
  "store_id": 1,
  "items": [
    { "product_id": 1, "quantity": 2, "discount": 0 }
  ],
  "payments": [
    { "method_id": 1, "amount": 22000 }
  ]
}
```

Custom amount:

```json
{
  "is_custom_amount": true,
  "name": "Biaya tambahan",
  "price": 5000,
  "quantity": 1
}
```

## Report dan Audit

| Method | Endpoint | Format |
|---|---|---|
| GET | `/admin/audit-logs` | JSON |
| GET | `/admin/reports/sales/csv` | CSV |
| GET | `/admin/reports/sales/xlsx` | Excel-compatible CSV |
| GET | `/admin/reports/sales/pdf` | PDF |
| GET | `/admin/reports/shifts/{format}` | CSV/XLSX/PDF |
| GET | `/admin/reports/reconciliation/{format}` | CSV/XLSX/PDF |

Filter umum:

```text
?date=2026-08-17&company_id=1
```

## Error Code

| HTTP | Arti |
|---|---|
| 200 | Request berhasil |
| 201 | Data berhasil dibuat |
| 401 | Token/credential invalid |
| 403 | Tidak punya permission atau fitur mati |
| 422 | Validasi gagal atau relasi tidak valid |
| 423 | License locked/expired |
| 429 | Rate limit kena |
| 500 | Server error tidak terduga |
| 503 | Migration/table/column belum tersedia |

Response error standar:

```json
{
  "success": false,
  "message": "Store tidak valid untuk company ini.",
  "data": null
}
```

## Perubahan auth/navigation mobile — 19 Oktober 2026

MobileApiAuth kini menjalankan MobileApiAuthorize setelah validasi Bearer. MobileAccess memetakan route permission yang sudah ada menjadi capability mobile. /auth/me mengirim user.permissions, active_shift (dengan store), require_active_shift, role.id/name, dan is_super_admin. /admin/mobile-menu mengirim permissions selain menu/action flags; endpoint ini tersedia untuk semua role terautentikasi dan mendukung sesi global Super Admin sebelum pemilihan company.

Tindakan API langsung diperiksa berdasarkan permission. Non-super-admin tidak dapat mengganti company_id atau mengakses store di luar company/assignment. Checkout-only dapat membaca invoice sendiri, sedangkan riwayat/invoice kasir lain membutuhkan permission penjualan. Route web pada payload hanya identifier permission dan tidak boleh digunakan sebagai endpoint mobile. Role IDs tetap berasal dari database per company, bukan angka tetap.

Perubahan ini tidak menjalankan migration/seeder dan tidak mengganti perhitungan transaksi. Deploy source backend sebelum memakai mobile fase 2. Tes menggunakan SQLite :memory:: MobileApiTest, RolePermissionTest, PosSaleCheckoutTest, PosStockAdjustmentTest — 25 tes, 137 assertion lulus. UAT empat akun production/perangkat belum dijalankan. Tidak ada password-reset REST endpoint baru pada fase ini.

## Fase 3 POS — 19 Oktober 2026

Katalog mobile mendukung gambar, variants/harga varian, pencarian varian, dan pagination. /auth/me mengumumkan capabilities checkout_idempotency (hanya bila unique index company_id/idempotency_key tersedia), checkout_atomic_rejection, qris_recovery, serta held_merge. Capability penolakan atomik memungkinkan mobile membuka kembali keranjang setelah penolakan 422 yang pasti, dengan harga/stok terbaru; timeout tetap terkunci.

Checkout memvalidasi nominal tunai/non-tunai/split dan permission diskon. discount pada request checkout tetap nominal per unit; mobile membagi diskon total baris dengan quantity. Pajak per baris dibulatkan ke Rupiah utuh. QRIS dicatat pending dan tidak melunasi sale sebelum webhook yang valid. Pembuatan QRIS mereservasi satu order per sale dan mengambil token/order yang sama saat replay; timeout gateway tidak memicu order baru. Rekonsiliasi admin diperlukan jika order belum memiliki token. Webhook memeriksa signature/nominal, menjaga status paid tetap final, serta tidak mengaktifkan kembali invoice void.

Restore held bill mengembalikan metadata lengkap dan replayable oleh pemilik. Merge mengunci bill dan memerlukan toko/customer sama. Laporan shift pribadi mengembalikan summary live. Expected cash hanya memasukkan cash/tunai aktif net kembalian, mengecualikan QRIS; close menolak invoice pending. Payload close adalah closing_cash/note. Split preview memakai split_total/is_balanced. Void mengunci sale sebelum pengembalian stok.

Verifikasi lokal: 31 tes backend, 184 assertion, dengan SQLite :memory: dan gateway Midtrans palsu. Tidak ada migrasi/seeder atau pembayaran production yang dijalankan. Deploy perubahan backend sebelum mobile fase 3; lakukan UAT perangkat dan Midtrans sandbox menggunakan akun resmi. Printer dan endpoint kas masuk/keluar manual belum ditambahkan.
