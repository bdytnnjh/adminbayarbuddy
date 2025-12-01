<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

// FCM Notification endpoints
Route::prefix('notifications')->group(function () {
    // Send notification to single device by FCM token
    Route::post('/send', [NotificationController::class, 'sendToDevice']);

    // Send notification to multiple devices
    Route::post('/send-multiple', [NotificationController::class, 'sendToMultipleDevices']);

    // Send notification to topic
    Route::post('/send-topic', [NotificationController::class, 'sendToTopic']);

    // Send notification to user by user ID
    Route::post('/send-to-user', [NotificationController::class, 'sendToUser']);
});
