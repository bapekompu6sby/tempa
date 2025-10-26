<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TEMPA</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 bg-white text-gray-900 min-h-screen flex flex-col leading-relaxed" style="margin: 0;">
    <header class="w-full py-6 bg-pu-blue border-b border-pu-blue-dark" style="background:#203368; border-bottom-color:#1b2d56;">
        <div class="container mx-auto flex items-center justify-between">
            <h1 class="text-3xl font-semibold" style="color:#FDB714"><a href="{{ url('/') }}" class="hover:underline" style="color:inherit">TEMPA</a></h1>
            <nav>
                <a href="{{ route('instructions.index') }}" class="text-sm mr-4" style="color:#FDB714">Instruksi</a>
                <a href="{{ route('events.index') }}" class="text-sm" style="color:#FDB714">Pelatihan</a>
            </nav>
        </div>
    </header>
    <main class="flex-1 container mx-auto px-6 py-8">
        @if(session('success'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
    <footer class="w-full border-t border-pu-blue bg-white py-4">
        <div class="container mx-auto text-center text-sm text-gray-600">&copy; {{ date('Y') }} TEMPA - Balai Pengembangan Kompetensi PU Wilayah VI Surabaya </div>
    </footer>
    {{-- Stack for page-specific scripts --}}
    @stack('scripts')
</body>
</html>
