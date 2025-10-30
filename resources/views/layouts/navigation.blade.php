<aside 
    :class="sidenavOpen ? 'translate-x-0 w-64 xl:relative' : '-translate-x-full xl:translate-x-0 xl:w-20'"
    class="fixed xl:static inset-y-0 left-0 bg-black text-red-600 shadow-xl flex flex-col transition-all duration-300 ease-in-out
           z-50 h-full xl:h-auto flex-shrink-0"
>
    <!-- Botão colapsar (desktop) -->
    <div class="flex justify-end p-2 hidden xl:flex">
        <button @click="sidenavOpen = !sidenavOpen" class="text-white">
             <i class="fas fa-bars text-2xl"></i>
        </button>
    </div>

    <!-- Conteúdo Sidebar -->
    <div class="flex-1 overflow-y-auto mt-2">
        <ul class="flex flex-col pl-0 mb-0">

            <!-- Dashboard -->
            <li class="mt-0.5 w-full">
                <a href="{{ route('dashboard') }}" class="flex items-center py-2.7 px-4 hover:bg-gray-700 transition">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <i class="fa-solid fa-house text-red-600"></i>
                    </div>
                    <span class="ml-2 font-semibold transition-all duration-300"
                          x-show="sidenavOpen"
                          x-transition:enter="transition-all ease-out duration-300"
                          x-transition:enter-start="opacity-0 w-0"
                          x-transition:enter-end="opacity-100 w-auto"
                          x-transition:leave="transition-all ease-in duration-300"
                          x-transition:leave-start="opacity-100 w-auto"
                          x-transition:leave-end="opacity-0 w-0">
                        {{ __('Dashboard') }}
                    </span>
                </a>
            </li>

            <!-- Alunos com submenu -->
            <li x-data="{ open: false }" class="mt-0.5 w-full">
                <button @click="open = !open" class="flex items-center justify-between w-full py-2.7 px-4 rounded-lg hover:bg-gray-700 transition">
                    <div class="flex items-center">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg">
                            <i class="fas fa-users text-red-600"></i>
                        </div>
                        <span class="ml-2 text-red-600 font-semibold transition-all duration-300"
                              x-show="sidenavOpen"
                              x-transition>
                            @if(Auth::user()->hasRole([App\Role::SUPER_ADMIN, App\Role::TREINADOR_GRAU_I, App\Role::TREINADOR_GRAU_II, App\Role::ARBITRATOR]))
                                {{ __('Alunos') }}
                            @else
                                {{ __('Meus Perfis') }}
                            @endif
                        </span>
                    </div>
                    <i :class="{'fa-chevron-down': !open, 'fa-chevron-up': open}" class="fas" x-show="sidenavOpen" x-transition></i>
                </button>

                <ul x-show="open" x-collapse class="ml-6 mt-2 space-y-1" x-cloak>
                    <li>
                        <a href="{{ route('student.index') }}" class="block py-2 px-4 rounded hover:bg-gray-600 transition text-sm text-white">
                            {{ __('Lista de Alunos') }}
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Graduações -->
            @if(Auth::user()->hasRole([App\Role::SUPER_ADMIN]))
            <li class="mt-0.5 w-full">
                <a href="{{ route('graduations.index') }}" class="flex items-center py-2.7 px-4 rounded-lg hover:bg-gray-700 transition">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <i class="fa-solid fa-graduation-cap text-red-500"></i>
                    </div>
                    <span class="ml-2 text-red-600 font-semibold transition-all duration-300" x-show="sidenavOpen" x-transition>{{ __('Graduações') }}</span>
                </a>
            </li>

            <!-- Clubes -->
            <li class="mt-0.5 w-full">
                <a href="{{ route('clubs.index') }}" class="flex items-center py-2.7 px-4 rounded-lg hover:bg-gray-700 transition">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <i class="fa-solid fa-pills text-red-500"></i>
                    </div>
                    <span class="ml-2 text-red-600 font-semibold transition-all duration-300" x-show="sidenavOpen" x-transition>{{ __('Clubes') }}</span>
                </a>
            </li>
            @endif

            <!-- Turmas e Boletos -->
            @if(Auth::user()->hasRole([App\Role::SUPER_ADMIN, App\Role::TREINADOR_GRAU_I, App\Role::TREINADOR_GRAU_II, App\Role::TREINADOR_GRAU_III]))
            <li class="mt-0.5 w-full">
                <a href="{{ route('classes.index') }}" class="flex items-center py-2.7 px-4 rounded-lg hover:bg-gray-700 transition">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <i class="fa-solid fa-chalkboard-user text-red-500"></i>
                    </div>
                    <span class="ml-2 text-red-600 font-semibold transition-all duration-300" x-show="sidenavOpen" x-transition>{{ __('Turmas') }}</span>
                </a>
            </li>

            <li class="mt-0.5 w-full">
                <a href="{{ route('boletos.index') }}" class="flex items-center py-2.7 px-4 rounded-lg hover:bg-gray-700 transition">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <i class="fa-solid fa-money-bill-1-wave text-red-500"></i>
                    </div>
                    <span class="ml-2 text-red-600 font-semibold transition-all duration-300" x-show="sidenavOpen" x-transition>{{ __('Boletos') }}</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</aside>

<!-- Overlay mobile -->
<div 
    x-show="sidenavOpen" 
    @click="sidenavOpen = false" 
    x-transition.opacity
    class="fixed inset-0 bg-black bg-opacity-50 z-40 xl:hidden"
></div>
