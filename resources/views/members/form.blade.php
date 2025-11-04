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
                        <div>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" checked>
                                <span>Ativo</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- fim foto -->
                    
                        
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        
                        <x-input label="Nome" name="name" value="{{ old('ctl00$MainContent$FormView1$imeTextBox', $member->name ?? '') }}" />
                        <x-select label="Sexo" name="gender" :options="[['True','Masculino'],['False','Feminino'],['','Outro']]" selected="True" />
                        <x-input label="Numero KAK" name="number_kak"  value="{{ old('number_kak', $member->number_kak ?? '') }}" :disabled="isset($member)" />
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
                                    <option value="{{ $clube->id }}" {{ (isset($member) && $member->club_id == $clube->id) ? 'selected' : '' }}>
                                        {{ $clube->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-date-input label="Data de nascimento" name="birth_date" value="{{ old('birth_date', $member->birth_date ?? '') }}" />
                        <x-date-input label="Data de Admissão" name="admission_date" value="{{ old('admission_date', $member->admission_date ?? '') }}" />
                        <x-input label="Nacionalidade" name="nationality" value="{{ old('nationality', $member->nationality ?? '') }}" />
                        <x-input label="Profissão" name="profession" value="{{ old('profession', $member->profession ?? '') }}" />
                    </div>
                </x-card>
                <x-card title="Contato">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input label="Telefone" name="phone_number" value="{{ old('phone_number', $member->phone_number ?? '') }}" />
                        <x-input label="Celular" name="cell_number" value="{{ old('cell_number', $member->cell_number ?? '') }}" />
                        <x-input label="E-mail" name="email" value="{{ old('email',  $member->user->email ?? '')}}" />
                        <x-input label="Contato" name="contact" value="{{ old('contact', $member->contact ?? '') }}" />
                        <x-input label="Número do Contato" name="contact_number" value="{{ old('contact_number', $member->contact_number ?? '') }}" />
                        <x-input label="Email do Contato" name="contact_email" value="{{ old('contact_email', $member->contact_email ?? '') }}" />
                    </div>
                </x-card>

                <x-card title="Endereço">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input label="Endereço" name="address" value="{{ old('address', $member->address ?? '') }}" />
                        <x-input label="Cidade" name="city" value="{{ old('city', $member->city ?? '') }}" />
                        <x-input label="Distrito" name="district" value="{{ old('district', $member->district ?? '') }}" />
                        <x-input label="Código Postal" name="postal_code" value="{{ old('postal_code', $member->postal_code ?? '') }}" />
                    </div>
                </x-card>

                <x-card title="Pais">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input label="Nome da mãe" name="father_name" value="{{ old('father_name', $member->father_name ?? '') }}" />
                        <x-input label="Nome do pai" name="mother_name" value="{{ old('mother_name', $member->mother_name ?? '') }}" />
                    </div>
                </x-card>

               <x-card title="Outro">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input label="Tamanho do cinturão" name="bolt_size" value="{{ old('bolt_size', $member->bolt_size ?? '') }}" />
                    </div>
                </x-card>

                
            </div>
                    @php
                        $documentTypes = collect(App\DocumentoIdentificacao::cases())
                            ->map(fn($docType) => [$docType->value, $docType->name])
                            ->toArray();
                    @endphp
                    
            <!-- Dados Complementares -->
            <div class="space-y-6">
                <x-card title="Documentos e Identificação">
                    <x-input label="N.º FNKP" name="number_fnkp" value="{{ old('number_fnkp', $member->number_fnkp ?? '') }}" />
                    <x-input label="N.º TPTD" name="number_tptd" value="{{ old('number_tptd', $member->number_tptd ?? '') }}" />
                    <x-input label="N.º JKS" name="number_jks" value="{{ old('number_jks', $member->number_jks ?? '') }}" />
                    <x-input label="N.º Contribuinte" name="nif" value="{{ old('nif', $member->nif ?? '') }}" />
                    <x-select label="Árbitro" name="ctl00$MainContent$repPolja$ctl05$drpPolje" :options="[['',''],['7011','Oficial de Mesa'],['7012','Juiz'],['7013','Árbitro B'],['7014','Árbitro A']]" selected="" />
                    <x-select label="Grau de Treinador" name="ctl00$MainContent$repPolja$ctl06$drpPolje" :options="[['',''],['7015','Grau I'],['7016','Grau II'],['7017','Grau III'],['7018','Grau IV']]" selected="" />
                    <x-select 
                        label="Documento de Identificação" 
                        name="document_type" 
                        :options="$documentTypes" 
                        selected="{{ old('document_type', $member->document_type ?? '') }}" 
                    />
                    <x-input label="N.º Doc. Identificação" name="document_number" value="{{ old('document_number', $member->document_number ?? '') }}" />
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
                    <x-select 
                        label="Família" 
                        name="family_user_id" 
                        :options="$families->map(fn($family) => [$family->id, $family->name])->prepend(['', '<Selecionar família>'])->toArray()" 
                        selected="{{ old('family_user_id', $member->familyMember->family_id ?? '') }}" 
                    />
                </x-card>

                <x-card title="Notas">
                    <textarea name="observations" rows="2" class="w-full border rounded p-2">{{ old('observations', $member->observations ?? '') }}</textarea>
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