<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\FirestoreService;

class TransactionTable extends Component
{
    public string $status = 'all';
    public int $limit = 100;

    protected FirestoreService $firestore;

    public function boot(FirestoreService $firestore)
    {
        $this->firestore = $firestore;
    }

    public function mount(string $status = 'all')
    {
        $this->status = $status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function getTransactionsProperty()
    {
        $filters = [];

        if ($this->status !== 'all') {
            $filters[] = ['status', '=', $this->status];
        }

        return $this->firestore->query(
            'transfer_histories',
            $filters,
            'createdAt',
            'DESC',
            $this->limit
        );
    }

    public function render()
    {
        return view('livewire.transaction-table', [
            'transactions' => $this->transactions,
        ]);
    }
}
