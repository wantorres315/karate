<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Mazer CSS -->
    <link rel="stylesheet" href="https://zuramai.github.io/mazer/demo/assets/compiled/css/app.css">
    <link rel="stylesheet" href="https://zuramai.github.io/mazer/demo/assets/compiled/css/app-dark.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

    <div id="app">

        {{-- Sidebar Mazer --}}
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <h3 class="text-center">{{ config('app.name', 'Laravel') }}</h3>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-item active">
                            <a href="{{ route('dashboard') }}" class="sidebar-link">
                                <i class="bi bi-speedometer2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-people"></i>
                                <span>Usuários</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-bar-chart"></i>
                                <span>Relatórios</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div id="main" class="layout-navbar">
            {{-- Navbar --}}
            <header class="mb-3">
                <nav class="navbar navbar-expand navbar-light bg-light shadow-sm">
                    <div class="container-fluid">
                        <a href="#" class="burger-btn d-block d-xl-none">
                            <i class="bi bi-list fs-3"></i>
                        </a>

                        <div class="ms-auto d-flex align-items-center">
                            <span class="me-3 fw-semibold">{{ Auth::user()->name ?? 'Guest' }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline-danger btn-sm">Sair</button>
                            </form>
                        </div>
                    </div>
                </nav>
            </header>

            {{-- Page Heading --}}
            @isset($header)
                <div class="page-heading px-4">
                    {{ $header }}
                </div>
            @endisset

            {{-- Page Content --}}
            <div class="page-content px-4">
                {{ $slot }}
            </div>
        </div>
    </div>

    {{-- Bootstrap + Mazer JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://zuramai.github.io/mazer/demo/assets/static/js/initTheme.js"></script>
</body>
</html>
