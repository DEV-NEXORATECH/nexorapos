<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class CfdController extends Controller
{
    // Render the Customer Facing Display view
    public function index($cashier_id)
    {
        return view('admin.pos.sale.cfd', compact('cashier_id'));
    }

    // API to fetch the latest cached cart data for a specific cashier
    public function getData($cashier_id)
    {
        $data = Cache::get("cfd_cart_{$cashier_id}");

        if (!$data) {
            // Return empty cart structure if no active session
            $data = [
                'cart' => [],
                'totals' => ['subtotal' => 0, 'discount' => 0, 'tax' => 0, 'total' => 0],
                'updated_at' => null
            ];
        }

        return response()->json($data);
    }
}
