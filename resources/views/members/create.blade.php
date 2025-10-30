<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Criar Usuário e Perfil') }}
        </h2>
    </x-slot>

    <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-6">
        @csrf

        {{-- Seção de foto + dados do usuário --}}
            
            {{-- Foto --}}
            <div class="flex-shrink-0 flex flex-col items-center space-y-2 w-full md:w-32">
                <x-input-label for="photo" :value="__('Foto de Perfil')" />
                <div style="display:flex; flex-direction: column; align-items: center; gap: 10px;">
                    @php
                        $userPhoto =  asset('assets/avatars/default.png');
                        $cameraButtonText = "Foto via Webcam";
                                            
                    @endphp

                    <div class="flex flex-col items-center space-y-4">
                        <!-- Foto do usuário -->
                        <img id="photoPreview" src="{{ $userPhoto }}" alt="Foto do usuário"
                            onerror="this.onerror=null; this.src='{{ asset('assets/avatars/default.png') }}'"
                            class="rounded-full w-60 h-60 object-cover border-2 border-gray-300">

                        <!-- Container da câmera (começa escondido) -->
                        <div id="cameraContainer" class="hidden">
                            <video id="video" autoplay playsinline
                                class="rounded-full w-60 h-60 object-cover border-2 border-gray-300 bg-gray-100"></video>
                        </div>

                        <!-- Botões -->
                        <div class="flex flex-col space-y-2 w-full items-center">
                            <!-- Iniciar câmera -->
                            <button type="button" id="startCameraBtn"
                                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">
                                {{ $cameraButtonText }}
                            </button>

                            <!-- Capturar foto (começa escondido) -->
                            <button type="button" id="captureBtn"
                                class="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 hidden">
                                Capturar Foto
                            </button>

                            <!-- Enviar foto -->
                            <button type="button" id="uploadPhotoBtn"
                                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">
                                Enviar Foto
                            </button>

                            <input type="file" id="fileInput" accept="image/*" class="hidden">
                        </div>
                    </div>

                </div>
                <input type="hidden" name="photo_data" id="photo_data" />
            </div>

            {{-- Campos principais do usuário --}}
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3 w-full">
                <div>
                    <label class="block text-sm font-medium">Nome</label>
                    <input type="text" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">E-mail</label>
                    <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <p id="emailFeedback" class="text-sm mt-1"></p>
                </div>
                
                 <div>
                    <label class="block text-sm font-medium">Clube</label>
                    <select name="club_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white p-2">
                        <option value="">Selecione</option>
                        @foreach ($clubs as $clube)
                            <option value="{{ $clube->id }}">{{ $clube->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        {{-- Campos Profile --}}
        <h3 class="text-lg font-bold border-b pb-1">Dados do Perfil</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium">Número FNKP</label>
                <input type="text" name="number_fnkp" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium">Data de Admissão</label>
                <input type="date" name="admission_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium">Nome do Pai</label>
                <input type="text" name="father_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium">Nome da Mãe</label>
                <input type="text" name="mother_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
           <div>
                <label class="block text-sm font-medium">Tipo de Documento</label>
                <select name="document_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white p-2">
                    <option value="">Selecione</option>
                    @foreach (App\DocumentoIdentificacao::cases() as $docType)
                        <option value="{{ $docType->value }}">{{ $docType->value }}</option>
                    @endforeach
                </select>
            </div>

           

            <div>
                <label class="block text-sm font-medium">Número do Documento</label>
                <input type="text" name="document_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium">Data de Nascimento</label>
                <input type="date" name="birth_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium">Nacionalidade</label>
                <input type="text" name="nationality" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium">Profissão</label>
                <input type="text" name="profession" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Endereço</label>
                <input type="text" name="address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium">Cidade</label>
                <input type="text" name="city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium">Distrito</label>
                <input type="text" name="district" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium">Telefone</label>
                <input type="text" name="phone_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium">Telemóvel</label>
                <input type="text" name="cell_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium">Contato de Emergência</label>
                <input type="text" name="contact" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium">Número do Contato</label>
                <input type="text" name="contact_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium">Email do Contato</label>
                <input type="email" name="contact_email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Observações</label>
                <textarea name="observations" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <x-primary-button>{{ __('Salvar') }}</x-primary-button>
        </div>
    </form>

  @push('scripts')
    @vite('resources/js/camera.js')
@endpush
    


</x-app-layout>
