<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;

class FirestoreService
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

            \Log::info('FirestoreService initialized successfully for project: ' . $this->projectId);
        } catch (\Throwable $e) {
            \Log::error('FirestoreService initialization failed: ' . $e->getMessage());
            $this->credentials = null;
        }
    }

    /**
     * Get access token for Firebase authentication
     */
    private function getAuthToken()
    {
        $scopes = ['https://www.googleapis.com/auth/cloud-platform'];
        $credentialsFetcher = new ServiceAccountCredentials($scopes, $this->credentials);
        return $credentialsFetcher->fetchAuthToken();
    }

    /**
     * Parse Firestore field value to PHP native type
     */
    private function parseFieldValue($value)
    {
        if (isset($value['stringValue'])) {
            return $value['stringValue'];
        } elseif (isset($value['integerValue'])) {
            return (int) $value['integerValue'];
        } elseif (isset($value['doubleValue'])) {
            return (float) $value['doubleValue'];
        } elseif (isset($value['booleanValue'])) {
            return $value['booleanValue'];
        } elseif (isset($value['timestampValue'])) {
            return $value['timestampValue'];
        } elseif (isset($value['nullValue'])) {
            return null;
        } elseif (isset($value['arrayValue']['values'])) {
            $array = [];
            foreach ($value['arrayValue']['values'] as $item) {
                $array[] = $this->parseFieldValue($item);
            }
            return $array;
        } elseif (isset($value['mapValue']['fields'])) {
            $map = [];
            foreach ($value['mapValue']['fields'] as $key => $val) {
                $map[$key] = $this->parseFieldValue($val);
            }
            return $map;
        }
        return $value;
    }

    /**
     * Convert PHP value to Firestore field format
     */
    private function toFirestoreValue($value)
    {
        if (is_string($value)) {
            return ['stringValue' => $value];
        } elseif (is_int($value)) {
            return ['integerValue' => (string) $value];
        } elseif (is_float($value)) {
            return ['doubleValue' => $value];
        } elseif (is_bool($value)) {
            return ['booleanValue' => $value];
        } elseif (is_null($value)) {
            return ['nullValue' => null];
        } elseif ($value instanceof \DateTime) {
            return ['timestampValue' => $value->format('Y-m-d\TH:i:s\Z')];
        } elseif (is_array($value)) {
            // Check if it's associative array (map) or indexed array
            if (array_keys($value) !== range(0, count($value) - 1)) {
                // Associative array - treat as map
                $fields = [];
                foreach ($value as $k => $v) {
                    $fields[$k] = $this->toFirestoreValue($v);
                }
                return ['mapValue' => ['fields' => $fields]];
            } else {
                // Indexed array
                $values = [];
                foreach ($value as $v) {
                    $values[] = $this->toFirestoreValue($v);
                }
                return ['arrayValue' => ['values' => $values]];
            }
        }
        return ['stringValue' => (string) $value];
    }

    /**
     * Mengambil satu dokumen berdasarkan Collection dan ID
     */
    public function find(string $collection, string $id)
    {
        if (!$this->credentials) {
            return null;
        }

        try {
            $authToken = $this->getAuthToken();
            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$id}";

            $response = $this->httpClient->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken['access_token'],
                    'Accept' => 'application/json',
                ]
            ]);

            $doc = json_decode($response->getBody()->getContents(), true);
            $fields = $doc['fields'] ?? [];

            $item = ['id' => $id];
            foreach ($fields as $key => $value) {
                $item[$key] = $this->parseFieldValue($value);
            }

            return $item;
        } catch (\Throwable $e) {
            \Log::error('Error finding document: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Mengambil semua data dari collection (Hati-hati jika data ribuan!)
     */
    public function all(string $collection)
    {
        if (!$this->credentials) {
            \Log::warning('Firestore credentials not initialized, returning empty collection');
            return collect([]);
        }

        try {
            $authToken = $this->getAuthToken();
            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}";

            $response = $this->httpClient->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken['access_token'],
                    'Accept' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $results = [];

            if (isset($data['documents'])) {
                foreach ($data['documents'] as $doc) {
                    $id = basename($doc['name']);
                    $fields = $doc['fields'] ?? [];

                    $item = ['id' => $id];
                    foreach ($fields as $key => $value) {
                        $item[$key] = $this->parseFieldValue($value);
                    }
                    $results[] = $item;
                }
            }

            return collect($results);

        } catch (\Throwable $e) {
            \Log::error('Error fetching documents from collection ' . $collection . ': ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Query Fleksibel dengan Where, OrderBy, dan Limit
     * Contoh penggunaan:
     * $filters = [
     *   ['status', '=', 'active'],
     *   ['age', '>', 20]
     * ];
     */
    public function query(string $collection, array $filters = [], string $orderBy = null, string $direction = 'DESC', int $limit = 0)
    {
        if (!$this->credentials) {
            \Log::warning('Firestore credentials not initialized, returning empty collection');
            return collect([]);
        }

        try {
            $authToken = $this->getAuthToken();
            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";

            // Build structured query
            $structuredQuery = [
                'from' => [['collectionId' => $collection]]
            ];

            // Add filters
            if (!empty($filters)) {
                $filterClauses = [];
                foreach ($filters as $filter) {
                    $field = $filter[0];
                    $op = $filter[1];
                    $value = $filter[2];

                    // Map operator
                    $opMap = [
                        '=' => 'EQUAL',
                        '==' => 'EQUAL',
                        '!=' => 'NOT_EQUAL',
                        '<' => 'LESS_THAN',
                        '<=' => 'LESS_THAN_OR_EQUAL',
                        '>' => 'GREATER_THAN',
                        '>=' => 'GREATER_THAN_OR_EQUAL',
                        'array-contains' => 'ARRAY_CONTAINS',
                        'in' => 'IN',
                    ];

                    $filterClauses[] = [
                        'fieldFilter' => [
                            'field' => ['fieldPath' => $field],
                            'op' => $opMap[$op] ?? 'EQUAL',
                            'value' => $this->toFirestoreValue($value)
                        ]
                    ];
                }

                if (count($filterClauses) > 1) {
                    $structuredQuery['where'] = [
                        'compositeFilter' => [
                            'op' => 'AND',
                            'filters' => $filterClauses
                        ]
                    ];
                } else {
                    $structuredQuery['where'] = $filterClauses[0];
                }
            }

            // Add order by - jika ada filter equality, harus include field filter juga di orderBy
            if ($orderBy) {
                $orderByFields = [];

                // Untuk filter equality pada field yang berbeda dari orderBy, 
                // Firestore membutuhkan composite index
                $orderByFields[] = [
                    'field' => ['fieldPath' => $orderBy],
                    'direction' => $direction === 'ASC' ? 'ASCENDING' : 'DESCENDING'
                ];

                $structuredQuery['orderBy'] = $orderByFields;
            }

            // Add limit
            if ($limit > 0) {
                $structuredQuery['limit'] = $limit;
            }

            // Log query untuk debugging
            \Log::info('Firestore query', [
                'collection' => $collection,
                'filters' => $filters,
                'orderBy' => $orderBy,
                'query' => json_encode($structuredQuery)
            ]);

            $response = $this->httpClient->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken['access_token'],
                    'Content-Type' => 'application/json',
                ],
                'json' => ['structuredQuery' => $structuredQuery]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            // Log response untuk debugging
            \Log::info('Firestore response', [
                'collection' => $collection,
                'documentCount' => is_array($data) ? count($data) : 0,
                'hasDocuments' => isset($data[0]['document'])
            ]);

            $results = [];

            if (is_array($data)) {
                foreach ($data as $item) {
                    if (isset($item['document'])) {
                        $doc = $item['document'];
                        $id = basename($doc['name']);
                        $fields = $doc['fields'] ?? [];

                        $result = ['id' => $id];
                        foreach ($fields as $key => $value) {
                            $result[$key] = $this->parseFieldValue($value);
                        }
                        $results[] = $result;
                    }
                }
            }

            \Log::info('Firestore results parsed', ['count' => count($results)]);

            return collect($results);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            \Log::error('Firestore query error (ClientException): ' . $responseBody);

            // Check if it's an index error
            if (str_contains($responseBody, 'index')) {
                \Log::warning('Firestore requires a composite index. Falling back to client-side filtering.');
                return $this->queryWithClientSideFiltering($collection, $filters, $orderBy, $direction, $limit);
            }

            return collect([]);
        } catch (\Throwable $e) {
            \Log::error('Error querying collection ' . $collection . ': ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Fallback: Query without server-side filter, then filter on client side
     */
    private function queryWithClientSideFiltering(string $collection, array $filters = [], string $orderBy = null, string $direction = 'DESC', int $limit = 0)
    {
        try {
            $authToken = $this->getAuthToken();
            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";

            // Query tanpa filter
            $structuredQuery = [
                'from' => [['collectionId' => $collection]]
            ];

            // Jika bisa orderBy tanpa filter
            if ($orderBy) {
                $structuredQuery['orderBy'] = [
                    [
                        'field' => ['fieldPath' => $orderBy],
                        'direction' => $direction === 'ASC' ? 'ASCENDING' : 'DESCENDING'
                    ]
                ];
            }

            $response = $this->httpClient->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken['access_token'],
                    'Content-Type' => 'application/json',
                ],
                'json' => ['structuredQuery' => $structuredQuery]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $results = [];

            if (is_array($data)) {
                foreach ($data as $item) {
                    if (isset($item['document'])) {
                        $doc = $item['document'];
                        $id = basename($doc['name']);
                        $fields = $doc['fields'] ?? [];

                        $result = ['id' => $id];
                        foreach ($fields as $key => $value) {
                            $result[$key] = $this->parseFieldValue($value);
                        }
                        $results[] = $result;
                    }
                }
            }

            $collection = collect($results);

            // Apply client-side filtering
            foreach ($filters as $filter) {
                $field = $filter[0];
                $op = $filter[1];
                $value = $filter[2];

                $collection = $collection->filter(function ($item) use ($field, $op, $value) {
                    $itemValue = $item[$field] ?? null;

                    return match ($op) {
                        '=', '==' => $itemValue === $value,
                        '!=' => $itemValue !== $value,
                        '<' => $itemValue < $value,
                        '<=' => $itemValue <= $value,
                        '>' => $itemValue > $value,
                        '>=' => $itemValue >= $value,
                        default => true,
                    };
                });
            }

            // Apply limit
            if ($limit > 0) {
                $collection = $collection->take($limit);
            }

            \Log::info('Client-side filtering results', ['count' => $collection->count()]);

            return $collection->values();

        } catch (\Throwable $e) {
            \Log::error('Error in client-side filtering: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Menambah data baru (Auto Generated ID)
     */
    public function create(string $collection, array $data)
    {
        if (!$this->credentials) {
            return null;
        }

        try {
            // Tambahkan timestamp manual
            $data['created_at'] = new \DateTime();
            $data['updated_at'] = new \DateTime();

            $authToken = $this->getAuthToken();
            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}";

            // Convert data to Firestore format
            $fields = [];
            foreach ($data as $key => $value) {
                $fields[$key] = $this->toFirestoreValue($value);
            }

            $response = $this->httpClient->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken['access_token'],
                    'Content-Type' => 'application/json',
                ],
                'json' => ['fields' => $fields]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            return basename($result['name'] ?? '');

        } catch (\Throwable $e) {
            \Log::error('Error creating document: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Set data dengan ID tertentu (Create or Replace)
     */
    public function set(string $collection, string $id, array $data)
    {
        if (!$this->credentials) {
            return null;
        }

        try {
            $data['updated_at'] = new \DateTime();

            $authToken = $this->getAuthToken();
            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$id}";

            // Convert data to Firestore format
            $fields = [];
            foreach ($data as $key => $value) {
                $fields[$key] = $this->toFirestoreValue($value);
            }

            $response = $this->httpClient->patch($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken['access_token'],
                    'Content-Type' => 'application/json',
                ],
                'json' => ['fields' => $fields]
            ]);

            return $id;

        } catch (\Throwable $e) {
            \Log::error('Error setting document: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update sebagian field saja
     */
    public function update(string $collection, string $id, array $data)
    {
        if (!$this->credentials) {
            return false;
        }

        try {
            $data['updated_at'] = new \DateTime();

            $authToken = $this->getAuthToken();

            // Build update mask (field paths to update)
            $updateMask = [];
            foreach (array_keys($data) as $key) {
                $updateMask[] = $key;
            }

            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$id}?updateMask.fieldPaths=" . implode('&updateMask.fieldPaths=', $updateMask);

            // Convert data to Firestore format
            $fields = [];
            foreach ($data as $key => $value) {
                $fields[$key] = $this->toFirestoreValue($value);
            }

            $response = $this->httpClient->patch($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken['access_token'],
                    'Content-Type' => 'application/json',
                ],
                'json' => ['fields' => $fields]
            ]);

            return true;

        } catch (\Throwable $e) {
            \Log::error('Error updating document: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Hapus dokumen
     */
    public function delete(string $collection, string $id)
    {
        if (!$this->credentials) {
            return false;
        }

        try {
            $authToken = $this->getAuthToken();
            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$id}";

            $this->httpClient->delete($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken['access_token'],
                ]
            ]);

            return true;

        } catch (\Throwable $e) {
            \Log::error('Error deleting document: ' . $e->getMessage());
            return false;
        }
    }
}