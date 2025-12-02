@props(['active' => 'transactions'])

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
                <a href="#" @class([
                    'flex items-center gap-3 px-4 py-3 rounded-lg transition',
                    'bg-purple-50 text-purple-600 border-l-4 border-purple-600' =>
                        $active === 'home',
                    'text-gray-600 hover:bg-gray-50' => $active !== 'home',
                ])>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Home</span>
                </a>
            </li>
            <li>
                <a href="#" @class([
                    'flex items-center gap-3 px-4 py-3 rounded-lg transition',
                    'bg-purple-50 text-purple-600 border-l-4 border-purple-600' =>
                        $active === 'users',
                    'text-gray-600 hover:bg-gray-50' => $active !== 'users',
                ])>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Users Management</span>
                </a>
            </li>
            <li>
                <a href="{{ route('transactions.index') }}" @class([
                    'flex items-center gap-3 px-4 py-3 rounded-lg transition',
                    'bg-purple-50 text-purple-600 border-l-4 border-purple-600' =>
                        $active === 'transactions',
                    'text-gray-600 hover:bg-gray-50' => $active !== 'transactions',
                ])>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Transactions</span>
                </a>
            </li>
            <li>
                <a href="#" @class([
                    'flex items-center gap-3 px-4 py-3 rounded-lg transition',
                    'bg-purple-50 text-purple-600 border-l-4 border-purple-600' =>
                        $active === 'family',
                    'text-gray-600 hover:bg-gray-50' => $active !== 'family',
                ])>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Family Help Requests</span>
                </a>
            </li>
            <li>
                <a href="#" @class([
                    'flex items-center gap-3 px-4 py-3 rounded-lg transition',
                    'bg-purple-50 text-purple-600 border-l-4 border-purple-600' =>
                        $active === 'security',
                    'text-gray-600 hover:bg-gray-50' => $active !== 'security',
                ])>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Security Center</span>
                </a>
            </li>
            <li>
                <a href="#" @class([
                    'flex items-center gap-3 px-4 py-3 rounded-lg transition',
                    'bg-purple-50 text-purple-600 border-l-4 border-purple-600' =>
                        $active === 'fraud',
                    'text-gray-600 hover:bg-gray-50' => $active !== 'fraud',
                ])>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Fraud Detection</span>
                </a>
            </li>
            <li>
                <a href="#" @class([
                    'flex items-center gap-3 px-4 py-3 rounded-lg transition',
                    'bg-purple-50 text-purple-600 border-l-4 border-purple-600' =>
                        $active === 'notifications',
                    'text-gray-600 hover:bg-gray-50' => $active !== 'notifications',
                ])>
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
        <button class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition w-full">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            <span>Settings</span>
        </button>
        <button class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition w-full">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Log Out</span>
        </button>
    </div>
</aside>
