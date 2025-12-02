<div wire:poll.3s>
    <div class="bg-white rounded-xl shadow-sm">
        <!-- Tabs -->
        <div class="border-b border-gray-200 px-6">
            <div class="flex gap-8">
                <button wire:click="setStatus('all')" @class([
                    'px-1 py-4 font-semibold border-b-2 transition',
                    'text-purple-600 border-purple-600' => $status === 'all',
                    'text-gray-500 hover:text-gray-700 border-transparent' => $status !== 'all',
                ])>
                    All Transactions
                </button>
                <button wire:click="setStatus('PENDING_APPROVAL')" @class([
                    'px-1 py-4 font-semibold border-b-2 transition',
                    'text-purple-600 border-purple-600' => $status === 'PENDING_APPROVAL',
                    'text-gray-500 hover:text-gray-700 border-transparent' =>
                        $status !== 'PENDING_APPROVAL',
                ])>
                    Pending Approval
                </button>
                <button wire:click="setStatus('SUCCESS')" @class([
                    'px-1 py-4 font-semibold border-b-2 transition',
                    'text-purple-600 border-purple-600' => $status === 'SUCCESS',
                    'text-gray-500 hover:text-gray-700 border-transparent' =>
                        $status !== 'SUCCESS',
                ])>
                    Success
                </button>
                <button wire:click="setStatus('FAILED')" @class([
                    'px-1 py-4 font-semibold border-b-2 transition',
                    'text-purple-600 border-purple-600' => $status === 'FAILED',
                    'text-gray-500 hover:text-gray-700 border-transparent' =>
                        $status !== 'FAILED',
                ])>
                    Failed
                </button>
                <button wire:click="setStatus('REJECTED')" @class([
                    'px-1 py-4 font-semibold border-b-2 transition',
                    'text-purple-600 border-purple-600' => $status === 'REJECTED',
                    'text-gray-500 hover:text-gray-700 border-transparent' =>
                        $status !== 'REJECTED',
                ])>
                    Rejected
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-6 py-4 text-left">
                            <input type="checkbox"
                                class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">ID Invoice</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Recipient</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Amount</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        @php
                            $recipientName = $transaction['recipientName'] ?? 'Unknown';
                            $amount = $transaction['amount'] ?? 0;
                            $txStatus = strtoupper($transaction['status'] ?? 'PENDING');
                            $recipientAcc = $transaction['recipientAcc'] ?? '';

                            // Format date
                            $createdAt = $transaction['createdAt'];
                            if (is_string($createdAt)) {
                                $dateTime = new DateTime($createdAt);
                            } else {
                                $dateTime = new DateTime();
                            }

                            // Get short ID (last 7 chars)
                            $shortId = substr($transaction['id'], -7);

                            // Status badge styling
                            $statusClass = match ($txStatus) {
                                'SUCCESS' => 'border-green-300 text-green-700 bg-green-50',
                                'FAILED' => 'border-red-300 text-red-700 bg-red-50',
                                'REJECTED' => 'border-orange-300 text-orange-700 bg-orange-50',
                                'PENDING_APPROVAL' => 'border-yellow-300 text-yellow-700 bg-yellow-50',
                                default => 'border-gray-300 text-gray-700 bg-gray-50',
                            };

                            $statusLabel = match ($txStatus) {
                                'SUCCESS' => 'Success',
                                'FAILED' => 'Failed',
                                'REJECTED' => 'Rejected',
                                'PENDING_APPROVAL' => 'Pending Approval',
                                default => ucfirst(strtolower(str_replace('_', ' ', $txStatus))),
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition" wire:key="tx-{{ $transaction['id'] }}">
                            <td class="px-6 py-4">
                                <input type="checkbox"
                                    class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ $shortId }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $dateTime->format('F j, Y, h:i A') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($recipientName) }}&background=random&color=fff"
                                        alt="{{ $recipientName }}" class="w-8 h-8 rounded-full">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ $recipientName }}</span>
                                        <span class="text-xs text-gray-500">{{ $recipientAcc }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">RM {{ number_format($amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">Transfer</td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex px-4 py-1.5 rounded-full text-sm font-medium border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">No transactions found</p>
                                    <p class="text-gray-400 text-sm mt-1">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-600">Live updates active</span>
                <span class="text-xs text-gray-400" wire:loading.remove>Auto-refresh every 3s</span>
                <span class="text-xs text-purple-600" wire:loading>
                    Updating...
                </span>
            </div>
            <button class="text-purple-600 hover:text-purple-700 font-semibold text-sm">
                See more
            </button>
        </div>
    </div>
</div>
