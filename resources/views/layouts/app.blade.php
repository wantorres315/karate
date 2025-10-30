<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidenavOpen: false }" x-cloak x-init="sidenavOpen = innerWidth >= 1280; window.addEventListener('resize', () => { if (innerWidth >= 1280) sidenavOpen = true })">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Tailwind & Alpine -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="min-h-screen flex flex-col font-sans bg-gray-100 text-gray-900" @keydown.window.escape="sidenavOpen = false">

    <!-- Navbar -->
    <nav class="fixed top-0 w-full bg-black z-50 shadow-md">
        <div class="flex items-center justify-between px-4 sm:px-6 py-2 sm:py-3">
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/logo_branco.png') }}" class="h-8 sm:h-10 xl:h-12" alt="Logo">
                </a>

                <!-- Botão menu (mobile apenas) -->
                <button
                    @click="sidenavOpen = !sidenavOpen"
                    class="text-white focus:outline-none xl:hidden"
                    :aria-expanded="sidenavOpen.toString()"
                    aria-controls="main-sidenav"
                    aria-label="Abrir menu"
                >
                    <i class="fas fa-bars text-2xl"></i>
                </button>

                <!-- Botão para alternar o menu também no desktop (XL) -->
                <button
                    @click="sidenavOpen = !sidenavOpen"
                    class="text-white focus:outline-none hidden xl:inline-flex"
                    :aria-expanded="sidenavOpen.toString()"
                    aria-controls="main-sidenav"
                    aria-label="Alternar menu"
                >
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>
            <div class="flex items-center space-x-4">
                <form method="GET" action="{{ route('members.index') }}" class="flex items-center gap-2">
                    <input name="q" value="{{ request('q') }}" placeholder="Pesquisar por nome, KAK, e-mail..." class="px-3 py-2 border rounded-md w-64" />
                    <button type="submit" class="px-3 py-2 rounded-md bg-gray-800 text-white">Pesquisar</button>
                </form>
                @include('layouts.user-dropdown')
            </div>
        </div>
    </nav>

    <!-- Layout Principal -->
    <div class="flex flex-1 pt-16 sm:pt-20 min-h-[calc(100vh-80px)] relative">

        <!-- Sidebar -->
        
        @include('layouts.navigation')

        <!-- Main Content -->
        <main :class="sidenavOpen ? 'flex-1 p-4 sm:p-6 transition-all duration-300 xl:ml-64' : 'flex-1 p-4 sm:p-6 transition-all duration-300 xl:ml-0'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="w-full col-span-full bg-white rounded-xl shadow-md p-6 min-h-[400px]">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="p-4 bg-black text-white text-center text-sm sm:text-base">
        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Todos os direitos reservados.
    </footer>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register("{{ asset('service-worker.js') }}")
                .then(reg => console.log("SW registrado:", reg))
                .catch(err => console.error("Erro SW:", err));
        }
    </script>

    @stack('scripts')
</body>
</html>
