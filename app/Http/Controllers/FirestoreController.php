<?php

namespace App\Http\Controllers;

use App\Services\FirestoreService;
use Illuminate\Http\Request;

class FirestoreController extends Controller
{
    protected $firestore;

    public function __construct(FirestoreService $firestore)
    {
        $this->firestore = $firestore;
    }

    /**
     * Get all users from Firestore
     * Endpoint: GET /api/users
     */
    public function getUsers(Request $request)
    {
        try {
            // Ambil semua data dari collection 'users'
            $users = $this->firestore->all('users');

            return response()->json([
                'success' => true,
                'data' => $users,
                'count' => $users->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
