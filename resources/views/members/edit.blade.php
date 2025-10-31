<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Criar Usuário e Perfil') }}
        </h2>
    </x-slot>

    <form 
        action="{{ route('members.update', $profile->id) }}" 
        method="POST" 
        enctype="multipart/form-data"
        class="p-6 bg-white dark:bg-gray-800 shadow-lg rounded-xl space-y-8"
    >
        @csrf
        @method('PUT')

      

        {{-- Campos Profile --}}
        <div class="pt-4 border-t border-gray-300 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Dados do Perfil</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              

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

  @push('scripts')
    @vite('resources/js/camera.js')
@endpush

</x-app-layout>
