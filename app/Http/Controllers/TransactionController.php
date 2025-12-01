<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;

class TransactionController extends Controller
{
    protected $firestore;

    public function __construct(FirestoreService $firestore)
    {
        $this->firestore = $firestore;
    }

    public function index(Request $request)
    {
        // Ambil filter dari request
        $status = $request->get('status', 'all');

        // Query dari Firestore
        $filters = [];

        // Filter berdasarkan status jika bukan "all"
        if ($status !== 'all') {
            $filters[] = ['status', '=', strtoupper($status)];
        }

        // Ambil data transaksi dengan filter, order by createdAt descending
        $transactions = $this->firestore->query(
            'transfer_histories',
            $filters,
            'createdAt',
            'DESC',
            100 // Limit 100 transaksi
        );

        // Log untuk debugging
        \Log::info('Transaction query', [
            'status' => $status,
            'count' => $transactions->count(),
            'is_ajax' => $request->ajax() || $request->has('ajax')
        ]);

        // If AJAX request, return only the table rows
        if ($request->ajax() || $request->has('ajax')) {
            $html = view('partials.transaction-rows', [
                'transactions' => $transactions
            ])->render();

            return response($html, 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ]);
        }

        // Return full page for normal request
        return view('transactions', [
            'transactions' => $transactions,
            'currentStatus' => $status
        ]);
    }
}
