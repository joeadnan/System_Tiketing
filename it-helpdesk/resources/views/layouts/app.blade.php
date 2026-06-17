<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'IT Helpdesk Ticketing' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <nav class="bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="font-bold">IT Helpdesk</a>
            <div class="flex gap-4 text-sm">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-200">Dashboard</a>
                <a href="{{ route('tickets.index') }}" class="hover:text-blue-200">Tickets</a>
                <a href="{{ route('reports.tickets') }}" class="hover:text-blue-200">Reports</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </main>
</body>
</html>
