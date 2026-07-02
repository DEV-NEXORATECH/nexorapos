# Flowchart Sistem Point Of Sale

Dokumen ini menjelaskan alur utama aplikasi Point Of Sale berbasis Laravel. File konfigurasi lokal ada di `.env`, dan dump database yang disediakan bernama `pos.sql` dengan database `pos`.

## 1. Setup Lokal

```mermaid
flowchart TD
    A[Mulai setup project] --> B[Install dependency Composer dan NPM]
    B --> C[Buat database MySQL bernama pos]
    C --> D[Import file pos.sql]
    D --> E[Cek konfigurasi .env]
    E --> F[Jalankan php artisan serve]
    F --> G[Buka http://127.0.0.1:8000]
```

Catatan `.env`:

- `DB_CONNECTION=mysql`
- `DB_DATABASE=pos`
- `DB_USERNAME=root`
- `DB_PASSWORD=` kosong untuk setup XAMPP default
- Jika password MySQL Anda berbeda, ubah `DB_PASSWORD`.

## 2. Alur Login dan Akses Admin

```mermaid
flowchart TD
    A[User membuka aplikasi] --> B[Halaman login]
    B --> C{Credential valid?}
    C -- Tidak --> D[Kembali ke login dengan error]
    C -- Ya --> E[Masuk ke route admin]
    E --> F[Middleware auth]
    F --> G{Sudah login?}
    G -- Tidak --> B
    G -- Ya --> H[Middleware Admin]
    H --> I{Route ada di permission role?}
    I -- Tidak --> J[Kembali dengan pesan tidak punya akses]
    I -- Ya --> K[Middleware XSS membersihkan input]
    K --> L[Controller menjalankan service]
    L --> M[Tampilkan halaman admin]
```

File terkait:

- `routes/web.php`
- `app/Http/Middleware/AdminMiddleware.php`
- `app/Http/Middleware/XssMiddleware.php`
- `app/Http/Controllers/Admin/AdminController.php`

## 3. Alur Dashboard

```mermaid
flowchart TD
    A[Admin membuka dashboard] --> B[AdminController adminPanel]
    B --> C[AdminService adminPanel]
    C --> D[Hitung jumlah user, store, kategori, produk, variant]
    C --> E[Hitung supplier, customer, pajak, purchase, sale]
    C --> F[Hitung total pembayaran dari sale completed]
    D --> G[Tampilkan admin.dashboard]
    E --> G
    F --> G
```

Data grafik penjualan:

```mermaid
flowchart TD
    A[Frontend request sales-data] --> B[AdminController getSalesData]
    B --> C[AdminService getSalesData]
    C --> D{Filter}
    D -- daily --> E[Group total sale per jam]
    D -- weekly --> F[Group total sale per hari]
    D -- monthly --> G[Group total sale per bulan]
    E --> H[Return JSON]
    F --> H
    G --> H
```

## 4. Alur Master Data

Master data memakai pola yang mirip: route masuk ke controller, controller memanggil service, service membaca atau menyimpan model, lalu kembali ke view.

```mermaid
flowchart TD
    A[Admin pilih menu master data] --> B{Menu}
    B --> C[Users]
    B --> D[Roles dan Permissions]
    B --> E[Settings]
    B --> F[Stores]
    B --> G[Categories]
    B --> H[Products dan Variants]
    B --> I[Suppliers dan Customers]
    B --> J[Taxes dan Discounts]
    C --> K[Controller]
    D --> K
    E --> K
    F --> K
    G --> K
    H --> K
    I --> K
    J --> K
    K --> L[Service]
    L --> M[Model dan Database]
    M --> N[View listing, add, update]
```

Operasi umum:

- Listing data: `GET admin-panel/.../listing`
- Form tambah: `GET admin-panel/.../add`
- Simpan data: `POST admin-panel/.../add`
- Form edit: `GET admin-panel/.../edit/{id}`
- Update data: `POST admin-panel/.../update/{id}`
- Hapus data: `GET admin-panel/.../delete/{id}`

## 5. Alur Transaksi POS

```mermaid
flowchart TD
    A[Kasir buka admin-panel/pos/sell] --> B[SaleController pos]
    B --> C[SaleService pos]
    C --> D[Ambil kategori, produk, customer, store]
    D --> E[Tampilkan halaman POS]
    E --> F[Kasir pilih produk]
    F --> G[POST pos/cart/add]
    G --> H[Simpan item ke session pos_cart]
    H --> I[Hitung subtotal, diskon, pajak, total]
    I --> J[Tampilkan cart terbaru]
    J --> K{Checkout?}
    K -- Belum --> F
    K -- Ya --> L[POST pos/checkout]
    L --> M{Cart kosong?}
    M -- Ya --> N[Return error Cart is empty]
    M -- Tidak --> O[Buat record sales]
    O --> P[Buat record sale_items]
    P --> Q[Hapus session pos_cart]
    Q --> R[Redirect ke invoice]
    R --> S[User melihat invoice atau download PDF]
```

File terkait:

- `app/Http/Controllers/POS/SaleController.php`
- `app/Services/Pos/SaleService.php`
- `resources/views/admin/pos/sale/pos.blade.php`
- `resources/views/admin/pos/sale/invoice.blade.php`
- `resources/views/admin/pos/sale/invoice_pdf.blade.php`

## 6. Relasi Data Utama

```mermaid
erDiagram
    USERS ||--o{ SALES : creates
    STORES ||--o{ SALES : has
    CUSTOMERS ||--o{ SALES : makes
    SALES ||--o{ SALE_ITEMS : contains
    PRODUCTS ||--o{ SALE_ITEMS : sold_as
    PRODUCT_VARIANTS ||--o{ SALE_ITEMS : optional_variant
    PRODUCT_CATEGORIES ||--o{ PRODUCTS : groups
    TAXES ||--o{ PRODUCTS : applied_to
    SUPPLIERS ||--o{ PURCHASES : supplies
    PURCHASES ||--o{ PURCHASE_ITEMS : contains
    PRODUCTS ||--o{ PURCHASE_ITEMS : purchased_as
    PRODUCTS ||--o{ STOCK_MOVEMENTS : tracked_by
    STORES ||--o{ STOCK_MOVEMENTS : located_at
    SALES ||--o{ PAYMENTS : paid_by
    METHODS ||--o{ PAYMENTS : payment_method
```

## 7. Ringkasan Modul

| Modul | Fungsi |
| --- | --- |
| Auth | Login, register, reset password, profile |
| Admin Dashboard | Statistik data dan grafik penjualan |
| Role & Permission | Membatasi akses route berdasarkan role user |
| Product | Kelola produk, kategori, variant, tax, import, export, recycle |
| Store | Kelola toko atau cabang |
| Supplier & Purchase | Kelola pemasok dan pembelian barang |
| Customer | Kelola pelanggan |
| Stock | Kelola pergerakan stok |
| Discount | Kelola diskon |
| POS Sale | Penjualan kasir, cart session, checkout, invoice, PDF |
| Payment | Kelola metode dan pembayaran |

## 8. Urutan Menjalankan Project

```bash
composer install
npm install
php artisan key:generate
php artisan config:clear
php artisan cache:clear
php artisan serve
```

Jika menggunakan file `.env` yang sudah dibuat di project ini, `php artisan key:generate` tidak wajib karena `APP_KEY` sudah terisi. Tetap boleh dijalankan jika ingin membuat key baru.
