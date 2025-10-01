<section>
<form method="post" action="{{ route('profile.update') }}"  enctype="multipart/form-data">
    @csrf
    @method('patch')
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Profile Information') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __("Update your account's profile information and email address.") }}
            </p>
        </header>

        <section class="mt-6 space-y-6 flex flex-col md:flex-row md:space-x-10">
            <!-- Coluna esquerda: formulário -->
            <div class="flex-1 space-y-6">
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                        :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                        :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div>
                            <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                                {{ __('Your email address is unverified.') }}

                                <button form="send-verification"
                                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Coluna direita: foto + câmera -->
            <div class="flex flex-col items-center space-y-4 md:w-72">
                <x-input-label for="photo" :value="__('Foto de Perfil')" />
                <div style="display:flex; flex-direction: column; align-items: center; gap: 10px;">
                    @php
                        $userPhoto = ($user->photo && $user->photo !== 'default.png') 
                                    ? $user->photo 
                                    : asset('assets/avatars/default.png');
                        $cameraButtonText = ($user->photo && $user->photo !== 'default.png') 
                                            ? 'Foto via Webcam' 
                                            : 'Tirar Minha Foto';
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
        </section>


    <section class = "mt-20">
        
        <div class="flex items-center gap-4 mt-5">
                    <x-primary-button>{{ __('Save') }}</x-primary-button>

                    @if (session('status') === 'profile-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
                    @endif
                </div>
    </section>
    <hr>
</form>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('video');
    const captureBtn = document.getElementById('captureBtn');
    const startCameraBtn = document.getElementById('startCameraBtn');
    const changePhotoBtn = document.getElementById('changePhotoBtn');
    const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
    const fileInput = document.getElementById('fileInput');
    const photoPreview = document.getElementById('photoPreview');
    const photoDataInput = document.getElementById('photo_data');
    const cameraContainer = document.getElementById('cameraContainer');

    const canvas = document.createElement('canvas');
    let stream = null;

    // Função para iniciar a câmera
    async function startCamera() {
        try {
            if (stream) {
                stopCamera();
            }

            // Mostrar o container da câmera antes de tentar acessá-la
            if (cameraContainer) cameraContainer.style.display = 'block';
            if (startCameraBtn) startCameraBtn.style.display = 'none';
            if (captureBtn) captureBtn.style.display = 'inline-block';
            if (photoPreview) photoPreview.style.display = 'none';
            if (changePhotoBtn) changePhotoBtn.style.display = 'none';

            // Acessar a câmera
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 240 },
                    height: { ideal: 240 }
                },
                audio: false
            });

            video.srcObject = stream;

            // Jogar o foco para o botão de captura
            if (captureBtn) {
                captureBtn.focus();
                // Forçar o play do vídeo para garantir que o feedback seja mostrado
                video.play().catch(err => {
                    console.error("Erro ao reproduzir o vídeo:", err);
                });
            }

        } catch (err) {
            console.error("Erro ao acessar a câmera: ", err);
            alert("Não foi possível acessar a câmera. Por favor, verifique as permissões.");
        }
    }

    // Função para parar a câmera
    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        if (video.srcObject) {
            video.srcObject = null;
        }
    }

    // Iniciar câmera quando clicar em "Tirar Minha Foto"
    if (startCameraBtn) {
        startCameraBtn.addEventListener('click', startCamera);
    }

    // Configurar o upload de arquivo
    if (uploadPhotoBtn) {
        uploadPhotoBtn.addEventListener('click', function() {
            fileInput.click();
        });
    }

    // Lidar com a seleção de arquivo
    if (fileInput) {
        fileInput.addEventListener('change', handleFileSelect);
    }

    // Função para lidar com a seleção de arquivo
    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (!file.type.match('image.*')) {
            alert('Por favor, selecione uma imagem válida.');
            return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {
            // Criar uma imagem para redimensionar
            const img = new Image();
            img.onload = function() {
                // Criar um canvas para redimensionar a imagem
                const canvas = document.createElement('canvas');
                const maxSize = 800; // Tamanho máximo da imagem
                let width = img.width;
                let height = img.height;

                // Redimensionar mantendo a proporção
                if (width > height) {
                    if (width > maxSize) {
                        height *= maxSize / width;
                        width = maxSize;
                    }
                } else {
                    if (height > maxSize) {
                        width *= maxSize / height;
                        height = maxSize;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // Converter para base64
                const dataURL = canvas.toDataURL('image/jpeg', 0.9);

                // Atualizar a prévia
                if (photoPreview) {
                    photoPreview.src = dataURL;
                    photoPreview.style.display = 'block';
                }

                // Atualizar o input hidden com os dados da imagem
                if (photoDataInput) {
                    photoDataInput.value = dataURL;
                }

                // Atualizar a interface
                if (changePhotoBtn) changePhotoBtn.style.display = 'inline-block';
                if (uploadPhotoBtn) uploadPhotoBtn.style.display = 'inline-block';
                if (cameraContainer) cameraContainer.style.display = 'none';
                if (captureBtn) captureBtn.style.display = 'none';
                if (startCameraBtn) startCameraBtn.style.display = 'none';
            };
            img.src = e.target.result;
        };

        reader.readAsDataURL(file);
    }

    // Parar a câmera quando o formulário for enviado ou a página for descarregada
    window.addEventListener('beforeunload', stopCamera);

    // Configurar o estado inicial baseado se o usuário já tem foto
    if (photoPreview) {
        const hasPhoto = photoPreview.src && !photoPreview.src.includes('default.png');

        if (hasPhoto) {
            // Se tiver foto, mostrar a foto e o botão de alterar
            if (changePhotoBtn) changePhotoBtn.style.display = 'inline-block';
            if (uploadPhotoBtn) uploadPhotoBtn.style.display = 'inline-block';
            if (photoPreview) photoPreview.style.display = 'block';
            if (startCameraBtn) startCameraBtn.style.display = 'none';
            if (cameraContainer) cameraContainer.style.display = 'none';
            if (captureBtn) captureBtn.style.display = 'none';
        } else {
            // Se for a imagem padrão, mostrar o botão para tirar foto
            if (startCameraBtn) startCameraBtn.style.display = 'inline-block';
            if (uploadPhotoBtn) uploadPhotoBtn.style.display = 'inline-block';
            if (photoPreview) photoPreview.style.display = 'block';
            if (changePhotoBtn) changePhotoBtn.style.display = 'none';
            if (cameraContainer) cameraContainer.style.display = 'none';
            if (captureBtn) captureBtn.style.display = 'none';
        }
    }

    // Alterar foto existente
    if (changePhotoBtn) {
        changePhotoBtn.addEventListener('click', function() {
            if (photoPreview) photoPreview.style.display = 'none';
            if (this) this.style.display = 'none';
            if (cameraContainer) cameraContainer.style.display = 'block';
            if (captureBtn) captureBtn.style.display = 'inline-block';
            startCamera();
        });
    }

    // Capturar foto
    if (captureBtn) {
        captureBtn.addEventListener('click', function() {
            if (!stream) return;

            try {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const context = canvas.getContext('2d');

                // Desenhar a imagem no canvas
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Converter para base64
                const dataURL = canvas.toDataURL('image/png');

                // Atualizar a prévia da foto
                if (photoPreview) {
                    photoPreview.src = dataURL;
                    photoPreview.style.display = 'block';
                }

                // Salvar os dados da imagem
                if (photoDataInput) photoDataInput.value = dataURL;

                // Parar a câmera
                stopCamera();

                // Esconder o container da câmera e o botão de captura
                if (cameraContainer) cameraContainer.style.display = 'none';
                if (this) this.style.display = 'none';

                // Mostrar o botão de alterar foto
                if (changePhotoBtn) {
                    changePhotoBtn.style.display = 'inline-block';
                    changePhotoBtn.focus();
                }

                // Esconder o botão de iniciar câmera
                if (startCameraBtn) startCameraBtn.style.display = 'none';

            } catch (error) {
                console.error("Erro ao capturar a foto:", error);
                alert("Ocorreu um erro ao capturar a foto. Por favor, tente novamente.");
            }
        });
    }
});
</script>

