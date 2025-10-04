 <section class="bg-white p-6 rounded-lg shadow">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informações do Perfil</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Atualize os dados da sua conta.</p>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Coluna esquerda: campos user + profile -->
                    <div class="space-y-4">
                        <!-- Campos da tabela users -->
                        <div>
                            <x-input-label for="name" :value="__('Nome')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                value="{{ old('name', $user->name) }}" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('E-mail')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                value="{{ old('email', $user->email) }}" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Campos da tabela profile -->
                       
                    </div>

                    <!-- Coluna direita: foto -->
                    
                </div>

                <!-- Botão salvar -->
                <div class="flex items-center gap-4 mt-6">
                    <x-primary-button>Salvar</x-primary-button>
                    @if(session('status')==='profile-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition
                           x-init="setTimeout(() => show=false,2000)"
                           class="text-sm text-gray-600 dark:text-gray-400">Salvo.</p>
                    @endif
                </div>
            </form>
        </section>