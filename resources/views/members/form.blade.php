<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($member) ? __('Editar Usuário e Perfil') : __('Criar Usuário e Perfil') }}
        </h2>
    </x-slot>

    <form action="{{ isset($member) ? route('members.update', $member) : route('members.store') }}"
          method="POST" enctype="multipart/form-data" class="p-6 space-y-8 bg-white rounded ">
        @csrf
        @if(isset($member))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Dados Pessoais -->
            <div class="space-y-6">
                <x-card title="Dados Pessoais">
                    <div class="flex items-center space-x-4">
                    <!-- inicio foto -->
                     
                    <div class="flex-shrink-0 flex flex-col items-center space-y-2 w-full">
                        <div style="display:flex; flex-direction: column; align-items: center; gap: 10px;">
                            @php
                                $userPhoto =  asset('assets/avatars/default.png');
                                $cameraButtonText = "Foto via Webcam";
                            @endphp

                            <div class="flex flex-col items-center space-y-4">
                                <!-- Foto do usuário -->
                                @if(isset($member) && !empty($member->name))
                                    @php
                                        $initials = collect(explode(' ', $member->name))->map(fn($n) => mb_substr($n,0,1))->join('');
                                    @endphp
                                    <div id="photoPreview" 
                                         class="rounded-full w-32 h-32 flex items-center justify-center bg-gray-300 text-4xl font-bold text-gray-700 border-2 border-gray-300 select-none transition-all duration-200">
                                        {{ $initials }}
                                    </div>
                                @else
                                    <div id="photoPreview" 
                                         class="rounded-full w-32 h-32 flex items-center justify-center bg-gray-300 text-4xl font-bold text-gray-700 border-2 border-gray-300 select-none transition-all duration-200">
                                        ?
                                    </div>
                                @endif

                                <!-- Container da câmera (começa escondido) -->
                                <div id="cameraContainer" class="hidden">
                                    <video id="video" autoplay playsinline
                                        class="rounded-full w-32 h-32 object-cover border-2 border-gray-300 bg-gray-100"></video>
                                </div>

                                <!-- Botões -->
                                <div class="flex flex-wrap gap-2 justify-center w-full items-center mt-2">
                                    <!-- Iniciar câmera -->
                                    <button type="button" id="startCameraBtn"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-700 text-sm">
                                        {{ $cameraButtonText }}
                                    </button>

                                    <!-- Capturar foto (começa escondido) -->
                                    <button type="button" id="captureBtn"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm hidden">
                                        Capturar Foto
                                    </button>

                                    <!-- Enviar foto -->
                                    <button type="button" id="uploadPhotoBtn"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-700 text-sm">
                                        Enviar Foto
                                    </button>

                                    <input type="file" id="fileInput" accept="image/*" class="hidden">
                                </div>
                            </div>

                        </div>
                        <input type="hidden" name="photo_data" id="photo_data" />
                    </div>
                    
                    <!-- fim foto -->
                    
                        
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" checked>
                                <span>Ativo</span>
                            </label>
                        </div>
                        <x-input label="Nome" name="ctl00$MainContent$FormView1$imeTextBox" value="{{ old('ctl00$MainContent$FormView1$imeTextBox', $member->name ?? '') }}" />
                        <x-select label="Sexo" name="ctl00$MainContent$FormView1$drpSpol" :options="[['True','Masculino'],['False','Feminino'],['','Outro']]" selected="True" />
                        <x-input label="ID membro" name="ctl00$MainContent$FormView1$broj1TextBox" value="{{ old('ctl00$MainContent$FormView1$broj1TextBox', $member->number_kak ?? '') }}" />
                        <x-input label="ID 2" name="ctl00$MainContent$FormView1$broj2TextBox" value="1491" />
                        <x-input label="Data de nascimento" name="ctl00$MainContent$FormView1$RadDatePicker2$dateInput" value="{{ old('ctl00$MainContent$FormView1$RadDatePicker2$dateInput', $member->birth_date ?? '') }}" />
                        <x-input label="SSN" name="ctl00$MainContent$FormView1$jmbgTextBox" />
                        <x-input label="Adjunto" name="ctl00$MainContent$FormView1$radDate$dateInput" value="01/05/2010" />
                        <x-input label="Data de cancelamento" name="ctl00$MainContent$FormView1$RadDatePicker1$dateInput" />
                    </div>
                </x-card>

                <x-card title="Contato">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input label="Gsm" name="ctl00$MainContent$FormView1$smsTextBox" value="919297093" />
                        <x-input label="E-mail" name="ctl00$MainContent$FormView1$emailTextBox" value="micaelc.santos@gmail.com" />
                        <x-input label="Telefone" name="ctl00$MainContent$FormView1$telefonTextBox" value="968739739" />
                    </div>
                </x-card>

                <x-card title="Endereço">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input label="Endereço" name="ctl00$MainContent$FormView1$adresaTextBox" value="{{ old('ctl00$MainContent$FormView1$adresaTextBox', $member->address ?? '') }}" />
                        <x-input label="Cidade" name="ctl00$MainContent$FormView1$gradTextBox" value="Cavadas" />
                        <x-input label="CEP" name="ctl00$MainContent$FormView1$zipTextBox" value="3105-160" />
                        <x-input label="Região" name="ctl00$MainContent$FormView1$republikaTextBox" />
                    </div>
                </x-card>

                <x-card title="Pais">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input label="Nome da mãe" name="ctl00$MainContent$FormView1$imemajkeTextBox" value="Maria Natália Mendes Cordeiro" />
                        <x-input label="Gsm mãe" name="ctl00$MainContent$FormView1$telmajkeTextBox" />
                        <x-input label="Nome do pai" name="ctl00$MainContent$FormView1$imeocaTextBox" value="Carlos Manuel Martinho dos Santos" />
                        <x-input label="Gsm pai" name="ctl00$MainContent$FormView1$telocaTextBox" />
                    </div>
                </x-card>

               <x-card title="Outro">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input label="Tamanho do cinturão" name="ctl00$MainContent$FormView1$velicinaPTextBox1" />
                        <x-input label="Código de barras" name="ctl00$MainContent$FormView1$barcodeTextBox1" />
                        <x-input label="Mensagem ao acessar" name="ctl00$MainContent$FormView1$txtCheckInMsg" />
                    </div>
                </x-card>

                <x-card title="Notas">
                    <textarea name="ctl00$MainContent$FormView1$biljeskeTextBox" rows="2" class="w-full border rounded p-2">Emergency contact: 919297093; Status: 2022</textarea>
                </x-card>
            </div>

            <!-- Dados Complementares -->
            <div class="space-y-6">
                <x-card title="Documentos e Identificação">
                    <x-input label="N.º FNKP" name="ctl00$MainContent$repPolja$ctl01$radNumPolje" value="{{ old('ctl00$MainContent$repPolja$ctl01$radNumPolje', $member->number_fnkp ?? '') }}" />
                    <x-input label="N.º TPTD" name="ctl00$MainContent$repPolja$ctl02$radNumPolje" />
                    <x-input label="N.º JKS" name="ctl00$MainContent$repPolja$ctl03$radNumPolje" />
                    <x-input label="N.º Contribuinte" name="ctl00$MainContent$repPolja$ctl04$radNumPolje" value="244 373 345,00" />
                    <x-select label="Árbitro" name="ctl00$MainContent$repPolja$ctl05$drpPolje" :options="[['',''],['7011','Oficial de Mesa'],['7012','Juiz'],['7013','Árbitro B'],['7014','Árbitro A']]" selected="" />
                    <x-select label="Grau de Treinador" name="ctl00$MainContent$repPolja$ctl06$drpPolje" :options="[['',''],['7015','Grau I'],['7016','Grau II'],['7017','Grau III'],['7018','Grau IV']]" selected="" />
                    <x-select label="Documento de Identificação" name="ctl00$MainContent$repPolja$ctl07$drpPolje" :options="[['',''],['7085','Cartão de Cidadão'],['7086','Passaporte'],['7087','Título de Residência'],['7088','Outro']]" selected="7085" />
                    <x-input label="N.º Doc. Identificação" name="ctl00$MainContent$repPolja$ctl08$radNumPolje" value="{{ old('ctl00$MainContent$repPolja$ctl08$radNumPolje', $member->document_number ?? '') }}" />
                </x-card>

                <x-card title="Cintos">
                    <ul class="list-disc pl-5">
                        <li>Branco - 9º Kyu <span class="text-xs text-gray-500">20/10/2001</span></li>
                        <li>Adultos Amarelo - 8º Kyu <span class="text-xs text-gray-500">07/07/2002</span></li>
                    </ul>
                </x-card>

                <x-card title="Grupos/treinamentos">
                    <table class="w-full text-sm border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th>#</th>
                                <th>Grupo</th>
                                <th>Assinatura</th>
                                <th>Presença</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1.</td>
                                <td>Adultos Iniciados</td>
                                <td>Data de início: 01/09/2022</td>
                                <td>69%</td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Adultos Intermédios e Avançados</td>
                                <td>Data de início: 01/09/2022</td>
                                <td>62%</td>
                            </tr>
                            <!-- Adicione os demais grupos conforme necessário -->
                        </tbody>
                    </table>
                </x-card>

                <x-card title="Pagamentos">
                    <ul class="list-disc pl-5">
                        <li>15,00 - 22/01/2025 - Cota Treinador FNKP - Iniciado</li>
                        <li>50,00 - 01/01/2025 - Cota Anual Praticante KAK - KA</li>
                        <li>50,00 - 01/01/2024 - Cota Anual Praticante KAK - KA</li>
                        <li>10,00 - 24/04/2023 - Cota Membro JKS - JKS Membro</li>
                    </ul>
                    <div class="mt-2 text-center text-lg font-bold text-green-600">
                        Devido total atual: 0,00
                    </div>
                </x-card>

                 <x-card title="Família">
                    <x-select label="Família" name="ctl00$MainContent$FormView1$drpParent" :options="[['',' '],['0','<Adicionar família>'],['21107','Família Ascenso Costa'],['19311','Familia Leal'],['25619','Familia Motta'],['20598','Familia Neves'],['20127','Familia Nunes dos Santos'],['19323','Familia Pinheiro da Cruz'],['20451','Família Santos'],['19314','Familia Varela']]" selected="" />
                </x-card>

                <x-card title="Condições médicas">
                    <x-input label="Condições médicas" name="ctl00$MainContent$FormView1$medicalconTextBox1" />
                    <x-input label="Exame médico válido até" name="ctl00$MainContent$FormView1$RadDatePicker3$dateInput" />
                </x-card>

                
            </div>
        </div>

        <!-- Botões Salvar/Excluir -->
        <div class="flex flex-wrap gap-4 justify-end md:justify-end mt-8">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700 transition-all duration-150">
                Salvar
            </button>
            <button type="button" class="bg-orange-500 text-white px-6 py-2 rounded shadow hover:bg-orange-600 transition-all duration-150">
                Excluir
            </button>
        </div>
    </form>

    @push('scripts')
        @vite('resources/js/camera.js')
    @endpush
</x-app-layout>