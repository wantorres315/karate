<x-app-layout>
  <div class="flex flex-wrap -mx-3">
    

    @if($countStudentsActive > 0)
      <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 xl:w-1/4">
          <x-dashboard.card 
              title="Alunos Ativos" 
              :value="$countStudentsActive" 
              icon="<i class='fa fa-users text-lg relative top-3 text-white'></i>" 
              color="from-blue-500 to-violet-500"
          />
      </div>
    @endif

    @if($countStudentsInactive > 0)
      <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 xl:w-1/4">
          <x-dashboard.card 
              title="Alunos Inativos" 
              :value="$countStudentsInactive" 
              icon="<i class='fa fa-users text-lg relative top-3 text-white'></i>" 
              color="from-blue-500 to-violet-500"
          />
      </div>
    @endif

   @foreach($studentByClub as $club)
      @php
        $clubName = $club['name'];
        $totalStudents = $club['count'];
        $logo = $club['logo'] ?? asset('images/logo_branco.png');
      @endphp

     <!-- Card 3 -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 xl:w-1/4">
      <x-dashboard.card title="Alunos Ativos - {{ $clubName }}" :value="$totalStudents" icon="<img src='{{$logo}}' class='w-12 h-12 text-center rounded-circle bg-red-500 '>" color="from-red-500 to-yellow-500"/>
    </div>

    @endforeach
</div>


</x-app-layout>
