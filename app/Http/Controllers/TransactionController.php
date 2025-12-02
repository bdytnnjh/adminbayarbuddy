<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        return view('welcome', [
            'currentStatus' => $status
        ]);
    }
}
