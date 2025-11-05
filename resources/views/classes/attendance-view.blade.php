<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Frequência das Aulas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <!-- Filtros -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Turma</label>
                            <select id="classe_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Selecione uma turma...</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }} - {{ $class->club->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Filtro</label>
                            <select id="filter_type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="month">Por Mês</option>
                                <option value="period">Por Período</option>
                            </select>
                        </div>

                        <!-- Filtro por Mês -->
                        <div id="month-filter">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mês</label>
                            <input type="month" id="month" value="{{ now()->format('Y-m') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Filtro por Período (inicialmente oculto) -->
                        <div id="period-filter" class="hidden md:col-span-2 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
                                <input type="date" id="start_date" 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
                                <input type="date" id="end_date" 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="flex items-end">
                            <button onclick="loadAttendance()" 
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                🔍 Buscar
                            </button>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div id="loading" class="hidden text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="mt-2 text-gray-600">Carregando...</p>
                    </div>

                    <!-- Mensagem de aviso -->
                    <div id="no-data" class="hidden text-center py-8">
                        <p class="text-gray-500">Selecione uma turma e clique em buscar para ver a frequência.</p>
                    </div>

                    <!-- Tabela de Frequência -->
                    <div id="attendance-table" class="hidden">
                        <!-- Resumo -->
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-600">Total de Alunos</p>
                                <p class="text-2xl font-bold text-gray-800" id="total-students">0</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600">Total de Aulas</p>
                                <p class="text-2xl font-bold text-gray-800" id="total-lessons">0</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600">Frequência Média</p>
                                <p class="text-2xl font-bold text-gray-800" id="avg-attendance">0%</p>
                            </div>
                        </div>

                        <!-- Tabela -->
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr id="table-header">
                                        <th class="sticky left-0 z-10 bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aluno
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nº KAK
                                        </th>
                                        <!-- Datas das aulas serão inseridas aqui -->
                                        <th class="sticky right-12 z-10 bg-gray-50 px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Presenças
                                        </th>
                                        <th class="sticky right-0 z-10 bg-gray-50 px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            %
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="attendance-body" class="bg-white divide-y divide-gray-200">
                                    <!-- Dados serão inseridos aqui via JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Legenda -->
                        <div class="mt-4 flex items-center gap-4 text-sm">
                            <span class="flex items-center gap-2">
                                <span class="w-6 h-6 bg-green-500 rounded inline-block"></span>
                                Presente
                            </span>
                            <span class="flex items-center gap-2">
                                <span class="w-6 h-6 bg-red-500 rounded inline-block"></span>
                                Ausente
                            </span>
                            <span class="flex items-center gap-2">
                                <span class="w-6 h-6 bg-gray-300 rounded inline-block"></span>
                                Sem registro (aula futura)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Alternar entre filtro por mês e período
        document.getElementById('filter_type').addEventListener('change', function() {
            const monthFilter = document.getElementById('month-filter');
            const periodFilter = document.getElementById('period-filter');
            
            if (this.value === 'month') {
                monthFilter.classList.remove('hidden');
                periodFilter.classList.add('hidden');
            } else {
                monthFilter.classList.add('hidden');
                periodFilter.classList.remove('hidden');
            }
        });

        function loadAttendance() {
            const classeId = document.getElementById('classe_id').value;
            const filterType = document.getElementById('filter_type').value;

            if (!classeId) {
                alert('Por favor, selecione uma turma');
                return;
            }

            // Montar URL baseado no tipo de filtro
            let url = `/classes/attendance-data/${classeId}?`;
            
            if (filterType === 'month') {
                const month = document.getElementById('month').value;
                if (!month) {
                    alert('Por favor, selecione um mês');
                    return;
                }
                url += `month=${month}`;
            } else {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                
                if (!startDate || !endDate) {
                    alert('Por favor, selecione as datas inicial e final');
                    return;
                }
                
                if (startDate > endDate) {
                    alert('A data inicial não pode ser maior que a data final');
                    return;
                }
                
                url += `start_date=${startDate}&end_date=${endDate}`;
            }

            // Mostrar loading
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('attendance-table').classList.add('hidden');
            document.getElementById('no-data').classList.add('hidden');

            // Fazer requisição
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loading').classList.add('hidden');
                    
                    if (data.lessons.length === 0) {
                        document.getElementById('no-data').classList.remove('hidden');
                        return;
                    }

                    renderAttendanceTable(data);
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('loading').classList.add('hidden');
                    alert('Erro ao carregar dados de frequência');
                });
        }

        function renderAttendanceTable(data) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Atualizar resumo
            document.getElementById('total-students').textContent = data.students.length;
            document.getElementById('total-lessons').textContent = data.lessons.length;
            
            // Calcular frequência média
            let totalAttendances = 0;
            let totalPastLessons = 0;
            
            data.lessons.forEach((lesson, index) => {
                const lessonDate = new Date(lesson.lesson_date);
                lessonDate.setHours(0, 0, 0, 0);
                
                if (lessonDate < today) {
                    totalPastLessons++;
                    data.students.forEach(student => {
                        if (student.attendances[index] === true) {
                            totalAttendances++;
                        }
                    });
                }
            });
            
            const totalPossible = data.students.length * totalPastLessons;
            const avgAttendance = totalPossible > 0 ? ((totalAttendances / totalPossible) * 100).toFixed(1) : 0;
            document.getElementById('avg-attendance').textContent = avgAttendance + '%';

            // Renderizar cabeçalho com datas
            const headerRow = document.getElementById('table-header');
            const existingDateHeaders = headerRow.querySelectorAll('.date-header');
            existingDateHeaders.forEach(th => th.remove());
            
            const presencasHeader = headerRow.children[2];
            
            data.lessons.forEach(lesson => {
                const date = new Date(lesson.lesson_date);
                const day = date.getDate().toString().padStart(2, '0');
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                
                const th = document.createElement('th');
                th.className = 'date-header px-2 py-3 text-center text-xs font-medium text-gray-500 border-l';
                th.textContent = `${day}/${month}`;
                
                headerRow.insertBefore(th, presencasHeader);
            });

            // Renderizar corpo da tabela
            let bodyHtml = '';
            data.students.forEach(student => {
                const presencas = student.attendances.filter((a, index) => {
                    const lessonDate = new Date(data.lessons[index].lesson_date);
                    lessonDate.setHours(0, 0, 0, 0);
                    return a === true && lessonDate < today;
                }).length;
                
                const pastLessonsCount = data.lessons.filter(lesson => {
                    const lessonDate = new Date(lesson.lesson_date);
                    lessonDate.setHours(0, 0, 0, 0);
                    return lessonDate < today;
                }).length;
                
                const percentual = pastLessonsCount > 0 
                    ? ((presencas / pastLessonsCount) * 100).toFixed(1) 
                    : 0;
                
                let percentClass = 'text-green-600';
                if (percentual < 50) percentClass = 'text-red-600';
                else if (percentual < 75) percentClass = 'text-yellow-600';

                bodyHtml += `
                    <tr class="hover:bg-gray-50">
                        <td class="sticky left-0 z-10 bg-white px-4 py-3 text-sm font-medium text-gray-900">
                            ${student.name}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 text-center">
                            ${student.number_kak || '—'}
                        </td>
                `;

                student.attendances.forEach((attended, index) => {
                    const lessonDate = new Date(data.lessons[index].lesson_date);
                    lessonDate.setHours(0, 0, 0, 0);
                    const isPast = lessonDate < today;

                    if (attended === true) {
                        bodyHtml += `
                            <td class="px-2 py-3 text-center border-l">
                                <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-bold inline-block w-6">
                                    ✓
                                </span>
                            </td>
                        `;
                    } else if (isPast) {
                        bodyHtml += `
                            <td class="px-2 py-3 text-center border-l">
                                <span class="bg-red-500 text-white px-2 py-1 rounded text-xs font-bold inline-block w-6">
                                    ✗
                                </span>
                            </td>
                        `;
                    } else {
                        bodyHtml += `
                            <td class="px-2 py-3 text-center border-l">
                                <span class="bg-gray-300 text-gray-600 px-2 py-1 rounded text-xs inline-block w-6">
                                    —
                                </span>
                            </td>
                        `;
                    }
                });

                bodyHtml += `
                        <td class="sticky right-12 z-10 bg-white px-4 py-3 text-sm text-gray-900 text-center font-medium">
                            ${presencas}/${pastLessonsCount}
                        </td>
                        <td class="sticky right-0 z-10 bg-white px-4 py-3 text-sm ${percentClass} text-center font-bold">
                            ${percentual}%
                        </td>
                    </tr>
                `;
            });

            document.getElementById('attendance-body').innerHTML = bodyHtml;
            document.getElementById('attendance-table').classList.remove('hidden');
        }

        // Carregar ao mudar turma
        document.getElementById('classe_id').addEventListener('change', loadAttendance);
        document.getElementById('month').addEventListener('change', function() {
            if (document.getElementById('classe_id').value) {
                loadAttendance();
            }
        });
    </script>

    <style>
        .sticky {
            position: sticky;
            background-color: white;
        }
        
        .sticky.left-0 {
            left: 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sticky.right-0 {
            right: 0;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sticky.right-12 {
            right: 48px;
        }
    </style>
</x-app-layout>