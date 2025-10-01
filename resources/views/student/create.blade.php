<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Criar Usuário e Perfil') }}
        </h2>
    </x-slot>

    <form action="{{ route('student.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-6">
        @csrf

        {{-- Seção de foto + dados do usuário --}}
            
            {{-- Foto --}}
            <div class="flex-shrink-0 flex flex-col items-center space-y-2 w-full md:w-32">
                <div class="w-32 h-32 rounded-full overflow-hidden border border-gray-300">
                    <img id="photoPreview" src="{{ asset('images/club.png') }}" alt="Preview Foto" class="w-full h-full object-cover">
                </div>
                <input type="file" name="photo" accept="image/*" capture="user" onchange="previewPhoto(event)"
                    class="text-sm text-gray-500 file:border-0 file:bg-gray-200 file:px-3 file:py-1 file:rounded-md file:cursor-pointer">
                <button type="button" onclick="openCamera()" class="text-sm bg-blue-500 text-white px-2 py-1 rounded-md">Usar Câmera</button>
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
                    <label class="block text-sm font-medium">Senha</label>
                    <input type="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
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

    {{-- Scripts --}}
    <script>
        // Pré-visualização do arquivo
        function previewPhoto(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('photoPreview').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        // Abrir câmera do computador
        async function openCamera() {
            try {
                // Solicita acesso à câmera
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });

                if (!stream.getVideoTracks().length) {
                    alert("Nenhuma câmera encontrada no dispositivo.");
                    return;
                }

                // Cria modal
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                modal.innerHTML = `
                    <div class="bg-white p-4 rounded-md flex flex-col items-center space-y-2">
                        <video id="videoCam" autoplay class="w-64 h-48 bg-gray-200 rounded-md"></video>
                        <div class="flex space-x-2">
                            <button id="captureBtn" class="bg-blue-500 text-white px-3 py-1 rounded-md">Capturar</button>
                            <button id="closeBtn" class="bg-gray-300 px-3 py-1 rounded-md">Fechar</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);

                // Configura vídeo
                const video = modal.querySelector('#videoCam');
                video.srcObject = stream;

                // Captura foto
                modal.querySelector('#captureBtn').onclick = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = 200;
                    canvas.height = 200;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    document.getElementById('photoPreview').src = canvas.toDataURL('image/png');

                    // Para a câmera e fecha modal
                    stream.getTracks().forEach(track => track.stop());
                    document.body.removeChild(modal);
                };

                // Fecha modal sem capturar
                modal.querySelector('#closeBtn').onclick = () => {
                    stream.getTracks().forEach(track => track.stop());
                    document.body.removeChild(modal);
                };

            } catch (err) {
                // Tratamento detalhado de erros
                switch (err.name) {
                    case "NotAllowedError":
                        alert("Acesso à câmera negado pelo usuário.");
                        break;
                    case "NotFoundError":
                        alert("Nenhuma câmera encontrada no dispositivo.");
                        break;
                    case "NotReadableError":
                        alert("Não foi possível acessar a câmera. Ela pode estar em uso por outro aplicativo.");
                        break;
                    case "OverconstrainedError":
                        alert("Nenhuma câmera atende às restrições solicitadas.");
                        break;
                    default:
                        alert("Erro ao acessar a câmera: " + err.message);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const emailInput = document.getElementById('email');
            const feedback = document.getElementById('emailFeedback');
            if (!emailInput) return;

            emailInput.addEventListener('blur', async () => {
                const email = emailInput.value.trim();
                if (!email) {
                    feedback.textContent = '';
                    return;
                }

                try {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch("{{ route('check-email') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ email })
                    });
                    const data = await response.json();

                    if (data.exists) {
                        feedback.textContent = "Este e-mail já está cadastrado.";
                        feedback.className = "text-sm text-red-500 mt-1";
                    } else {
                        feedback.textContent = "E-mail disponível.";
                        feedback.className = "text-sm text-green-500 mt-1";
                    }

                } catch (err) {
                    console.error(err);
                    feedback.textContent = "Erro ao verificar e-mail.";
                    feedback.className = "text-sm text-yellow-500 mt-1";
                }
            });
        });

    </script>
    


</x-app-layout>
