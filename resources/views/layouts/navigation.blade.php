 <div class="items-center block w-auto max-h-screen  h-sidenav grow basis-full">
        <ul class="flex flex-col pl-0 mb-0">
              <li class="mt-0.5 w-full">
                <a href="{{ route('student.index') }}" class=" dark:text-white dark:opacity-80 py-2.7 text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap px-4 transition-colors rounded-lg">
                  <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                      <i class="fas fa-users mr-2 text-red-600"></i>
                  </div>
                  <span class="ml-1 duration-300 opacity-100 pointer-events-none ease" style="color: #E62111;">
                  @if(Auth::user()->hasRole([App\Role::SUPER_ADMIN, App\Role::TREINADOR_GRAU_I, App\Role::TREINADOR_GRAU_II, App\Role::ARBITRATOR]))
              
                    {{ __('Alunos') }}

                  @else
                    
                    {{ __('Meus Perfis') }}
                  @endif
                  </span>
                </a>
              </li>
              @if(Auth::user()->hasRole([App\Role::SUPER_ADMIN]))
            <li class="mt-0.5 w-full">
                <a href="{{ route('graduations.index') }}"
                  class="py-2.7 dark:text-white dark:opacity-80 text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 font-semibold text-slate-700 transition-colors">
                <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                  <i class="relative top-0 text-sm leading-normal text-red-500 fa-solid fa-pills"></i>
                </div>
                <span class="ml-1 duration-300 opacity-100 pointer-events-none ease" style="color: #E62111;">
                  {{ __('Graduações') }}
              </a>
            </li>
            <!--<li class="mt-0.5 w-full">
                <a href="#"
                  class="py-2.7 dark:text-white dark:opacity-80 text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 font-semibold text-slate-700 transition-colors">
                <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                  <i class="relative top-0 text-sm leading-normal text-red-500 fa-solid fa-pills"></i>
                </div>
                <span class="ml-1 duration-300 opacity-100 pointer-events-none ease" style="color: #E62111;">
                  {{ __('Faturamento') }}
              </a>
            </li> -->
            <li class="mt-0.5 w-full">
                <a href="{{ route('clubs.index') }}"
                  class="py-2.7 dark:text-white dark:opacity-80 text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 font-semibold text-slate-700 transition-colors">
                <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                  <i class="relative top-0 text-sm leading-normal text-red-500 fa-solid fa-pills"></i>
                </div>
                <span class="ml-1 duration-300 opacity-100 pointer-events-none ease" style="color: #E62111;">
                  {{ __('Clubes') }}
              </a>
            </li>
              @endif
        </ul>
      </div>

