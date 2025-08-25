<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\PermissionCategoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\RoleUserController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Route;
// use App\Http\Middleware\AdminMiddleware;


Route::get('/', function () {
    return view('front.home');
});

// Route::get('/dashboard', function () {

//     $user = Auth::user();

//         if ($user->is_admin) {
//             return redirect()->to('adminPanel/dashboard');
//         } else {
//             return redirect()->to('frontPanel/dashBoard');
//         }

// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


    Route::get('frontPanel/dashBoard', [HomeController::class, 'index'])->name('homePage');
    Route::get('frontPanel/archive', [HomeController::class, 'archive'])->name('archivePage');
    Route::get('frontPanel/category', [HomeController::class, 'category'])->name('catPage');
    Route::get('frontPanel/blogDetails', [HomeController::class, 'blogDetail'])->name('blogPage');
    Route::get('frontPanel/contactPage', [HomeController::class, 'contact'])->name('contactPage');
    Route::post('frontPanel/contactPage', [HomeController::class, 'store'])->name('contactPage');
    Route::post('frontPanel/newsletter', [NewsLetterController::class, 'news']);

    Route::get('blogdetails/{id}', [HomeController::class, 'blogdetails'])->name('blogdetails');

Route::middleware(['auth', 'Admin', 'XSS'])->group(function () {

    Route::get('admin-panel/dashboard', [AdminController::class, 'adminPanel']);

    Route::get('admin-panel/categories/add', [CategoryController::class, 'category']);
    Route::get('admin-panel/categories/listing', [CategoryController::class, 'index']);
    Route::post('admin-panel/categories/add', [CategoryController::class, 'store']);
    Route::get('admin-panel/categories/edit/{id}', [CategoryController::class, 'edit']);
    Route::post('admin-panel/categories/update/{id}', [CategoryController::class, 'update']);
    Route::get('admin-panel/categories/delete/{id}', [CategoryController::class, 'destroy']);

    //Route FOr posts Table
    Route::get('admin-panel/posts/listing', [PostController::class, 'index']);
    Route::get('admin-panel/posts/add', [PostController::class, 'post']);
    Route::post('admin-panel/posts/add', [PostController::class, 'store']);
    Route::get('admin-panel/posts/edit/{id}', [PostController::class, 'edit']);
    Route::put('admin-panel/posts/update/{id}', [PostController::class, 'update']);
    Route::get('admin-panel/posts/delete/{id}', [PostController::class, 'destroy']);


    //Routes For Users crud
    Route::get('admin-panel/users/listing', [UserController::class, 'index']);
    Route::get('admin-panel/users/add', [UserController::class, 'post']);
    Route::post('admin-panel/users/add', [UserController::class, 'store']);
    Route::get('admin-panel/users/edit/{id}', [UserController::class, 'edit']);
    Route::post('admin-panel/posts/update/{id}', [UserController::class, 'update']);
    Route::get('admin-panel/users/delete/{id}', [UserController::class, 'destroy']);

    //Routes for roles
    Route::get('admin-panel/roles/listing', [RoleController::class, 'index']);
    Route::get('admin-panel/roles/add', [RoleController::class, 'add']);
    Route::post('admin-panel/roles/add', [RoleController::class, 'store']);
    Route::get('admin-panel/roles/edit/{id}', [RoleController::class, 'edit']);
    Route::post('admin-panel/roles/update/{id}', [RoleController::class, 'update']);
    Route::get('admin-panel/roles/delete/{id}', [RoleController::class, 'destroy']);

    //Route for permissions

    Route::get('admin-panel/permissions/listing', [PermissionController::class, 'index']);
    Route::get('admin-panel/permissions/add', [PermissionController::class, 'add']);
    Route::post('admin-panel/permissions/add', [PermissionController::class, 'store']);
    Route::get('admin-panel/permissions/edit/{id}', [PermissionController::class, 'edit']);
    Route::post('admin-panel/permissions/update/{id}', [PermissionController::class, 'update']);
    Route::get('admin-panel/permissions/delete/{id}', [PermissionController::class, 'destroy']);

    //Route for Role Permission pivot

    Route::get('admin-panel/role-permissions/listing', [RolePermissionController::class, 'index']);
    Route::get('admin-panel/role-permissions/add', [RolePermissionController::class, 'add']);
    Route::post('admin-panel/role-permissions/add', [RolePermissionController::class, 'store']);
    Route::get('admin-panel/role-permissions/edit/{id}', [RolePermissionController::class, 'edit']);
    Route::post('admin-panel/role-permissions/update/{id}', [RolePermissionController::class, 'update']);
    Route::get('admin-panel/role-permissions/delete/{id}', [RolePermissionController::class, 'destroy']);

    //Route for User Role pivot

    Route::get('admin-panel/user-role/listing', [RoleUserController::class, 'index']);
    Route::get('admin-panel/user-role/add', [RoleUserController::class, 'add']);
    Route::post('admin-panel/user-role/add', [RoleUserController::class, 'store']);
    Route::get('admin-panel/user-role/edit/{id}', [RoleUserController::class, 'edit']);
    Route::post('admin-panel/user-role/update/{id}', [RoleUserController::class, 'update']);
    Route::get('admin-panel/user-role/delete/{id}', [RoleUserController::class, 'destroy']);

    //ROute FOr adding permission Categories

    Route::get('admin-panel/permission-categories/listing', [PermissionCategoryController::class, 'index']);
    Route::get('admin-panel/permission-categories/add', [PermissionCategoryController::class, 'add']);
    Route::post('admin-panel/permission-categories/add', [PermissionCategoryController::class, 'store']);
    Route::get('admin-panel/permission-categories/edit/{id}', [PermissionCategoryController::class, 'edit']);
    Route::post('admin-panel/permission-categories/update/{id}', [PermissionCategoryController::class, 'update']);
    Route::get('admin-panel/permission-categories/delete/{id}', [PermissionCategoryController::class, 'destroy']);

    //Route For Setting
    Route::get('admin-panel/settings/listing', [SettingController::class, 'index']);
    Route::get('admin-panel/settings/add', [SettingController::class, 'add']);
    Route::Post('admin-panel/settings/add', [SettingController::class, 'store']);
    Route::get('admin-panel/settings/edit/{id}', [SettingController::class, 'edit']);
    Route::post('admin-panel/settings/update/{id}', [SettingController::class, 'update']);
    Route::get('admin-panel/settings/delete/{id}', [SettingController::class, 'destroy']);


});

 //ROutes For Email
    Route::get('sendEmail', [EmailController::class, 'sendEmail']);

