<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Patuha Outdoor</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-slate-100 overflow-x-hidden">

    <div class="flex min-h-screen">

        @include('partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0">

            @include('partials.navbar')

            <main class="flex-1 p-4 md:p-8 overflow-x-hidden">
                @if(session('success'))
                    <div class="mb-4 rounded bg-green-100 border border-green-400 text-green-700 px-4 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 rounded bg-red-100 border border-red-400 text-red-700 px-4 py-3">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')

            </main>

        </div>

    </div>

</body>

</html>