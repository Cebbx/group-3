<?php

use Illuminate\Support\Facades\Route;

try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
} catch (\Throwable $e) {
    Route::get('/', function () use ($e) {
        return response('<h1>Database Connection Error</h1><p>Please check your credentials in Render Environment variables.</p><pre>' . $e->getMessage() . '</pre>', 500);
    });
}

Route::view('/', 'welcome')->name('home');

Route::get('/trip-tickets/{ticket_number}/complete-via-qr', [App\Http\Controllers\QrCodeController::class, 'completeTrip'])->name('trip-tickets.complete-via-qr');
Route::get('/guard/scanner', [App\Http\Controllers\QrCodeController::class, 'scannerPage'])->name('guard.scanner');
Route::post('/guard/verify-pin', [App\Http\Controllers\QrCodeController::class, 'verifyPin'])->name('guard.verify-pin');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('post','livewire.post.index')->name('post.index');
    
    // Protected Print Routes
    Route::get('/vehicle-requests/{id}/print', [App\Http\Controllers\PrintController::class, 'printRequest'])->name('vehicle-requests.print');
    Route::get('/trip-tickets/{id}/print', [App\Http\Controllers\PrintController::class, 'printTicket'])->name('trip-tickets.print');
    Route::get('/withdrawal-slips/{id}/print', [App\Http\Controllers\PrintController::class, 'printSlip'])->name('withdrawal-slips.print');
});

require __DIR__.'/settings.php';
