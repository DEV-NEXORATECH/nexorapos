# Retail POS Full UI/UX + Functional Audit

Tanggal audit: 2026-08-12

## Menu Saat Ini

### Super Admin

| Menu | Fungsi | Status |
| --- | --- | --- |
| Dashboard | KPI SaaS: company, license, user, enabled feature | PASS |
| Company Features | Kelola company license, billing, due date, max user, feature toggle | PASS |
| Users | Kelola user lintas company dengan guard privilege | PASS |
| App Settings | Setting global aplikasi | PARTIAL |

Super Admin tidak menampilkan Business Process, License Status tenant, dan menu operasional POS agar tidak rancu dengan user company.

### Admin Company / Employee

| Menu | Fungsi | Status |
| --- | --- | --- |
| Dashboard | KPI operasional company + license card | PASS |
| Business Process | Panduan flow dinamis sesuai fitur aktif company | PASS |
| License Status | Lihat paket, status pembayaran, due date, grace, fitur aktif, limit user | PASS |
| Company Settings | Setting nama company, legal name, NPWP, kontak, alamat, logo | PASS |
| Users | CRUD user tenant sesuai permission | PASS |
| Roles & Access | CRUD role + permission checklist | PASS |
| Stores | Master outlet/toko | PASS |
| Categories | Master kategori produk | PASS |
| Products | Master produk, kategori, tax, harga, stok | PASS |
| Product Variants | Master varian produk | PASS |
| Tax | Master pajak | PASS |
| Suppliers | Master supplier | PASS |
| Customers | Master customer | PASS |
| Discounts | Master diskon | PASS |
| Payment Methods | Master metode pembayaran | PASS |
| POS Sell | Checkout kasir, hitung server-side, payment, stock movement, receipt | PASS |
| Sales | Daftar transaksi sale | PASS |
| Sales Reconciliation | Cocokkan total sales dan payment per periode | PARTIAL |
| Purchases | Transaksi pembelian | PARTIAL |
| Payments | Payment record, void bukan hard delete | PASS |

`Sale Items` dan `Purchase Items` tetap ada sebagai route teknis, tetapi tidak menjadi primary sidebar menu karena seharusnya menjadi detail dari Sales/Purchases.

## Flow Utama

1. Super Admin login.
2. Super Admin buka Company Features.
3. Super Admin set license, payment status, due date, grace period, max users.
4. Super Admin enable fitur company.
5. Super Admin/Admin buat user admin company.
6. Admin company login dengan company code.
7. Admin buka Company Settings untuk logo/nama/PT/NPWP.
8. Admin buat role lewat Roles & Access dan checklist permission.
9. Admin buat employee.
10. Employee login.
11. Employee hanya melihat menu sesuai `Company Feature + Role Permission + License`.
12. Kasir buka POS Sell.
13. Kasir pilih product/variant, qty, customer, discount, tax, payment method.
14. Backend menghitung subtotal/tax/discount/grand total.
15. Sale, sale items, payment, stock movement dibuat.
16. Receipt/invoice bisa dibuka.
17. Jika license expired/suspended/inactive, operasional terkunci 423.

## Matrix

| Menu | Exists | UI | Create | Read | Update | Delete | Permission | Feature | License | Tenant | Status |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| Company Features | Yes | PASS | N/A | PASS | PASS | Suspend/Activate | Super Admin | N/A | PASS | Platform | PASS |
| Users | Yes | PASS | PASS | PASS | PASS | PASS | PASS | N/A | PASS | PASS | PASS |
| Roles & Access | Yes | PASS | PASS | PASS | PASS | RESTRICT if used | PASS | PASS | PASS | PASS | PASS |
| Company Settings | Yes | PASS | N/A | PASS | PASS | N/A | PASS | N/A | PASS | PASS | PASS |
| License Status | Yes | PASS | N/A | PASS | N/A | N/A | Read-only tenant | PASS | PASS | PASS | PASS |
| POS Master | Yes | PARTIAL | PASS | PASS | PASS | RESTRICT/soft where needed | PASS | PASS | PASS | PASS | PARTIAL |
| POS Sell | Yes | PASS | PASS | PASS | N/A | Cancel/Void | PASS | PASS | PASS | PASS | PASS |
| Sales | Yes | PASS | PASS | PASS | PASS | Cancel, no hard delete | PASS | PASS | PASS | PASS | PASS |
| Purchases | Yes | PARTIAL | PASS | PASS | PASS | Cancel, no hard delete | PASS | PASS | PASS | PASS | PARTIAL |
| Payments | Yes | PASS | PASS | PASS | PASS | Void, no hard delete | PASS | PASS | PASS | PASS | PASS |
| Sales Reconciliation | Yes | PARTIAL | N/A | PARTIAL | N/A | N/A | PASS | PASS | PASS | PASS | PARTIAL |

## Field/Relation Audit

| Module | Field | DB Field | API Field | Create | Edit | Validation | Relation | PASS/FAIL |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| User | Phone | phone | phone | Yes | Yes | Yes | N/A | PASS |
| User | Password | password | password | Required | Optional | Yes | N/A | PASS |
| Product | Tax | tax_id | tax_id | Yes | Yes | Yes | Scoped model | PASS |
| Product | Category | category_id | category_id | Yes | Yes | Yes | Scoped model | PASS |
| Purchase | Supplier | supplier_id | supplier_id | Yes | Yes | Yes | Scoped model | PASS |
| Purchase Item | Purchase | purchase_id | purchase_id | Yes | Yes | Yes | Scoped model | PASS |
| Purchase Item | Product | product_id | product_id | Yes | Yes | Yes | Scoped model | PASS |
| Payment | Sale | sale_id | sale_id | Yes | Yes | Yes | Scoped model | PASS |
| Payment | Method | method_id | method_id | Yes | Yes | Yes | Scoped model | PASS |
| Stock | Product Variant | product_variant_id | product_variant_id | Optional | Optional | Yes | Must belong to product | PASS |
| Sale | Product Variant | variant_id | variant_id | Optional | N/A | Yes | Must belong to product | PASS |

## UAT Checklist

| Test | Steps | Expected | Status |
| --- | --- | --- | --- |
| TEST 01 | Login Super Admin | Masuk dashboard platform | READY |
| TEST 02 | Buka Company Features | Hanya Super Admin bisa akses | READY |
| TEST 03 | Set License | Status/due/grace/max user tersimpan | READY |
| TEST 04 | Enable Features | Menu tenant berubah sesuai fitur | READY |
| TEST 05 | Create Admin | User company dibuat tanpa super admin escalation | READY |
| TEST 06 | Login Admin | Masuk tenant dengan company code | READY |
| TEST 07 | Create Role | Role + permission checklist tersimpan | READY |
| TEST 08 | Configure Access | Permission tidak melebihi admin/company feature | READY |
| TEST 09 | Create Employee | Employee masuk company sendiri | READY |
| TEST 10 | Login Employee | Menu sesuai role | READY |
| TEST 11 | Create Product | Product relasi category/tax valid | READY |
| TEST 12 | Create Supplier | Supplier tenant tersimpan | READY |
| TEST 13 | Purchase | Purchase tersimpan dan cancel bukan hard delete | READY |
| TEST 14 | POS Sale | Sale/payment/item/stock dibuat | READY |
| TEST 15 | Payment | Payment bisa void, bukan hard delete | READY |
| TEST 16 | Stock Validation | Stock insufficient ditolak | READY |
| TEST 17 | License Grace | Banner/popup warning, akses masih jalan | READY |
| TEST 18 | License Expired | 423 lock page | READY |
| TEST 19 | Permission Denied | URL/button unauthorized ditolak | READY |
| TEST 20 | Cross Tenant Test | Data tenant lain tidak bisa diedit | READY |

## Remaining Risk

| Area | Risiko | Rekomendasi |
| --- | --- | --- |
| Sales Reconciliation | Sudah ada halaman, tetapi audit bisnis period/store/difference perlu UAT manual lebih dalam | P1 polish setelah UAT awal |
| Purchase Items / Sale Items | Route teknis masih ada untuk kompatibilitas | Pindahkan penuh ke detail page Sales/Purchases |
| Global Search | Dihapus dari navbar karena belum functional | Implement setelah data/menu stabil |
| App Settings | Masih mengikuti schema setting existing | Perjelas field global yang benar-benar dibutuhkan |
