<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CashMovementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\WorkdeskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorShareController;
use Illuminate\Support\Facades\Route;

// Public Vendor Share & Download Portal (No login needed for print vendor)
Route::get('/share/{token}', [VendorShareController::class, 'show'])->name('vendor.share');
Route::get('/share/{token}/download', [VendorShareController::class, 'download'])->name('vendor.share.download');
Route::get('/share/{token}/preview', [VendorShareController::class, 'preview'])->name('vendor.share.preview');

// Guest Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/quick-login', [AuthController::class, 'quickLogin'])->name('quick-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated System Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Orders Management
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create-design', [OrderController::class, 'create'])->name('orders.create-design');
    Route::post('/orders/create-design', [OrderController::class, 'store'])->name('orders.store-design');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'printReceipt'])->name('orders.receipt');
    Route::post('/orders/{order}/settle', [OrderController::class, 'settlePayment'])->name('orders.settle');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    // Fast Retail POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // Designer Workdesk
    Route::get('/workdesk', [WorkdeskController::class, 'index'])->name('workdesk.index');
    Route::post('/workdesk/{job}/upload', [WorkdeskController::class, 'uploadAsset'])->name('workdesk.upload');
    Route::post('/workdesk/{job}/claim', [WorkdeskController::class, 'claimJob'])->name('workdesk.claim');

    // Cash Book & Audit
    Route::get('/cash', [CashMovementController::class, 'index'])->name('cash.index');
    Route::post('/cash', [CashMovementController::class, 'store'])->name('cash.store');

    // Cloud Design Archive
    Route::get('/archive', [ArchiveController::class, 'index'])->name('archive.index');
    Route::get('/archive/{asset}/download', [ArchiveController::class, 'download'])->name('archive.download');
    Route::get('/archive/{asset}/preview', [ArchiveController::class, 'preview'])->name('archive.preview');
    Route::post('/vendor/dispatch/{asset}/email', [VendorShareController::class, 'sendEmail'])->name('vendor.dispatch.email');
    Route::get('/vendor/dispatch/{asset}/preview-email', [VendorShareController::class, 'previewEmail'])->name('vendor.dispatch.preview-email');

    // Multi-branch settings (Owner only)
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
    Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');

    // Staff / User Management (Owner only)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});
