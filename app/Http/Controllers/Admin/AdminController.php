<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminService;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function adminPanel()
    {
        return $this->adminService->adminPanel();
    }

    public function getSalesData(Request $request)
    {
        return $this->adminService->getSalesData($request);
    }
}
