<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\CustomerStatementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Invoice\DownloadInvoiceController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Invoice\OverdueController;
use App\Http\Controllers\Invoice\PrintInvoiceController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesReturn\SalesReturnController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): Factory|View => view('welcome'));

Route::middleware('auth')->group(function (): void {
    Route::delete('/logout', [SessionController::class, 'destroy'])->name('logout');

    Route::prefix('dashboard')->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resources([
            'invoices' => InvoiceController::class,
            'customers' => CustomerController::class,
            'payments' => PaymentController::class,
        ]);
        // Route::resource('payments', PaymentController::class)->except(['edit', 'update']);
        Route::resource('sales-returns', SalesReturnController::class)->except(['create']);
        Route::controller(CustomerStatementController::class)->group(function (): void {
            Route::get('/statements/customers', 'index')->name('customers.statements.index');
            Route::get('/statements/customers/{customer}', 'show')->name('customers.statements.show');
        });
        Route::controller(ReportController::class)->group(function (): void {
            Route::get('/reports/daily', 'daily')->name('reports.daily');
            Route::get('/reports/monthly', 'monthly')->name('reports.monthly');
            Route::get('/reports/top-customers', 'topCustomers')->name('reports.top-customers');
            Route::get('/reports/profit', 'profit')->name('reports.profit');
        });

        Route::get('/invoices/{invoice}/download', DownloadInvoiceController::class)->name('invoices.download');
        Route::get('/invoices/{invoice}/print', PrintInvoiceController::class)->name('invoices.print');
        Route::get('/overdue', OverdueController::class)->name('overdue.index');

        Route::get('/payments/create/{invoice?}', [PaymentController::class, 'create'])->name('payments.create');
        Route::get('/sales-returns/create/{invoice}', [SalesReturnController::class, 'create'])->name('sales-returns.create');

    });
});
Route::prefix('auth')
    ->middleware('guest')
    ->group(function (): void {
        Route::controller(RegisterController::class)->group(function (): void {
            Route::get('/register', 'create')->name('register');
            Route::post('/register', 'store')->name('register.post')->middleware('throttle:auth');
        });
        Route::controller(SessionController::class)->group(function (): void {
            Route::get('/login', 'create')->name('login');
            Route::post('/login', 'store')->name('login.post')->middleware('throttle:auth');
        });
    });
