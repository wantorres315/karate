<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidenavOpen: true }" x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts e ícones -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Tailwind & Alpine -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="min-h-screen flex flex-col font-sans bg-gray-100 text-gray-900">

    <!-- Navbar -->
    <nav class="fixed w-full bg-black z-50 shadow-md">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/logo_branco.png') }}" class="h-12" alt="Logo">
                </a>
                <!-- Botão menu mobile -->
                <button @click="sidenavOpen = !sidenavOpen" class="text-white ml-6 xl:hidden">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            <div class="flex items-center space-x-4">
                @include('layouts.user-dropdown')
            </div>
        </div>
    </nav>

    <!-- Layout principal -->
    <div class="flex flex-1 pt-20 min-h-[calc(100vh-80px)]">

        <!-- Sidebar -->
         @include('layouts.navigation')

        <!-- Overlay mobile -->
        <div 
            x-show="sidenavOpen" 
            @click="sidenavOpen = false" 
            x-transition.opacity 
            class="fixed inset-0 bg-black bg-opacity-50 z-40 xl:hidden">
        </div>

        <!-- Main content -->
        <main 
            class="flex-1 p-6 min-h-screen transition-all duration-300"
            :class="w-full sidenavOpen ? 'xl:ml-64' : 'xl:ml-20'"
        >
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-2 gap-6">
                <div class="col-span-full mt-6 p-6 bg-white rounded-xl shadow-md min-h-[500px]">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="p-4 bg-black text-white text-center">
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
