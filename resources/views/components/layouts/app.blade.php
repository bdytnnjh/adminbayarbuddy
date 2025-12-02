<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'BayarBuddy Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <x-header :title="$title ?? 'Dashboard'" />

            <!-- Content Area -->
            <main class="flex-1 p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>

</html>
