<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminService;


class AdminController extends Controller
{
    public $adminService;

    public function __construct(){
        $this->adminService = new AdminService();
    }

    public function adminPanel()
    {
        return $this->adminService->adminPanel();
    }

}
