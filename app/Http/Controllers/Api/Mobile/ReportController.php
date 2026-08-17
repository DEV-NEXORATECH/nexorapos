<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Pos\CashierShift;
use App\Models\Pos\Payment;
use App\Models\Pos\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function auditLogs(Request $request)
    {
        $companyId = $this->companyId($request);
        $logs = AuditLog::query()
            ->where('company_id', $companyId)
            ->when($request->module, fn ($q) => $q->where('module', $request->module))
            ->when($request->action, fn ($q) => $q->where('action', $request->action))
            ->latest('created_at')
            ->paginate((int) $request->get('per_page', 20));

        return $this->json(true, 'Audit log list.', ['audit_logs' => $logs]);
    }

    public function salesExport(Request $request, string $format)
    {
        $companyId = $this->companyId($request);
        $sales = Sale::with(['store', 'customer', 'user', 'payments.method'])
            ->where('company_id', $companyId)
            ->when($request->date, fn ($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->get();

        $rows = $sales->map(fn ($sale) => [
            'invoice' => $sale->invoice_number,
            'date' => $sale->created_at?->format('Y-m-d H:i:s'),
            'store' => $sale->store?->name,
            'customer' => $sale->customer?->name,
            'cashier' => $sale->user?->name,
            'total' => $sale->total,
            'paid' => $sale->paid_amount,
            'status' => $sale->status,
        ])->all();

        return $this->export($format, 'sales-report', $rows, 'Sales Report');
    }

    public function shiftExport(Request $request, string $format)
    {
        if (! Schema::hasTable('cashier_shifts')) {
            return $this->json(false, 'Migration cashier_shifts belum dijalankan.', null, 503);
        }

        $companyId = $this->companyId($request);
        $rows = CashierShift::with(['store', 'user'])
            ->where('company_id', $companyId)
            ->when($request->date, fn ($q) => $q->whereDate('opened_at', $request->date))
            ->latest('opened_at')
            ->get()
            ->map(fn ($shift) => [
                'cashier' => $shift->user?->name,
                'store' => $shift->store?->name,
                'shift' => $shift->shift_name,
                'status' => $shift->status,
                'opened_at' => $shift->opened_at?->format('Y-m-d H:i:s'),
                'closed_at' => $shift->closed_at?->format('Y-m-d H:i:s'),
                'opening_cash' => $shift->opening_cash,
                'closing_cash' => $shift->closing_cash,
                'expected_cash' => $shift->expected_cash,
                'cash_difference' => $shift->cash_difference,
                'sales_total' => $shift->sales_total,
            ])->all();

        return $this->export($format, 'shift-report', $rows, 'Shift Report');
    }

    public function reconciliationExport(Request $request, string $format)
    {
        $companyId = $this->companyId($request);
        $date = $request->get('date', now()->toDateString());
        $sales = Sale::with('payments.method')->where('company_id', $companyId)->whereDate('created_at', $date)->get();
        $payments = Payment::whereIn('sale_id', $sales->pluck('id'))->with('method')->get();

        $rows = $payments
            ->groupBy(fn ($payment) => $payment->method?->name ?? 'Unknown')
            ->map(fn ($items, $method) => [
                'date' => $date,
                'method' => $method,
                'transactions' => $items->count(),
                'payment_total' => $items->sum('amount'),
            ])
            ->values()
            ->all();

        return $this->export($format, 'reconciliation-report', $rows, 'Reconciliation Report');
    }

    private function export(string $format, string $filename, array $rows, string $title)
    {
        if ($format === 'pdf') {
            $html = view('admin.reports.simple-pdf', compact('rows', 'title'))->render();

            return Pdf::loadHTML($html)->download($filename.'.pdf');
        }

        if ($format !== 'csv' && $format !== 'xlsx') {
            return $this->json(false, 'Format harus csv, xlsx, atau pdf.', null, 422);
        }

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            if (! empty($rows)) {
                fputcsv($handle, array_keys($rows[0]));
            }
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename.'.csv', ['Content-Type' => 'text/csv']);
    }

    private function companyId(Request $request): int
    {
        if ($request->user()?->isSuperAdmin() && $request->query('company_id')) {
            return (int) $request->query('company_id');
        }

        return (int) $request->user()->company_id;
    }

    private function json(bool $success, string $message, mixed $data = null, int $status = 200)
    {
        return response()->json(compact('success', 'message', 'data'), $status);
    }
}
