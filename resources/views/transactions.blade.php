<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BayarBuddy - Transactions</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
            <!-- Logo -->
            <div class="p-6">
                <div class="flex items-center gap-2">
                    <div
                        class="w-12 h-12 bg-linear-to-br from-purple-600 to-pink-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-xl">B</span>
                    </div>
                    <span class="font-bold text-xl">BayarBuddy</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4">
                <ul class="space-y-1">
                    <li>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Users Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 bg-purple-50 text-purple-600 rounded-lg border-l-4 border-purple-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Transactions</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Family Help Requests</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Security Center</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Fraud Detection</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span>Notifications</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Bottom Navigation -->
            <div class="p-4 border-t border-gray-200">
                <button
                    class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span>Settings</span>
                </button>
                <button
                    class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Log Out</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-8 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800">Transactions</h1>

                    <div class="flex items-center gap-4">
                        <!-- Search -->
                        <button class="p-2 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>

                        <!-- User Profile -->
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-800">Sushant Nikam</p>
                                <p class="text-xs text-gray-500">nikamasushant4@gmail.com</p>
                            </div>
                            <img src="https://ui-avatars.com/api/?name=Sushant+Nikam&background=7c3aed&color=fff"
                                alt="User" class="w-10 h-10 rounded-full">
                        </div>

                        <!-- Notification Bell -->
                        <button class="p-2 hover:bg-gray-100 rounded-lg transition relative">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>

                        <!-- Flag/Report -->
                        <button class="p-2 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 p-8">
                <div class="bg-white rounded-xl shadow-sm">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200 px-6">
                        <div class="flex gap-8">
                            <a href="{{ route('transactions.index', ['status' => 'all']) }}"
                                class="px-1 py-4 {{ $currentStatus === 'all' ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-500 hover:text-gray-700' }} font-semibold">
                                All Transactions
                            </a>
                            <a href="{{ route('transactions.index', ['status' => 'success']) }}"
                                class="px-1 py-4 {{ $currentStatus === 'success' ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-500 hover:text-gray-700' }} font-semibold">
                                Completed
                            </a>
                            <a href="{{ route('transactions.index', ['status' => 'failed']) }}"
                                class="px-1 py-4 {{ $currentStatus === 'failed' ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-500 hover:text-gray-700' }} font-semibold">
                                Failed
                            </a>
                            <a href="{{ route('transactions.index', ['status' => 'pending']) }}"
                                class="px-1 py-4 {{ $currentStatus === 'pending' ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-500 hover:text-gray-700' }} font-semibold">
                                Pending
                            </a>
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
                                        $senderName = $transaction['senderName'] ?? 'Unknown';
                                        $amount = $transaction['amount'] ?? 0;
                                        $status = strtoupper($transaction['status'] ?? 'PENDING');
                                        $recipientAcc = $transaction['recipientAcc'] ?? '';

                                        // Format date
                                        $createdAt = $transaction['createdAt'];
                                        if ($createdAt instanceof \Google\Cloud\Core\Timestamp) {
                                            $dateTime = $createdAt->get();
                                        } else {
                                            $dateTime = new DateTime();
                                        }

                                        // Get short ID (last 7 chars)
                                        $shortId = substr($transaction['id'], -7);

                                        // Status badge styling
                                        $statusClass = match ($status) {
                                            'SUCCESS' => 'border-green-300 text-green-700 bg-green-50',
                                            'FAILED' => 'border-red-300 text-red-700 bg-white',
                                            default => 'border-gray-300 text-gray-700 bg-white',
                                        };

                                        $statusLabel = match ($status) {
                                            'SUCCESS' => 'Completed',
                                            'FAILED' => 'Failed',
                                            default => 'Pending',
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <input type="checkbox"
                                                class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ $shortId }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $dateTime->format('F j, Y, h:i A') }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($recipientName) }}&background=random&color=fff"
                                                    alt="{{ $recipientName }}" class="w-8 h-8 rounded-full">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-medium text-gray-900">{{ $recipientName }}</span>
                                                    <span class="text-xs text-gray-500">{{ $recipientAcc }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">RM
                                            {{ number_format($amount, 2) }}</td>
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
                                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
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

                    <!-- Footer with See More -->
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                        <button class="text-purple-600 hover:text-purple-700 font-semibold text-sm">
                            See more
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>
