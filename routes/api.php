<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FirestoreController;

Route::get('/users', [FirestoreController::class, 'getUsers']);
