<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Services\FirestoreService;

Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');

Route::get('/test-firestore', function (FirestoreService $firestore) {
    try {
        // Menggunakan method all() dari FirestoreService
        $users = $firestore->all('transfer_histories');

        dd([
            'transfer_histories_count' => $users->count(),
            'transfer_histories' => $users,
            'test' => 'Firestore service loaded successfully'
        ]);
    } catch (\Exception $e) {
        dd([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});