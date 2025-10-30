<x-app-layout>
    {{-- Inclua Alpine.js no seu layout se ainda não tiver --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

        {{-- Alunos Ativos --}}
        @if($countStudentsActive > 0)
        <div x-data="{ count: 0 }" 
             x-init="const interval = setInterval(() => { if(count < {{ $countStudentsActive }}) { count++ } else { clearInterval(interval) } }, 10)" 
             class="bg-gradient-to-r from-green-400 to-green-600 rounded-xl p-6 shadow-lg transform hover:scale-105 transition-transform duration-300 flex items-center space-x-4">
            <i class="fa fa-users text-5xl text-white"></i>
            <div>
                <h4 class="text-white font-semibold text-xl">Alunos Ativos</h4>
                <span class="text-white font-bold text-3xl" x-text="count"></span>
            </div>
        </div>
        @endif

        {{-- Alunos Inativos --}}
        @if($countStudentsInactive > 0)
        <div x-data="{ count: 0 }" 
             x-init="const interval = setInterval(() => { if(count < {{ $countStudentsInactive }}) { count++ } else { clearInterval(interval) } }, 10)" 
             class="bg-gradient-to-r from-gray-400 to-gray-600 rounded-xl p-6 shadow-lg transform hover:scale-105 transition-transform duration-300 flex items-center space-x-4">
            <i class="fa fa-user-slash text-5xl text-white"></i>
            <div>
                <h4 class="text-white font-semibold text-xl">Alunos Inativos</h4>
                <span class="text-white font-bold text-3xl" x-text="count"></span>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
