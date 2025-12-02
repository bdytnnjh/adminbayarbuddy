@props(['title' => 'Dashboard'])

<header class="bg-white border-b border-gray-200 px-8 py-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>

        <div class="flex items-center gap-4">
            <!-- Search -->
            <button class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <img src="https://ui-avatars.com/api/?name=Sushant+Nikam&background=7c3aed&color=fff" alt="User"
                    class="w-10 h-10 rounded-full">
            </div>

            <!-- Notification Bell -->
            <button class="p-2 hover:bg-gray-100 rounded-lg transition relative">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>

            <!-- Flag/Report -->
            <button class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                </svg>
            </button>
        </div>
    </div>
</header>
