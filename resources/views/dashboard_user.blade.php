<x-app-layout>
  <div class="flex flex-wrap -mx-3">
    <div id="calendar" class="bg-white p-4 rounded shadow"></div>
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

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
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

        const calendarEl = document.getElementById('calendar');
        const eventModal = document.getElementById('eventModal');
        const deleteBtn = document.getElementById('deleteEvent');
        let currentEvent = null;
        let selectedDate = null;

        // Inicializar calendário
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
                if (!isSuperAdmin) return;
                selectedDate = info.dateStr;
                document.getElementById('eventStart').value = selectedDate + 'T08:00';
                document.getElementById('eventEnd').value = selectedDate + 'T10:00';
                const createModal = document.getElementById('createModal');
                if (createModal) createModal.classList.remove('hidden');
            },

            eventClick(info) {
                currentEvent = info.event;
                document.getElementById('modalTitle').textContent = info.event.title;
                document.getElementById('modalType').textContent = info.event.extendedProps.type
                    ? `Tipo: ${info.event.extendedProps.type}`
                    : '';
                document.getElementById('modalDescription').innerHTML =
                    `<strong>Descrição</strong>: ${info.event.extendedProps.description || 'Sem descrição'}`;
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

        // --- Fechar modal de evento (corrigido) ---
        document.addEventListener('click', (e) => {
            if (e.target.id === 'closeModal') {
                eventModal.classList.add('hidden');
            }
        });

        // --- Excluir evento ---
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

        // --- Modal PDF (mantido igual, corrigido se precisar futuramente) ---
        const openPdfModal = document.getElementById('openPdfModal');
        const pdfModal = document.getElementById('pdfModal');
        const cancelPdfModal = document.getElementById('cancelPdfModal');
        const pdfForm = document.getElementById('pdfForm');

        if (openPdfModal && pdfModal && pdfForm) {
            openPdfModal.addEventListener('click', () => pdfModal.classList.remove('hidden'));
            cancelPdfModal?.addEventListener('click', () => {
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
        }
    });
  </script>
  @endpush

  @push('styles')
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
  @endpush
</x-app-layout>
