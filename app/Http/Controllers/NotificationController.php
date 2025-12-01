<?php

namespace App\Http\Controllers;

use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Send notification to single device
     * POST /api/notifications/send
     * 
     * Request Body:
     * {
     *   "fcm_token": "device_fcm_token_here",
     *   "title": "Notification Title",
     *   "body": "Notification Body",
     *   "data": { "key": "value" } // optional
     * }
     */
    public function sendToDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->fcmService->sendToDevice(
                $request->fcm_token,
                $request->title,
                $request->body,
                $request->data ?? []
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification sent successfully',
                    'data' => $result['data']
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send notification',
                    'error' => $result['error'],
                    'details' => $result['details'] ?? null
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to multiple devices
     * POST /api/notifications/send-multiple
     * 
     * Request Body:
     * {
     *   "fcm_tokens": ["token1", "token2", "token3"],
     *   "title": "Notification Title",
     *   "body": "Notification Body",
     *   "data": { "key": "value" } // optional
     * }
     */
    public function sendToMultipleDevices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_tokens' => 'required|array|min:1',
            'fcm_tokens.*' => 'required|string',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->fcmService->sendToMultipleDevices(
                $request->fcm_tokens,
                $request->title,
                $request->body,
                $request->data ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Notifications sent',
                'summary' => [
                    'total' => count($request->fcm_tokens),
                    'success' => $result['success_count'],
                    'failed' => $result['failure_count']
                ],
                'details' => $result['results']
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to topic
     * POST /api/notifications/send-topic
     * 
     * Request Body:
     * {
     *   "topic": "all_users",
     *   "title": "Notification Title",
     *   "body": "Notification Body",
     *   "data": { "key": "value" } // optional
     * }
     */
    public function sendToTopic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->fcmService->sendToTopic(
                $request->topic,
                $request->title,
                $request->body,
                $request->data ?? []
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification sent to topic successfully',
                    'data' => $result['data']
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send notification to topic',
                    'error' => $result['error']
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending notification to topic',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to user by user ID (fetch FCM token from Firestore)
     * POST /api/notifications/send-to-user
     * 
     * Request Body:
     * {
     *   "user_id": "user_document_id",
     *   "title": "Notification Title",
     *   "body": "Notification Body",
     *   "data": { "key": "value" } // optional
     * }
     */
    public function sendToUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get user from Firestore
            $firestoreService = app(\App\Services\FirestoreService::class);
            $user = $firestoreService->find('users', $request->user_id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Check if user has FCM token
            if (empty($user['fcmToken'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have FCM token'
                ], 400);
            }

            $result = $this->fcmService->sendToDevice(
                $user['fcmToken'],
                $request->title,
                $request->body,
                $request->data ?? []
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification sent to user successfully',
                    'user_id' => $request->user_id,
                    'data' => $result['data']
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send notification to user',
                    'error' => $result['error'],
                    'details' => $result['details'] ?? null
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending notification to user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
