<aside 
    id="main-sidenav"
    x-data="{ openMenu: null }"
    class="fixed inset-y-0 left-0 w-64 xl:w-72 bg-black text-red-600 shadow-xl mt-8 flex flex-col transform transition-transform duration-300 ease-in-out z-40"
    :class="sidenavOpen ? 'translate-x-0' : '-translate-x-full'"
>

    <div class="flex-1 overflow-y-auto pt-2 px-2 mt-8">
        <!-- Menu replicando a hierarquia fornecida.
             Ícones usam FontAwesome; todos os links apontam para '#' conforme solicitado. -->
        <ul id="ulNav" class="nav-links  mt-6 list-none m-0 p-0 space-y-1">
            <li>
                <a href="{{route('dashboard')}}" class="flex items-center text-white px-3 py-2 rounded hover:bg-gray-800">
                    <i class="fas fa-th-large w-6 text-center"></i>
                    <span class="ml-3 link_name">Painel</span>
                </a>
                <ul class="sub-menu blank" x-show="openMenu === ''" x-cloak x-transition.opacity>
                    <li><a class="link_name" href="{{route('dashboard')}}">Painel</a></li>
                </ul>
            </li>

            <li id="menuClanovi">
                <div class="iocn-link flex items-center justify-between cursor-pointer px-3 py-2 rounded hover:bg-gray-800"
                     @click.prevent="openMenu = (openMenu === 'menuClanovi' ? null : 'menuClanovi')"
                     :aria-expanded="(openMenu === 'menuClanovi').toString()"
                     role="button"
                     tabindex="0"
                >
                    <div class="flex items-center">
                        <a href="#" class="flex items-center text-white">
                            <i class="fas fa-user w-6 text-center"></i>
                            <span class="ml-3 link_name">Membros</span>
                        </a>
                    </div>
                    <i class="fas fa-chevron-down arrow text-white" :class="openMenu === 'menuClanovi' ? 'rotate-180' : ''"></i>
                </div>

                <!-- submenu stays inside li and is hidden until opened -->
                <ul class="sub-menu mt-2 ml-2 pl-8 pr-2 py-2 bg-gray-900/80 rounded-md ring-1 ring-white/10 backdrop-blur-sm space-y-1"
                    x-show="openMenu === 'menuClanovi'" x-cloak x-transition>
                    <li id="clanoviaspx">
                        <a href="{{route('members.index')}}" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition">
                            <span>Membros</span>
                        </a>
                    </li>
                    <li id="familijeaspx">
                        <a href="{{ route('familias.index') }}" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition">
                            <span>Famílias</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li id="menuClasses">
                <div class="iocn-link flex items-center justify-between cursor-pointer px-3 py-2 rounded hover:bg-gray-800"
                     @click.prevent="openMenu = (openMenu === 'menuClasses' ? null : 'menuClasses')"
                     :aria-expanded="(openMenu === 'menuClasses').toString()"
                     role="button"
                     tabindex="0"
                >
                    <div class="flex items-center">
                        <a href="#" class="flex items-center text-white">
                            <i class="fas fa-tasks w-6 text-center"></i>
                            <span class="ml-3 link_name">Treinamentos</span>
                        </a>
                    </div>
                    <i class="fas fa-chevron-down arrow text-white" :class="openMenu === 'menuClasses' ? 'rotate-180' : ''"></i>
                </div>
                <ul class="sub-menu mt-2 ml-2 pl-8 pr-2 py-2 bg-gray-900/80 rounded-md ring-1 ring-white/10 backdrop-blur-sm space-y-1"
                    x-show="openMenu === 'menuClasses'" x-cloak x-transition>
                    <li id="classesaspx">
                        <a href="{{route('classes.index')}}" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition">
                            <span>Treinamentos</span>
                        </a>
                    </li>
                    <li id="scheduleaspx">
                        <a href="{{route('classes.schedule')}}" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition">
                            <span>Horário</span>
                        </a>
                    </li>
                    <li id="prisutnostaspx">
                        <a href="{{route('classes.attendance.view')}}" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition">
                            <span>A frequência das aulas</span>
                        </a>
                    </li>
                    <li id="treneriaspx">
                        <a href="{{ route('trainers.index') }}" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition">
                            <span>Treinadores</span>
                        </a>
                    </li>
                    <li id="eventsaspx">
                        <a href="{{route('schedule.index')}}" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition">
                            <span>Eventos</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li id="menuFinance">
                <div class="iocn-link flex items-center justify-between cursor-pointer px-3 py-2 rounded hover:bg-gray-800"
                     @click.prevent="openMenu = (openMenu === 'menuFinance' ? null : 'menuFinance')"
                     :aria-expanded="(openMenu === 'menuFinance').toString()"
                     role="button"
                     tabindex="0"
                >
                    <div class="flex items-center">
                        <a href="#" class="flex items-center text-white">
                            <i class="fas fa-dollar-sign w-6 text-center"></i>
                            <span class="ml-3 link_name">Finanças</span>
                        </a>
                    </div>
                    <i class="fas fa-chevron-down arrow text-white" :class="openMenu === 'menuFinance' ? 'rotate-180' : ''"></i>
                </div>
                <ul class="sub-menu mt-2 ml-2 pl-8 pr-2 py-2 bg-gray-900/80 rounded-md ring-1 ring-white/10 backdrop-blur-sm space-y-1"
                    x-show="openMenu === 'menuFinance'" x-cloak x-transition>
                    <li id="racuniaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Faturas</span></a></li>
                    <li id="racunidodajaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Pagamentos</span></a></li>
                    <li id="financijeaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Custos e receitas</span></a></li>
                    <li id="feesaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Pacotes de assinatura</span></a></li>
                    <li id="poreziaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Impostos</span></a></li>
                    <li id="racuniknjigaaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Facturas recebidas</span></a></li>
                </ul>
            </li>

            <li id="menuAchievements">
                <div class="iocn-link flex items-center justify-between cursor-pointer px-3 py-2 rounded hover:bg-gray-800"
                     @click.prevent="openMenu = (openMenu === 'menuAchievements' ? null : 'menuAchievements')"
                     :aria-expanded="(openMenu === 'menuAchievements').toString()"
                     role="button"
                     tabindex="0"
                >
                    <div class="flex items-center">
                        <a href="#" class="flex items-center text-white">
                            <i class="fas fa-award w-6 text-center"></i>
                            <span class="ml-3 link_name">Realizações</span>
                        </a>
                    </div>
                    <i class="fas fa-chevron-down arrow text-white" :class="openMenu === 'menuAchievements' ? 'rotate-180' : ''"></i>
                </div>
                <ul class="sub-menu mt-2 ml-2 pl-8 pr-2 py-2 bg-gray-900/80 rounded-md ring-1 ring-white/10 backdrop-blur-sm space-y-1"
                    x-show="openMenu === 'menuAchievements'" x-cloak x-transition>
                    <li id="diplomeaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Certificados</span></a></li>
                    <li id="rezultatiaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Competições</span></a></li>
                </ul>
            </li>

            <li id="menuConfigurations">
                <div class="iocn-link flex items-center justify-between cursor-pointer px-3 py-2 rounded hover:bg-gray-800"
                     @click.prevent="openMenu = (openMenu === 'menuConfigurations' ? null : 'menuConfigurations')"
                     :aria-expanded="(openMenu === 'menuConfigurations').toString()"
                     role="button"
                     tabindex="0"
                >
                    <div class="flex items-center">
                        <a href="#" class="flex items-center text-white">
                            <i class="fas fa-cog w-6 text-center"></i>
                            <span class="ml-3 link_name">Configurações</span>
                        </a>
                    </div>
                    <i class="fas fa-chevron-down arrow text-white" :class="openMenu === 'menuConfigurations' ? 'rotate-180' : ''"></i>
                </div>
                <ul class="sub-menu mt-2 ml-2 pl-8 pr-2 py-2 bg-gray-900/80 rounded-md ring-1 ring-white/10 backdrop-blur-sm space-y-1"
                    x-show="openMenu === 'menuConfigurations'" x-cloak x-transition>
                    <li id="pojaseviaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Estilos</span></a></li>
                    <li id="kyudanaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Graduações</span></a></li>
                    <li id="diplomeaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Parâmetros de classificação</span></a></li>
                    <li id="rezultatiaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Sistema de cinto</span></a></li>
                    <li id="disciplineaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Categorias</span></a></li>
                    <li id="velicineaspx"><a href="#" class="flex items-center px-3 py-2 rounded text-sm text-gray-200 hover:bg-white/10 hover:text-white transition"><span>Tipos de transações</span></a></li>
                    Tipos de receitas
                    Tipos de despesas
                </ul>
            </li>

           
        </ul>
    </div>
</aside>

<!-- Overlay (mobile) -->
        <div 
            x-show="sidenavOpen" 
            @click="sidenavOpen = false" 
            x-transition.opacity 
            class="fixed inset-0 bg-black bg-opacity-50 z-30 xl:hidden">
        </div>