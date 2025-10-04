<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Criar Usuário e Perfil') }}
        </h2>
    </x-slot>

    <form 
        action="{{ route('student.update', $profile->id) }}" 
        method="POST" 
        enctype="multipart/form-data"
        class="p-6 bg-white dark:bg-gray-800 shadow-lg rounded-xl space-y-8"
    >
        @csrf
        @method('PUT')

        {{-- Foto + Dados do Usuário --}}
        <div class="flex flex-col md:flex-row gap-8 items-start md:items-center">
            {{-- Foto --}}
            <div class="flex flex-col items-center gap-4 w-full md:w-1/3">
                @php
                    $profilePhoto = ($profile->photo && $profile->photo !== 'default.png') 
                                ? $profile->photo 
                                : asset('assets/avatars/default.png');
                    $cameraButtonText = ($profile->photo && $profile->photo !== 'default.png') 
                                        ? 'Foto via Webcam' 
                                        : 'Tirar Minha Foto';
                @endphp

                <div class="flex flex-col items-center gap-4">
                    <!-- Foto -->
                    <img 
                        id="photoPreview"
                        src="{{ $profilePhoto }}"
                        alt="Foto do usuário"
                        onerror="this.onerror=null; this.src='{{ asset('assets/avatars/default.png') }}'"
                        class="rounded-full w-56 h-56 object-cover border-4 border-gray-300 dark:border-gray-600 shadow-md"
                    >

                    <!-- Container da câmera -->
                    <div id="cameraContainer" class="hidden">
                        <video id="video" autoplay playsinline
                            class="rounded-full w-56 h-56 object-cover border-2 border-gray-300 bg-gray-100"></video>
                    </div>

                    <!-- Botões -->
                    <div class="flex flex-col space-y-3 w-full items-center">
                        <x-primary-button id="startCameraBtn" type="button"  class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                            {{ $cameraButtonText }}
                        </x-primary-button>

                        <x-primary-button id="captureBtn" type="button"  class="hidden bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                            Capturar Foto
                        </x-primary-button>

                        <x-primary-button id="uploadPhotoBtn" type="button"  class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                            Enviar Foto
                        </x-primary-button>

                        <input type="file" id="fileInput" accept="image/*" class="hidden">
                    </div>
                </div>

                <input type="hidden" name="photo_data" id="photo_data" />
            </div>

            {{-- Campos principais do usuário --}}
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Numero KAK</label>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mt-3">{{ $profile->number_kak }}</label>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">E-mail</label>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mt-3">{{ $profile->user->email }}</label>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nome</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ $profile->name }}" 
                        required
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Clube</label>
                    <select 
                        name="club_id" 
                        @if(!auth()->user()->hasAnyRole([
                            App\Role::TREINADOR_GRAU_I->value,
                            App\Role::TREINADOR_GRAU_II->value,
                            App\Role::TREINADOR_GRAU_III->value,
                            App\Role::ARBITRATOR->value,
                            App\Role::SUPER_ADMIN->value,
                        ])); disabled @endif
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm p-2 bg-white dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Selecione</option>
                        @foreach ($clubs as $clube)
                            <option value="{{ $clube->id }}" {{ $profile->club_id == $clube->id ? 'selected' : '' }}>
                                {{ $clube->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Campos Profile --}}
        <div class="pt-4 border-t border-gray-300 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Dados do Perfil</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ([
                    'number_fnkp' => 'Número FNKP',
                    'admission_date' => 'Data de Admissão',
                    'father_name' => 'Nome do Pai',
                    'mother_name' => 'Nome da Mãe',
                    'birth_date' => 'Data de Nascimento',
                    'nationality' => 'Nacionalidade',
                    'profession' => 'Profissão',
                    'address' => 'Endereço',
                    'city' => 'Cidade',
                    'district' => 'Distrito',
                    'phone_number' => 'Telefone',
                    'cell_number' => 'Telemóvel',
                    'contact' => 'Contato de Emergência',
                    'contact_number' => 'Número do Contato',
                    'contact_email' => 'Email do Contato',
                ] as $field => $label)
                    <div @class(['md:col-span-2' => $field === 'address'])>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $label }}</label>

                        @if($field === 'number_fnkp' || $field == 'admission_date')
                            {{-- Apenas exibe o valor --}}
                            <p class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200 p-2">
                                {{ $profile->$field ?? '—' }}
                            </p>
                        @else
                            <input 
                                type="{{ in_array($field, ['birth_date']) ? 'date' : (str_contains($field, 'email') ? 'email' : 'text') }}" 
                                name="{{ $field }}"
                                value="{{ $profile->$field }}"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            >
                        @endif
                    </div>
                @endforeach

                {{-- Número do Documento + Tipo do Documento lado a lado --}}
                <div class="flex gap-4 md:col-span-2">
                   

                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Tipo de Documento</label>
                        <select 
                            name="document_type" 
                            class="mt-1 block w-full p-2 border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">Selecione</option>
                            @foreach (App\DocumentoIdentificacao::cases() as $docType)
                                <option value="{{ $docType->value }}" {{ $profile->document_type == $docType->value ? 'selected' : '' }}>
                                    {{ $docType->value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                     <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Número do Documento</label>
                        <input 
                            type="text" 
                            name="document_number" 
                            value="{{ $profile->document_number }}" 
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        >
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Observações</label>
                    <textarea 
                        name="observations" 
                        rows="3"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                    >{{ $profile->observations }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-6">
            <x-primary-button class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                {{ __('Salvar') }}
            </x-primary-button>
        </div>
    </form>

    <script>
document.addEventListener('DOMContentLoaded', () => {
    const startCameraBtn = document.getElementById('startCameraBtn');
    const captureBtn = document.getElementById('captureBtn');
    const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const video = document.getElementById('video');
    const photoPreview = document.getElementById('photoPreview');
    const fileInput = document.getElementById('fileInput');
    const photoDataInput = document.getElementById('photo_data');

    let stream;

    // Iniciar câmera
    startCameraBtn.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
            cameraContainer.classList.remove('hidden');
            photoPreview.classList.add('hidden');
            captureBtn.classList.remove('hidden');
            startCameraBtn.classList.add('hidden');
        } catch (err) {
            alert('Erro ao acessar a câmera: ' + err.message);
            console.error(err);
        }
    });

    // Capturar imagem
    captureBtn.addEventListener('click', () => {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataUrl = canvas.toDataURL('image/png');

        photoPreview.src = dataUrl;
        photoPreview.classList.remove('hidden');
        cameraContainer.classList.add('hidden');
        captureBtn.classList.add('hidden');
        startCameraBtn.classList.remove('hidden');

        // Parar vídeo
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }

        // Salvar imagem no input hidden
        photoDataInput.value = dataUrl;
    });

    // Upload manual de arquivo
    uploadPhotoBtn.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.src = e.target.result;
            photoPreview.classList.remove('hidden');
            cameraContainer.classList.add('hidden');
            captureBtn.classList.add('hidden');
            startCameraBtn.classList.remove('hidden');
            photoDataInput.value = e.target.result;
        };
        reader.readAsDataURL(file);
    });
});
</script>

</x-app-layout>
