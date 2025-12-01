<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;

class FcmService
{
    private $credentials;
    private $projectId;
    private $httpClient;

    public function __construct()
    {
        try {
            $keyPath = storage_path('app/firebase_credentials.json');

            if (!file_exists($keyPath)) {
                \Log::error("File kredensial Firebase tidak ditemukan di: " . $keyPath);
                $this->credentials = null;
                return;
            }

            $jsonKey = json_decode(file_get_contents($keyPath), true);
            if (!is_array($jsonKey) || ($jsonKey['type'] ?? null) !== 'service_account') {
                \Log::error('Konfigurasi kredensial Firebase tidak valid atau bukan service_account.');
                $this->credentials = null;
                return;
            }

            $this->credentials = $jsonKey;
            $this->projectId = $jsonKey['project_id'] ?? env('FIREBASE_PROJECT_ID');
            $this->httpClient = new \GuzzleHttp\Client(['timeout' => 30]);

            \Log::info('FcmService initialized successfully for project: ' . $this->projectId);
        } catch (\Throwable $e) {
            \Log::error('FcmService initialization failed: ' . $e->getMessage());
            $this->credentials = null;
        }
    }

    /**
     * Get access token for Firebase authentication
     */
    private function getAuthToken()
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentialsFetcher = new ServiceAccountCredentials($scopes, $this->credentials);
        return $credentialsFetcher->fetchAuthToken();
    }

    /**
     * Send notification to a single device
     * 
     * @param string $fcmToken - FCM token perangkat
     * @param string $title - Judul notifikasi
     * @param string $body - Isi notifikasi
     * @param array $data - Data tambahan (optional)
     * @return array - Response dari FCM API
     */
    public function sendToDevice(string $fcmToken, string $title, string $body, array $data = [])
    {
        if (!$this->credentials) {
            throw new \Exception('FCM Service not initialized');
        }

        try {
            $authToken = $this->getAuthToken();
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            $message = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ]
            ];

            // Tambahkan data jika ada
            if (!empty($data)) {
                $message['message']['data'] = $data;
            }

            $response = $this->httpClient->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken['access_token'],
                    'Content-Type' => 'application/json',
                ],
                'json' => $message
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            \Log::info('FCM notification sent successfully', [
                'fcm_token' => substr($fcmToken, 0, 20) . '...',
                'title' => $title
            ]);

            return [
                'success' => true,
                'data' => $result
            ];

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response';

            \Log::error('FCM notification failed', [
                'error' => $e->getMessage(),
                'response' => $errorBody
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'details' => json_decode($errorBody, true)
            ];
        } catch (\Throwable $e) {
            \Log::error('FCM notification error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to multiple devices
     * 
     * @param array $fcmTokens - Array of FCM tokens
     * @param string $title - Judul notifikasi
     * @param string $body - Isi notifikasi
     * @param array $data - Data tambahan (optional)
     * @return array - Response summary
     */
    public function sendToMultipleDevices(array $fcmTokens, string $title, string $body, array $data = [])
    {
        $results = [
            'success_count' => 0,
            'failure_count' => 0,
            'results' => []
        ];

        foreach ($fcmTokens as $token) {
            $result = $this->sendToDevice($token, $title, $body, $data);

            if ($result['success']) {
                $results['success_count']++;
            } else {
                $results['failure_count']++;
            }

            $results['results'][] = [
                'token' => substr($token, 0, 20) . '...',
                'success' => $result['success'],
                'error' => $result['error'] ?? null
            ];
        }

        return $results;
    }

    /**
     * Send notification to topic
     * 
     * @param string $topic - Topic name
     * @param string $title - Judul notifikasi
     * @param string $body - Isi notifikasi
     * @param array $data - Data tambahan (optional)
     * @return array - Response dari FCM API
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = [])
    {
        if (!$this->credentials) {
            throw new \Exception('FCM Service not initialized');
        }

        try {
            $authToken = $this->getAuthToken();
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            $message = [
                'message' => [
                    'topic' => $topic,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ]
            ];

            // Tambahkan data jika ada
            if (!empty($data)) {
                $message['message']['data'] = $data;
            }

            $response = $this->httpClient->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken['access_token'],
                    'Content-Type' => 'application/json',
                ],
                'json' => $message
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            \Log::info('FCM topic notification sent successfully', [
                'topic' => $topic,
                'title' => $title
            ]);

            return [
                'success' => true,
                'data' => $result
            ];

        } catch (\Throwable $e) {
            \Log::error('FCM topic notification error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
