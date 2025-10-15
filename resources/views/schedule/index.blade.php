<x-app-layout>
    <div class="p-6 max-w-6xl mx-auto">
        <h2 class="text-2xl font-bold mb-4">📅 Agenda de Eventos</h2>

        <!-- Botão para abrir modal de filtro PDF -->
        <div class="flex justify-end mb-4">
            <button id="openPdfModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                Exportar PDF
            </button>
        </div>

        <div id="calendar" class="bg-white p-4 rounded shadow"></div>
    </div>

    <!-- Modal de filtro de datas para PDF -->
    <div id="pdfModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 class="text-lg font-semibold mb-4">Exportar Eventos para PDF</h3>
            <form method="POST" action="{{ route('events.exportPdf') }}" class="space-y-3" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Data Inicial</label>
                    <input type="date" name="start_date" value = "2025-09-01" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Data Final</label>
                    <input type="date" name="end_date" value = "2026-07-01" class="w-full border rounded p-2" required>
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" id="cancelPdfModal" class="bg-gray-300 hover:bg-gray-400 text-black px-3 py-1 rounded">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                        Gerar PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modais de criação e detalhes -->
    <div id="createModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 class="text-lg font-semibold mb-4">Criar Evento</h3>
            <form id="createEventForm" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Título</label>
                    <input type="text" id="eventTitle" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                    <select id="eventType" class="w-full border rounded p-2">
                        <option value="T">Treinos Técnicos</option>
                        <option value="TC">Treinos de Competição Kata e Kumite</option>
                        <option value="F">Formação, Cursos, Reuniões</option>
                        <option value="EN">Encontros: Treino/Exames/Torneio</option>
                        <option value="C">Competições/Torneios/Provas</option>
                        <option value="E">Estágios Técnicos/Competição</option>
                        <option value="EX">Exames de Graduação</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Início</label>
                    <input type="datetime-local" id="eventStart" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Fim</label>
                    <input type="datetime-local" id="eventEnd" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Local</label>
                    <input type="text" id="eventLocation" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Organização</label>
                    <input type="text" id="eventOrganization" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea id="eventDescription" rows="3" class="w-full border rounded p-2"></textarea>
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" id="cancelCreate" class="bg-gray-300 hover:bg-gray-400 text-black px-3 py-1 rounded">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">
                        Criar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de detalhes -->
    <div id="eventModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96 relative">
            <h3 id="modalTitle" class="text-xl font-semibold mb-2"></h3>
            <p id="modalType" class="text-sm text-gray-600 mb-2"></p>
            <p id="modalDate" class="text-sm text-gray-600 mb-4"></p>
            <p id="modalDescription" class="text-gray-700 mb-4"></p>

            <div class="flex justify-end space-x-2">
                <button id="closeModal" class="bg-gray-300 hover:bg-gray-400 text-black px-3 py-1 rounded">
                    Fechar
                </button>
                @if(auth()->user()->hasRole(\App\Role::SUPER_ADMIN->value))
                    <button id="deleteEvent" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                        Excluir
                    </button>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
        <script>
            const token = '{{ csrf_token() }}';
            const isSuperAdmin = @json(auth()->user()->hasRole(\App\Role::SUPER_ADMIN->value));

            const eventColors = {
                'T': '#FBBF24',
                'TC': '#F472B6',
                'F': '#60A5FA',
                'EN': '#34D399',
                'C': '#F87171',
                'E': '#A78BFA',
                'EX': '#FCD34D',
                'default': '#9CA3AF'
            };

            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                const createModal = document.getElementById('createModal');
                const eventModal = document.getElementById('eventModal');
                const closeModal = document.getElementById('closeModal');
                const cancelCreate = document.getElementById('cancelCreate');
                const createForm = document.getElementById('createEventForm');
                const deleteBtn = document.getElementById('deleteEvent');
                let currentEvent = null;
                let selectedDate = null;

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'pt-br',
                    height: 'auto',
                    editable: true,
                    selectable: true,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: '/events',

                    dateClick(info) {
                        if (!isSuperAdmin) {
                            alert('Apenas o super admin pode criar eventos.');
                            return;
                        }
                        selectedDate = info.dateStr;
                        document.getElementById('eventStart').value = selectedDate + 'T08:00';
                        document.getElementById('eventEnd').value = selectedDate + 'T10:00';
                        createModal.classList.remove('hidden');
                    },

                    eventClick(info) {
                        currentEvent = info.event;
                        document.getElementById('modalTitle').textContent = info.event.title;
                        document.getElementById('modalType').textContent = info.event.extendedProps.type
                            ? `Tipo: ${info.event.extendedProps.type}`
                            : '';
                        document.getElementById('modalDescription').innerHTML = `<strong>Descrição</strong>: ${info.event.extendedProps.description || 'Sem descrição'}`;
                        document.getElementById('modalDate').innerHTML =
                            `De ${new Date(info.event.start).toLocaleString()} <br> até ${info.event.end ? new Date(info.event.end).toLocaleString() : ''}`;
                        eventModal.classList.remove('hidden');
                    },

                    eventDrop(info) {
                        fetch(`/events/${info.event.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                start: info.event.start.toISOString(),
                                end: info.event.end ? info.event.end.toISOString() : info.event.start.toISOString()
                            })
                        });
                    },
                    eventResize(info) {
                        fetch(`/events/${info.event.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                start: info.event.start.toISOString(),
                                end: info.event.end.toISOString()
                            })
                        });
                    }
                });

                calendar.render();

                createForm.addEventListener('submit', e => {
                    e.preventDefault();
                    const title = document.getElementById('eventTitle').value;
                    const type = document.getElementById('eventType').value;
                    const start = document.getElementById('eventStart').value;
                    const end = document.getElementById('eventEnd').value;
                    const location = document.getElementById('eventLocation').value;
                    const organization = document.getElementById('eventOrganization').value;
                    const description = document.getElementById('eventDescription').value;

                    fetch('/events', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ title, type, start, end, location, organization, description })
                    })
                    .then(res => res.json())
                    .then(() => {
                        createModal.classList.add('hidden');
                        createForm.reset();
                        calendar.refetchEvents();
                    });
                });

                cancelCreate.addEventListener('click', () => {
                    createModal.classList.add('hidden');
                    createForm.reset();
                });

                closeModal.addEventListener('click', () => eventModal.classList.add('hidden'));

                if (deleteBtn) {
                    deleteBtn.addEventListener('click', () => {
                        if (!confirm('Deseja realmente excluir este evento?')) return;
                        fetch(`/events/${currentEvent.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            }
                        }).then(() => {
                            eventModal.classList.add('hidden');
                            calendar.refetchEvents();
                        });
                    });
                }

                // Modal PDF
                const openPdfModal = document.getElementById('openPdfModal');
                const pdfModal = document.getElementById('pdfModal');
                const cancelPdfModal = document.getElementById('cancelPdfModal');
                const pdfForm = document.getElementById('pdfForm');

                openPdfModal.addEventListener('click', () => pdfModal.classList.remove('hidden'));
                cancelPdfModal.addEventListener('click', () => {
                    pdfModal.classList.add('hidden');
                    pdfForm.reset();
                });

                pdfForm.addEventListener('submit', e => {
                    e.preventDefault();
                    const startDate = document.getElementById('pdfStartDate').value;
                    const endDate = document.getElementById('pdfEndDate').value;
                    if (!startDate || !endDate) return alert('Selecione as duas datas.');
                    console.log('Gerar PDF de', startDate, 'até', endDate);
                    pdfModal.classList.add('hidden');
                    pdfForm.reset();
                });
            });
        </script>
    @endpush
</x-app-layout>
