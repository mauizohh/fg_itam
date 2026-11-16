<?php

use App\Http\Controllers\ReportController;
use App\Livewire\Administration\AssetCategories;
use App\Livewire\Administration\AuditLogs;
use App\Livewire\Administration\Locations;
use App\Livewire\Administration\Roles;
use App\Livewire\Administration\Settings;
use App\Livewire\Administration\Users;
use App\Livewire\Assets\AllAssets;
use App\Livewire\Assets\AssetDetail;
use App\Livewire\Assets\AssetsInRepair;
use App\Livewire\Assets\AssignedAssets;
use App\Livewire\Assets\AvailableAssets;
use App\Livewire\Assets\RetiredAssets;
use App\Livewire\Audits\AuditHistory;
use App\Livewire\Audits\Exceptions;
use App\Livewire\Audits\NewAudit;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Employee\MyAssets;
use App\Livewire\Maintenance\Repairs;
use App\Livewire\Maintenance\Warranty;
use App\Livewire\People\Departments;
use App\Livewire\People\EmployeeDetail;
use App\Livewire\People\Employees;
use App\Livewire\Procurement\PurchaseOrders;
use App\Livewire\Procurement\Purchases;
use App\Livewire\Procurement\Suppliers;
use App\Livewire\Reports\ReportsIndex;
use App\Livewire\Software\Licenses;
use App\Livewire\Software\Renewals;
use App\Livewire\Software\SoftwareCatalog;
use App\Livewire\Transactions\AssignAsset;
use App\Livewire\Transactions\LoanAsset;
use App\Livewire\Transactions\ReturnAsset;
use App\Livewire\Transactions\TransactionHistory;
use App\Livewire\Transactions\TransferAsset;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function (): void {
    Route::get('/login', Login::class)->name('login');
});

Route::post('/logout', function (Request $request): RedirectResponse {
    $user = Auth::user();
    if ($user !== null) {
        AuditLogService::record(
            'Logout',
            'Auth',
            (string) $user->getAuthIdentifier(),
            null,
            'Signed out of FG ITAM',
            $user->name ?? 'System'
        );
    }

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

// Authenticated ITAM Application Routes
Route::middleware('auth')->group(function (): void {
    Route::get('/', Dashboard::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');
    Route::get('/dashboard', Dashboard::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard.index');

    // Employee Self-Service
    Route::get('/my-assets', MyAssets::class)
        ->middleware('permission:my_assets.view')
        ->name('my-assets');

    // Assets
    Route::prefix('assets')->name('assets.')->group(function (): void {
        Route::get('/', AllAssets::class)
            ->middleware('permission:assets.view')
            ->name('index');
        Route::get('/available', AvailableAssets::class)
            ->middleware('permission:assets.view')
            ->name('available');
        Route::get('/assigned', AssignedAssets::class)
            ->middleware('permission:assets.view')
            ->name('assigned');
        Route::get('/repair', AssetsInRepair::class)
            ->middleware('permission:assets.view')
            ->name('repair');
        Route::get('/retired', RetiredAssets::class)
            ->middleware('permission:assets.retired.view')
            ->name('retired');
        Route::get('/{id}', AssetDetail::class)
            ->middleware('permission:assets.view')
            ->name('detail');
    });

    // People
    Route::prefix('people')->name('people.')->middleware('permission:people.view')->group(function (): void {
        Route::get('/employees', Employees::class)->name('employees');
        Route::get('/employees/{id}', EmployeeDetail::class)->name('employee.detail');
        Route::get('/departments', Departments::class)->name('departments');
    });

    // Transactions
    Route::prefix('transactions')->name('transactions.')->group(function (): void {
        Route::get('/history', TransactionHistory::class)
            ->middleware('permission:transactions.history')
            ->name('history');
        Route::middleware('permission:transactions.manage')->group(function (): void {
            Route::get('/assign', AssignAsset::class)->name('assign');
            Route::get('/return', ReturnAsset::class)->name('return');
            Route::get('/transfer', TransferAsset::class)->name('transfer');
            Route::get('/loan', LoanAsset::class)->name('loan');
        });
    });

    // Procurement
    Route::prefix('procurement')->name('procurement.')->middleware('permission:procurement.view')->group(function (): void {
        Route::get('/suppliers', Suppliers::class)->name('suppliers');
        Route::get('/purchases', Purchases::class)->name('purchases');
        Route::get('/purchase-orders', PurchaseOrders::class)->name('purchase-orders');
    });

    // Maintenance
    Route::prefix('maintenance')->name('maintenance.')->middleware('permission:maintenance.view')->group(function (): void {
        Route::get('/repairs', Repairs::class)->name('repairs');
        Route::get('/warranty', Warranty::class)->name('warranty');
    });

    // Software
    Route::prefix('software')->name('software.')->middleware('permission:software.view')->group(function (): void {
        Route::get('/', SoftwareCatalog::class)->name('index');
        Route::get('/licenses', Licenses::class)->name('licenses');
        Route::get('/renewals', Renewals::class)->name('renewals');
    });

    // Audits
    Route::prefix('audits')->name('audits.')->group(function (): void {
        Route::get('/new', NewAudit::class)
            ->middleware('permission:audits.scan')
            ->name('new');
        Route::get('/history', AuditHistory::class)
            ->middleware('permission:audits.history.view')
            ->name('history');
        Route::get('/exceptions', Exceptions::class)
            ->middleware('permission:audits.exceptions.view')
            ->name('exceptions');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->middleware('permission:reports.view')->group(function (): void {
        Route::get('/', ReportsIndex::class)->name('index');
        Route::get('/pdf/{type}', [ReportController::class, 'downloadPdf'])->name('pdf');
        Route::get('/export/{type}', [ReportController::class, 'exportCsv'])->name('csv');
    });

    // Administration
    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/categories', AssetCategories::class)
            ->middleware('permission:admin.categories')
            ->name('categories');
        Route::get('/locations', Locations::class)
            ->middleware('permission:admin.locations')
            ->name('locations');
        Route::get('/users', Users::class)
            ->middleware('permission:admin.users')
            ->name('users');
        Route::get('/roles', Roles::class)
            ->middleware('permission:admin.roles')
            ->name('roles');
        Route::get('/settings', Settings::class)
            ->middleware('permission:admin.settings')
            ->name('settings');
        Route::get('/audit-logs', AuditLogs::class)
            ->middleware('permission:admin.audit_logs')
            ->name('audit-logs');
    });
});
