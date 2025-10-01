<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidenavOpen: false }" x-cloak>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" /> 
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#4F46E5">
  <title>{{ config('app.name', 'Laravel') }}</title>
  

   <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <!-- Font Awesome nova versão -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <!-- Main Styling -->
    <link href="{{ asset('assets/css/argon-dashboard-tailwind.css') }}" rel="stylesheet" />

    <!-- Fonts Laravel -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- CSS externos primeiro -->
  <link rel="stylesheet" href="{{ asset('assets/css/nucleo-icons.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/nucleo-svg.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/argon-dashboard-tailwind.css') }}" />
  <!-- Tailwind CSS por último -->

  <!-- Scripts -->

  @stack('head')

  <!-- x-cloak CSS -->
  <style>[x-cloak] { display: none !important; }</style>
</head> 

<body class="min-h-screen flex flex-col m-0 font-sans text-base antialiased font-normal leading-default bg-brand-white text-brand-preto">

  <!-- Navbar preta -->
  <div class="w-full !bg-brand-preto min-h-25"  style="background: #262626">
    <nav class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 shadow-none rounded-2xl" navbar-main>
      <div class="flex items-center justify-between w-full px-4 py-1 mx-auto">
        <div class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0">
          <ul class="flex flex-row justify-end pl-0 mb-0 list-none w-full">
            @include('layouts.user-dropdown')
            <li class="flex items-center pl-4 xl:hidden">
              <button @click="sidenavOpen = true" class="block p-0 text-sm text-brand-white">
                <div class="w-4.5">
                  <i class="block h-0.5 rounded-sm !bg-brand-white mb-1"></i>
                  <i class="block h-0.5 rounded-sm !bg-brand-white mb-1"></i>
                  <i class="block h-0.5 rounded-sm !bg-brand-white"></i>
                </div>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </div>

  <!-- Sidenav preta -->
  <aside style="background: #262626"
    class="fixed inset-y-0 left-0 z-[999] flex-wrap justify-between block w-full max-w-64 p-0 my-4 antialiased transition-transform duration-200 !bg-brand-preto shadow-xl xl:translate-x-0 xl:ml-6 xl:block rounded-2xl"
    :class="{ '-translate-x-full xl:translate-x-0': !sidenavOpen }"
  >
    <div class="h-19 relative">
      <button @click="sidenavOpen = false" class="absolute top-0 right-0 p-4 opacity-50 cursor-pointer text-brand-gray xl:hidden">
        <i class="fas fa-times !text-brand-white"></i>
      </button>
      <a class="block px-8 py-6 text-sm whitespace-nowrap !text-brand-white">
        <img src="{{ asset('images/logo_branco.png') }}" class="inline max-h-8" alt="logo" />
      </a>
    </div>

    <hr class="h-px bg-gradient-to-r from-transparent via-brand-gray to-transparent" />

    @include('layouts.navigation')
  </aside>

  <!-- Conteúdo principal (centro branco) -->
  <main class="flex-1 relative xl:ml-68 transition-all rounded-xl">
    <div class="w-full px-6 py-6 mx-auto">
      <section class="mt-4 p-6 !bg-brand-white !text-brand-preto rounded-xl shadow-md">
        {{ $slot }}
      </section>
    </div>
  </main>

  <!-- Footer preto -->
  <footer class="p-4 mt-6 !bg-brand-preto text-white text-center" style="background: #262626">
    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Todos os direitos reservados.
  </footer>

  <!-- Scripts -->
  @stack('scripts')

<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register("{{ asset('service-worker.js') }}")
    .then(reg => console.log("SW registrado:", reg))
    .catch(err => console.error("Erro SW:", err));
}
</script>

</body>
</html>
