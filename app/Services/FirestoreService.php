<?php

namespace App\Services;

use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\Query;

class FirestoreService
{
    protected $db;

    public function __construct()
    {
        // Pastikan file JSON ada di storage/app/firebase_credentials.json
        $keyPath = storage_path('app/firebase_credentials.json');

        if (!file_exists($keyPath)) {
            throw new \Exception("File kredensial Firebase tidak ditemukan di: " . $keyPath);
        }

        $this->db = new FirestoreClient([
            'keyFilePath' => $keyPath,
            // Project ID opsional jika sudah ada di dalam JSON, tapi aman jika didefine
            // 'projectId' => env('FIREBASE_PROJECT_ID'), 
        ]);
    }

    /**
     * Mengambil satu dokumen berdasarkan Collection dan ID
     */
    public function find(string $collection, string $id)
    {
        $docRef = $this->db->collection($collection)->document($id);
        $snapshot = $docRef->snapshot();

        if ($snapshot->exists()) {
            return array_merge(['id' => $snapshot->id()], $snapshot->data());
        }

        return null;
    }

    /**
     * Mengambil semua data dari collection (Hati-hati jika data ribuan!)
     */
    public function all(string $collection)
    {
        $docs = $this->db->collection($collection)->documents();
        return $this->transformDocuments($docs);
    }

    /**
     * Query Fleksibel dengan Where, OrderBy, dan Limit
     * * Contoh penggunaan:
     * $filters = [
     * ['status', '=', 'active'],
     * ['umur', '>', 20]
     * ];
     */
    public function query(string $collection, array $filters = [], string $orderBy = null, string $direction = 'DESC', int $limit = 0)
    {
        $query = $this->db->collection($collection);

        // Terapkan Filter (Where)
        foreach ($filters as $filter) {
            // $filter[0] = field, $filter[1] = operator, $filter[2] = value
            $query = $query->where($filter[0], $filter[1], $filter[2]);
        }

        // Terapkan Order By
        if ($orderBy) {
            $query = $query->orderBy($orderBy, $direction);
        }

        // Terapkan Limit
        if ($limit > 0) {
            $query = $query->limit($limit);
        }

        return $this->transformDocuments($query->documents());
    }

    /**
     * Menambah data baru (Auto Generated ID)
     */
    public function create(string $collection, array $data)
    {
        // Tambahkan timestamp manual karena Firestore tidak otomatis seperti MySQL
        $data['created_at'] = new \DateTime();
        $data['updated_at'] = new \DateTime();

        $ref = $this->db->collection($collection)->add($data);
        return $ref->id(); // Mengembalikan ID baru
    }

    /**
     * Set data dengan ID tertentu (Create or Replace)
     */
    public function set(string $collection, string $id, array $data)
    {
        $data['updated_at'] = new \DateTime();
        $this->db->collection($collection)->document($id)->set($data, ['merge' => true]);
        return $id;
    }

    /**
     * Update sebagian field saja
     */
    public function update(string $collection, string $id, array $data)
    {
        $docRef = $this->db->collection($collection)->document($id);

        // Format update Firestore butuh struktur ['path' => 'field', 'value' => 'val']
        $updates = [];
        foreach ($data as $key => $val) {
            $updates[] = ['path' => $key, 'value' => $val];
        }
        $updates[] = ['path' => 'updated_at', 'value' => new \DateTime()];

        $docRef->update($updates);
        return true;
    }

    /**
     * Hapus dokumen
     */
    public function delete(string $collection, string $id)
    {
        $this->db->collection($collection)->document($id)->delete();
        return true;
    }

    /**
     * Helper internal untuk mengubah Object Firestore jadi Array bersih
     */
    protected function transformDocuments($documents)
    {
        $results = [];
        foreach ($documents as $document) {
            if ($document->exists()) {
                $results[] = array_merge(['id' => $document->id()], $document->data());
            }
        }
        return collect($results); // Return sebagai Laravel Collection biar enak di-loop
    }
}