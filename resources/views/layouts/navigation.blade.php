<aside 
    id="main-sidenav"
    x-data="{ openMenu: null }"
    class="fixed inset-y-0 left-0 bg-black text-red-600 shadow-xl mt-8 flex flex-col transform transition-transform duration-300 ease-in-out z-40"
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
                <ul class="sub-menu bg-black/80 mt-1 rounded-sm overflow-hidden" x-show="openMenu === 'menuClanovi'" x-cloak x-transition>
                    <li id="clanoviaspx"><a href="{{route('members.index')}}" class="block px-3 py-2 text-white">Membros</a></li>
                    <li id="leadsaspx"><a href="#" class="block px-3 py-2 text-white">Leads</a></li>
                    <li id="familijeaspx"><a href="#" class="block px-3 py-2 text-white">Famílias</a></li>
                    <li id="grupeaspx"><a href="#" class="block px-3 py-2 text-white">Grupos</a></li>
                    <li id="poljaaspx"><a href="#" class="block px-3 py-2 text-white">Campos</a></li>
                    <li id="karticeaspx"><a href="#" class="block px-3 py-2 text-white">Cartões</a></li>
                    <li id="mjerenjaaspx"><a href="#" class="block px-3 py-2 text-white">Medições</a></li>
                    <li id="gdpraspx"><a href="#" class="block px-3 py-2 text-white">GDPR</a></li>
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
                <ul class="sub-menu bg-black/80 mt-1 rounded-sm overflow-hidden" x-show="openMenu === 'menuClasses'" x-cloak x-transition>
                    <li id="classesaspx"><a href="#" class="block px-3 py-2 text-white">Treinamentos</a></li>
                    <li id="scheduleaspx"><a href="#" class="block px-3 py-2 text-white">Horário</a></li>
                    <li id="prisutnostaspx"><a href="#" class="block px-3 py-2 text-white">A frequência das aulas</a></li>
                    <li id="feedbacksaspx"><a href="#" class="block px-3 py-2 text-white">Comentários</a></li>
                    <li id="treneriaspx"><a href="#" class="block px-3 py-2 text-white">Treinadores</a></li>
                    <li id="trainingplansaspx"><a href="#" class="block px-3 py-2 text-white">Planos de treinamento</a></li>
                    <li id="bookingaspx"><a href="#" class="block px-3 py-2 text-white">Formulários de reserva</a></li>
                    <li id="bookingclassaspx"><a href="#" class="block px-3 py-2 text-white">Próximas treinamentos</a></li>
                    <li id="eventsaspx"><a href="#" class="block px-3 py-2 text-white">Eventos</a></li>
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
                <ul class="sub-menu bg-black/80 mt-1 rounded-sm overflow-hidden" x-show="openMenu === 'menuFinance'" x-cloak x-transition>
                    <li id="racuniaspx"><a href="#" class="block px-3 py-2 text-white">Faturas</a></li>
                    <li id="racunidodajaspx"><a href="#" class="block px-3 py-2 text-white">Pagamentos</a></li>
                    <li id="financijeaspx"><a href="#" class="block px-3 py-2 text-white">Custos e receitas</a></li>
                    <li id="feesaspx"><a href="#" class="block px-3 py-2 text-white">Pacotes de assinatura</a></li>
                    <li id="poreziaspx"><a href="#" class="block px-3 py-2 text-white">Impostos</a></li>
                    <li id="racuniknjigaaspx"><a href="#" class="block px-3 py-2 text-white">Facturas recebidas</a></li>
                    <li id="planaspx"><a href="#" class="block px-3 py-2 text-white">Planos financeiros</a></li>
                    <li id="financijevrsteaspx"><a href="#" class="block px-3 py-2 text-white">Configurações</a></li>
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
                <ul class="sub-menu bg-black/80 mt-1 rounded-sm overflow-hidden" x-show="openMenu === 'menuAchievements'" x-cloak x-transition>
                    <li id="pojaseviaspx"><a href="#" class="block px-3 py-2 text-white">Promoções</a></li>
                    <li id="kyudanaspx"><a href="#" class="block px-3 py-2 text-white">Sistema de cinto</a></li>
                    <li id="diplomeaspx"><a href="#" class="block px-3 py-2 text-white">Certificados</a></li>
                    <li id="rezultatiaspx"><a href="#" class="block px-3 py-2 text-white">Competições</a></li>
                    <li id="disciplineaspx"><a href="#" class="block px-3 py-2 text-white">Categorias</a></li>
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