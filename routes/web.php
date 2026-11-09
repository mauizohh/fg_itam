<?php

use Illuminate\Support\Facades\Route;

// Main navigation routes mapping to 32 UI screens
Route::get('/', \App\Livewire\Dashboard::class)->name('dashboard');
Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard.index');

// Assets
Route::prefix('assets')->name('assets.')->group(function () {
    Route::get('/', \App\Livewire\Assets\AllAssets::class)->name('index');
    Route::get('/available', \App\Livewire\Assets\AvailableAssets::class)->name('available');
    Route::get('/assigned', \App\Livewire\Assets\AssignedAssets::class)->name('assigned');
    Route::get('/repair', \App\Livewire\Assets\AssetsInRepair::class)->name('repair');
    Route::get('/retired', \App\Livewire\Assets\RetiredAssets::class)->name('retired');
    Route::get('/{id}', \App\Livewire\Assets\AssetDetail::class)->name('detail');
});

// People
Route::prefix('people')->name('people.')->group(function () {
    Route::get('/employees', \App\Livewire\People\Employees::class)->name('employees');
    Route::get('/employees/{id}', \App\Livewire\People\EmployeeDetail::class)->name('employee.detail');
    Route::get('/departments', \App\Livewire\People\Departments::class)->name('departments');
});

// Transactions
Route::prefix('transactions')->name('transactions.')->group(function () {
    Route::get('/assign', \App\Livewire\Transactions\AssignAsset::class)->name('assign');
    Route::get('/return', \App\Livewire\Transactions\ReturnAsset::class)->name('return');
    Route::get('/transfer', \App\Livewire\Transactions\TransferAsset::class)->name('transfer');
    Route::get('/loan', \App\Livewire\Transactions\LoanAsset::class)->name('loan');
});

// Procurement
Route::prefix('procurement')->name('procurement.')->group(function () {
    Route::get('/suppliers', \App\Livewire\Procurement\Suppliers::class)->name('suppliers');
    Route::get('/purchases', \App\Livewire\Procurement\Purchases::class)->name('purchases');
    Route::get('/purchase-orders', \App\Livewire\Procurement\PurchaseOrders::class)->name('purchase-orders');
});

// Maintenance
Route::prefix('maintenance')->name('maintenance.')->group(function () {
    Route::get('/repairs', \App\Livewire\Maintenance\Repairs::class)->name('repairs');
    Route::get('/warranty', \App\Livewire\Maintenance\Warranty::class)->name('warranty');
});

// Software
Route::prefix('software')->name('software.')->group(function () {
    Route::get('/', \App\Livewire\Software\SoftwareCatalog::class)->name('index');
    Route::get('/licenses', \App\Livewire\Software\Licenses::class)->name('licenses');
    Route::get('/renewals', \App\Livewire\Software\Renewals::class)->name('renewals');
});

// Audits
Route::prefix('audits')->name('audits.')->group(function () {
    Route::get('/new', \App\Livewire\Audits\NewAudit::class)->name('new');
    Route::get('/history', \App\Livewire\Audits\AuditHistory::class)->name('history');
    Route::get('/exceptions', \App\Livewire\Audits\Exceptions::class)->name('exceptions');
});

// Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', \App\Livewire\Reports\ReportsIndex::class)->name('index');
    Route::get('/pdf/{type}', [\App\Http\Controllers\ReportController::class, 'downloadPdf'])->name('pdf');
    Route::get('/export/{type}', [\App\Http\Controllers\ReportController::class, 'exportCsv'])->name('csv');
});

// Administration
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/categories', \App\Livewire\Administration\AssetCategories::class)->name('categories');
    Route::get('/locations', \App\Livewire\Administration\Locations::class)->name('locations');
    Route::get('/users', \App\Livewire\Administration\Users::class)->name('users');
    Route::get('/roles', \App\Livewire\Administration\Roles::class)->name('roles');
    Route::get('/settings', \App\Livewire\Administration\Settings::class)->name('settings');
    Route::get('/audit-logs', \App\Livewire\Administration\AuditLogs::class)->name('audit-logs');
});
