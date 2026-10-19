<?php

namespace App\Services;

use App\Models\{Company, CompanyPayment, User, MobileApiToken, AuditLog};
use App\Models\Pos\{Sale, Product, Customer, Store};
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileDashboard
{
    public function range(Request $request): array
    {
        $data = $request->validate(['from' => ['nullable', 'date_format:Y-m-d'], 'to' => ['nullable', 'date_format:Y-m-d'], 'store_id' => ['nullable', 'integer', 'min:1']]);
        $to = CarbonImmutable::parse($data['to'] ?? now('Asia/Jakarta')->format('Y-m-d'), 'Asia/Jakarta')->startOfDay();
        $from = CarbonImmutable::parse($data['from'] ?? $to->format('Y-m-d'), 'Asia/Jakarta')->startOfDay();
        abort_if($from->greaterThan($to) || $from->diffInDays($to) >= 93, 422, 'Periode harus berurutan dan maksimal 93 hari.');
        return [$from, $to];
    }

    private function day(string $column): string
    {
        // The existing application stores naive timestamps in UTC (config/app.php).
        return match (DB::getDriverName()) {
            'sqlite' => "DATE(datetime($column, '+7 hours'))",
            'mysql', 'mariadb' => "DATE(DATE_ADD($column, INTERVAL 7 HOUR))",
            default => abort(501, 'Agregasi tanggal dashboard belum tersedia untuk database ini.'),
        };
    }

    private function series($query, string $column, string $sum, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $expression = $this->day($column);
        $rows = $query->selectRaw("$expression as day, $sum as value")->groupByRaw($expression)->orderBy('day')->get()->keyBy('day');
        $result = [];
        for ($date = $from; $date->lessThanOrEqualTo($to); $date = $date->addDay()) {
            $key = $date->format('Y-m-d');
            $result[] = ['date' => $key, 'value' => (float) ($rows[$key]->value ?? 0)];
        }
        return $result;
    }

    public function company(Request $request, int $companyId): array
    {
        $user = $request->user();
        abort_unless($user->isSuperAdmin() || strtolower(trim($user->role?->role ?? '')) === 'admin', 403);
        abort_unless(app(MobileAccess::class)->allows($user, 'pos.sales.view'), 403, 'Izin laporan penjualan diperlukan.');
        [$from, $to] = $this->range($request);
        $storeId = $request->integer('store_id') ?: (! $user->isSuperAdmin() ? $user->store_id : null);
        if ($storeId) abort_unless(Store::where('company_id', $companyId)->whereKey($storeId)->exists(), 403);
        if (! $user->isSuperAdmin() && $user->store_id) abort_unless((int) $storeId === (int) $user->store_id, 403);
        $base = Sale::withoutGlobalScopes()->where('company_id', $companyId)->when($storeId, fn ($q) => $q->where('store_id', $storeId))->where('status', 'completed')->whereRaw('LOWER(payment_status) = ?', ['paid']);
        $sales = (clone $base)->whereBetween('created_at', [$from->utc(), $to->endOfDay()->utc()]);
        $revenue = (float) (clone $sales)->sum('total');
        $count = (clone $sales)->count();
        $days = (int) $from->diffInDays($to) + 1;
        $previous = (float) (clone $base)->whereBetween('created_at', [$from->subDays($days)->utc(), $from->subDay()->endOfDay()->utc()])->sum('total');
        $ids = (clone $sales)->select('id');
        $payments = DB::table('payments')->join('methods', 'methods.id', '=', 'payments.method_id')->join('sales', 'sales.id', '=', 'payments.sale_id')
            ->where('payments.company_id', $companyId)->where('payments.status', 'active')->whereIn('payments.sale_id', clone $ids)
            ->selectRaw("methods.name as label, SUM(payments.amount - CASE WHEN LOWER(TRIM(methods.name)) IN ('cash','tunai') THEN sales.change_amount ELSE 0 END) as value")->groupBy('methods.id', 'methods.name')->get();
        $top = DB::table('sale_items')->leftJoin('products', 'products.id', '=', 'sale_items.product_id')->whereIn('sale_items.sale_id', clone $ids)
            ->selectRaw("sale_items.product_id, COALESCE(products.name, 'Produk dihapus') as name, SUM(sale_items.quantity) as quantity, SUM((sale_items.price-sale_items.discount)*sale_items.quantity+sale_items.tax) as total")->groupBy('sale_items.product_id', 'products.name')->orderByDesc('quantity')->limit(5)->get();
        $low = Product::where('company_id', $companyId)->where('track_stock', true)->whereColumn('stock_quantity', '<=', 'minimum_stock');
        return [
            'period' => ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d'), 'timezone' => 'Asia/Jakarta', 'store_id' => $storeId],
            'kpis' => ['revenue' => $revenue, 'transactions' => $count, 'average_order_value' => $count ? round($revenue/$count) : null, 'sales_growth' => $previous > 0 ? round(($revenue-$previous)/$previous*100, 2) : null, 'products' => Product::where('company_id', $companyId)->count(), 'low_stock' => (clone $low)->count(), 'customers' => Customer::where('company_id', $companyId)->count()],
            'sales_series' => $this->series(clone $sales, 'created_at', 'SUM(total)', $from, $to),
            'transaction_series' => $this->series(clone $sales, 'created_at', 'COUNT(*)', $from, $to),
            'payments' => $payments, 'top_products' => $top,
            'low_stock' => (clone $low)->orderBy('stock_quantity')->limit(5)->get(['id','name','stock_quantity','minimum_stock']),
            'latest_sales' => (clone $sales)->latest('created_at')->limit(5)->get(['id','invoice_number','created_at','total','payment_status']),
            'definitions' => ['revenue' => 'Penjualan completed dan paid, setelah diskon, termasuk pajak; void/pending tidak termasuk.', 'growth' => 'Perbandingan dengan periode sebelumnya berdurasi sama; null jika basis nol.', 'inventory' => 'Snapshot produk/stok/customer per company, tidak dibatasi periode/toko karena model stok per company.'],
        ];
    }

    private function licenseStatus(Company $company): string
    {
        if ($company->subscription_status === 'inactive') return 'INACTIVE';
        if (! $company->is_active || $company->subscription_status === 'blocked') return 'SUSPENDED';
        if (! $company->payment_due_at) return 'INACTIVE';
        $today = now()->startOfDay(); $due = $company->payment_due_at->copy()->startOfDay();
        if ($due->lt($today)) return $due->copy()->addDays(max((int) ($company->grace_period_days ?? 7), 0))->gte($today) ? 'GRACE_PERIOD' : 'EXPIRED';
        return $today->diffInDays($due) <= 7 ? 'EXPIRING_SOON' : 'ACTIVE';
    }

    public function platform(Request $request): array
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Hanya Super Admin.');
        [$from, $to] = $this->range($request);
        $companies = Company::query()->select(['id','name','company_code','is_active','subscription_status','payment_due_at','grace_period_days','created_at'])->get();
        $licenses = $companies->groupBy(fn ($company) => $this->licenseStatus($company))->map(fn ($rows, $status) => ['label' => $status, 'value' => $rows->count()])->values();
        $billing = CompanyPayment::where('purpose', 'license')->whereBetween('created_at', [$from->utc(), $to->endOfDay()->utc()]);
        $activity = AuditLog::withoutGlobalScopes()->latest('created_at')->limit(8)->get(['id','company_id','action','module','target_id','created_at']);
        $companyNames = $companies->pluck('name', 'id');
        $distribution = User::withoutGlobalScopes()->whereNotNull('company_id')->where('is_super_admin', false)->selectRaw('company_id, COUNT(*) as value')->groupBy('company_id')->get()->map(fn ($row) => ['label' => $companyNames[$row->company_id] ?? 'Company #'.$row->company_id, 'value' => (int) $row->value])->sortByDesc('value')->values();
        return [
            'period' => ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d'), 'timezone' => 'Asia/Jakarta'],
            'kpis' => ['companies' => $companies->count(), 'active_companies' => $companies->where('is_active', true)->count(), 'inactive_companies' => $companies->where('is_active', false)->count(), 'expiring_licenses' => $companies->filter(fn ($c) => in_array($this->licenseStatus($c), ['EXPIRING_SOON','GRACE_PERIOD']))->count(), 'expired_licenses' => $companies->filter(fn ($c) => $this->licenseStatus($c) === 'EXPIRED')->count(), 'registered_users' => User::withoutGlobalScopes()->count(), 'active_api_users' => MobileApiToken::where('last_used_at', '>=', now()->subMinutes(15))->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->distinct()->count('user_id'), 'billing_paid' => (float) CompanyPayment::where('purpose', 'license')->where('status', 'paid')->whereBetween('paid_at', [$from->utc(), $to->endOfDay()->utc()])->sum('amount')],
            'company_series' => $this->series(Company::whereBetween('created_at', [$from->utc(), $to->endOfDay()->utc()]), 'created_at', 'COUNT(*)', $from, $to),
            'license_distribution' => $licenses, 'user_distribution' => $distribution->take(10)->values(),
            'billing' => (clone $billing)->selectRaw('status as label, COUNT(*) as count, SUM(amount) as value')->groupBy('status')->get(),
            'latest_companies' => $companies->sortByDesc('created_at')->take(5)->values(), 'activity' => $activity,
            'definitions' => ['active_users' => 'Akun dengan token mobile belum expired dan last_used_at dalam 15 menit; bukan status online realtime.', 'company_status' => 'Aktif/nonaktif berdasarkan is_active, terpisah dari status lisensi.', 'billing' => 'Pendapatan lisensi: purpose=license, status paid, paid_at dalam periode. Breakdown status: pembayaran dibuat dalam periode (created_at).', 'snapshot' => 'Company, user, dan lisensi adalah snapshot saat ini. Growth chart menghitung pendaftaran pada periode.'],
        ];
    }
}
