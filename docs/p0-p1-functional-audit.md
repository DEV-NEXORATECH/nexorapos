# P0/P1 Functional Audit - Retail Point Of sale

Tanggal audit: 2026-08-12

## Scope

Audit ini mengecek jalur:

Database -> Model -> Relation -> Validation -> Service -> Controller/API -> Permission -> Company Scope -> License -> Form Create -> List -> Detail/Edit -> Delete

## CRUD Matrix

| Module | Create | Read | Update | Delete | Tenant | Permission | License | Relation | Validation | UI | Status |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| Users | Ada | Ada | Ada | Ada | Company-scoped service | RBAC route | 423 lock | Role, Company | Store/Update validation | Add/List/Edit | PASS with new privilege tests |
| Roles | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Permissions, Users | Store/Update validation | Add/List/Edit | PASS, patched feature cap |
| Stores | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Sales, StockMovement | Service validation | Add/List/Edit | PASS code audit, delete restricted by FK |
| Categories | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Products | Service validation | Add/List/Edit | PASS code audit, product FK set null |
| Products | Ada | Ada | Ada | Soft delete/force | BelongsToCompany | RBAC route | 423 lock | Category, Tax, Variants, Sales | Service validation | Add/List/Edit/Archive | PASS code audit, transaction FK restrict |
| Product Variants | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Product, StockMovement | Service validation | Add/List/Edit | PASS code audit, transaction FK restrict |
| Taxes | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Products | Service validation | Add/List/Edit | PASS code audit, product tax nullable |
| Suppliers | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Purchases | Service validation | Add/List/Edit | PASS code audit, purchase FK restrict |
| Customers | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Sales | Service validation | Add/List/Edit | PASS code audit, sale FK null on delete |
| Discounts | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Sales feature group | Service validation | Add/List/Edit | PASS code audit |
| Payment Methods | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Payments | Service validation | Add/List/Edit | PASS code audit, payment FK restrict |
| Sales | Ada/POS | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Store, Customer, User, Items, Payments | POS service server totals | POS/List/Edit/Invoice | PASS with POS tests |
| Sale Items | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Sale, Product, Variant | Service validation | Add/List/Edit | PASS code audit |
| Purchases | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Supplier, Items | Service validation | Add/List/Edit | PASS code audit, supplier FK restrict |
| Purchase Items | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Purchase, Product | Service validation | Add/List/Edit | PASS code audit, product FK restrict |
| Payments | Ada | Ada | Ada | Ada | BelongsToCompany | RBAC route | 423 lock | Sale, Method | Service validation | Add/List/Edit | PASS code audit, method FK restrict |
| Reconciliation | Read/report | Ada | N/A | N/A | Sale scope | RBAC route | 423 lock | Sales | Controller report | Separate page | PASS code audit |
| Company Settings | Ada/update | Ada | Ada | N/A | Own company only | Navbar/link | Locked when expired | Company | Controller validation | Company page | PASS code audit |
| License Status | Super Admin control / Tenant read | Ada | Super Admin only | N/A | Company specific | Super Admin guard | Whitelisted | Features, CompanyFeature | Controller validation | Features/License pages | PASS with license tests |
| Settings | Ada | Ada | Ada | Ada | Patched BelongsToCompany | RBAC route | 423 lock | Company | Store/Update validation | Settings pages | PASS with new settings test |

## Field Mapping Summary

| Module | Field UI | Request Field | Validation | DB Column | Relation | Create | Edit | Required | Status |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| Users | Name | name | required | users.name | - | Yes | Yes | Yes | PASS |
| Users | Email | email | required unique | users.email | - | Yes | Yes | Yes | PASS |
| Users | Phone | phone | required | users.phone | - | Yes | Existing update does not edit phone | Yes on create | RISK: edit phone parity |
| Users | Role | role_id | exists roles | users.role_id | Role | Yes | Yes | Yes | PASS |
| Users | Company | company_id | super admin required | users.company_id | Company | Super Admin only | Not editable by company admin | Required for Super Admin | PASS |
| Users | Super Admin | is_super_admin | boolean | users.is_super_admin | - | Super Admin only | Company admin blocked | No | PASS patched/tested |
| Roles | Role | role | required | roles.role | - | Yes | Yes | Yes | PASS |
| Roles | Permissions | permissions[] | capped in service | role_permissions.permission_id | Permission | Via edit | Via edit | No | PASS patched/tested |
| Stores | Name | name | service validation | stores.name | Sales/Stock | Yes | Yes | Yes | PASS code audit |
| Stores | Code | code | service validation | stores.code | Sales/Stock | Yes | Yes | Yes | PASS code audit |
| Categories | Name | name | service validation | product_categories.name | Product | Yes | Yes | Yes | PASS code audit |
| Products | Name | name | service validation | products.name | - | Yes | Yes | Yes | PASS code audit |
| Products | SKU | sku | service validation | products.sku | - | Yes | Yes | Varies | PASS code audit |
| Products | Barcode | barcode | service validation | products.barcode | - | Yes | Yes | Varies | PASS code audit |
| Products | Category | category_id | service validation | products.category_id | ProductCategory | Yes | Yes | No | PASS code audit |
| Products | Tax | tax_id | service validation | products.tax_id | Tax | Yes | Yes | No | PASS code audit |
| Products | Price | price | service validation | products.price | SaleItem | Yes | Yes | Yes | PASS code audit |
| Products | Cost | cost | service validation | products.cost | Purchase/stock | Yes | Yes | No | PASS code audit |
| Products | Track Stock | track_stock | service validation | products.track_stock | Stock | Yes | Yes | No | PASS code audit |
| Variants | Product | product_id | service validation | product_variants.product_id | Product | Yes | Yes | Yes | PASS code audit |
| Variants | Name | name | service validation | product_variants.name | - | Yes | Yes | Yes | PASS code audit |
| Taxes | Name | name | service validation | taxes.name | Product | Yes | Yes | Yes | PASS code audit |
| Taxes | Rate | rate | service validation | taxes.rate | Product/SaleItem | Yes | Yes | Yes | PASS code audit |
| Suppliers | Name | name | service validation | suppliers.name | Purchase | Yes | Yes | Yes | PASS code audit |
| Customers | Name | name | service validation | customers.name | Sale | Yes | Yes | Yes | PASS code audit |
| Discounts | Name/Type/Value | name/type/value | service validation | discounts.* | Sale feature | Yes | Yes | Yes | PASS code audit |
| Payment Methods | Name | name | service validation | methods.name | Payment | Yes | Yes | Yes | PASS code audit |
| Sales POS | Store | store_id | checkout validation | sales.store_id | Store | Yes | N/A | Yes | PASS tested |
| Sales POS | Customer | customer_id | checkout validation | sales.customer_id | Customer | Yes | N/A | No | PASS tested |
| Sales POS | Method | method_id | checkout validation | payments.method_id | Method | Yes | N/A | Yes | PASS tested |
| Sales POS | Product | product_id | cart validation | sale_items.product_id | Product | Yes | N/A | Yes | PASS tested |
| Sales POS | Quantity | quantity | cart validation | sale_items.quantity | Product stock | Yes | N/A | Yes | PASS tested |
| Settings | Key | key | setting validation | settings.key | Company | Yes | Yes | Yes | PASS patched/tested |
| Settings | Value | value | setting validation | settings.value | Company | Yes | Yes | Yes | PASS patched/tested |

## Cascading Relation Audit

| Relation | Current Guard | Status |
| --- | --- | --- |
| Company -> Store | BelongsToCompany + company_id FK | PASS |
| Category -> Product | products.category_id nullable/set null | PASS |
| Product -> Variant | variant scoped + product relation | PASS |
| Supplier -> Purchase | purchase.supplier_id restrict on delete | PASS |
| Customer -> Sale | sale.customer_id null on delete | ACCEPTED |
| Role -> Permission | service sync + admin permission cap | PASS |
| Company Feature -> Permission | AdminMiddleware route guard + RoleService feature cap | PASS patched/tested |

## Delete Behavior Audit

| Entity | Current Behavior | Transaction Guard | Status |
| --- | --- | --- | --- |
| Product | Soft delete, force delete available | sale_items/purchase_items/stock_movements restrict | PASS, force delete must stay restricted by DB |
| Supplier | Hard delete | purchases.supplier_id restrict | PASS |
| Category | Hard delete | products.category_id set null | ACCEPTED |
| Role | Hard delete blocked when users exist | service guard | PASS |
| Payment Method | Hard delete | payments.method_id restrict | PASS |
| Store | Hard delete | sales/stock_movements restrict | PASS |
| Customer | Hard delete | sales.customer_id set null | ACCEPTED |
| Sale | Hard delete | sale_items/payments cascade by base migration | RISK: business may prefer cancel/archive instead of hard delete |
| Purchase | Hard delete | purchase_items cascade by base migration | RISK: business may prefer cancel/archive instead of hard delete |

## Bugs Found And Fixed In This Pass

1. `settings` was not tenant-aware.
   - Added `settings.company_id` migration.
   - Added `BelongsToCompany` to `Setting`.
   - Split setting cache by company.
   - Added settings tenant tests.

2. `UserService` swallowed `abort(403)` inside broad `catch`.
   - Re-throws `HttpExceptionInterface`.
   - Added privilege/cross-tenant tests.

3. License middleware order was after permission/feature middleware.
   - Changed route group order to auth -> license -> admin permission -> XSS.
   - Added real route expired lock test expecting 423.

4. Admin could grant permission from a disabled feature if the admin role still had that permission.
   - RoleService now rejects requested permissions whose feature is disabled.
   - Added RolePermissionTest.

5. Runtime profile uploads were tracked/deleted noisily.
   - Added `.gitignore` rule for `/public/admin/profile_images/*`.
   - Added `.gitkeep`.

## Remaining Risks

| Risk | Impact | Recommendation |
| --- | --- | --- |
| Sale/Purchase hard delete still exists | Transaction history can be removed if route allowed and FK cascade applies | P1: convert to cancel/archive workflow |
| User edit form may not expose phone while create requires phone | Create/Edit parity gap | P1: add phone to edit form/update validation |
| Some POS services still rely on service-level validation instead of FormRequest classes | Harder to audit uniformly | P1: normalize validation layer |
| Cascading dropdown reset is mostly UI-side and not fully automated | User can submit stale relation IDs | P1: add backend relation-company validation for every FK pair |
| Deleted `public/admin/profile_images/*` may still be referenced by existing remote DB rows | Broken old avatars | Decide before commit: restore old tracked images or migrate to placeholder/user-upload storage |
| Deleted vendor `public/admin/*.html` no longer referenced by app | Low risk | Keep deleted if no vendor demo needed |

## Verification

- `php artisan migrate:fresh --seed` on SQLite in-memory: PASS
- Targeted P0/P1 tests: PASS after patches
- Full test suite should be run after every further patch.
