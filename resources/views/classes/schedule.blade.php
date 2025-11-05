<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Horário das Classes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-4">📅 Horário das Classes</h2>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            if (!calendarEl) {
                console.error('Elemento calendar não encontrado!');
                return;
            }

            console.log('Inicializando calendário...');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'pt-br',
                height: 'auto',
                editable: false,
                selectable: false,
                eventStartEditable: false,
                eventResizableFromStart: false,
                eventDurationEditable: false,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridWeek,timeGridDay'
                },
                slotMinTime: '08:00:00',
                slotMaxTime: '22:00:00',
                allDaySlot: false,
                weekends: true,
                events: '{{ route('classes.schedule.data') }}',
                
                loading: function(isLoading) {
                    console.log('Carregando eventos:', isLoading);
                },
                
                eventDidMount: function(info) {
                    console.log('Evento montado:', info.event.title);
                    
                    // Adicionar cursor pointer para indicar que é clicável
                    info.el.style.cursor = 'pointer';
                    
                    // Adicionar tooltip com mais informações
                    info.el.title = `${info.event.title}\n${info.event.extendedProps.instructor}\nClique para ver detalhes`;
                },

                eventClick: function(info) {
                    const event = info.event;
                    const classeId = event.extendedProps.classe_id;
                    
                    console.log('Clique no evento - ID da classe:', classeId);
                    
                    // Redirecionar para a página de edição/visualização da turma
                    if (classeId) {
                        const url = "{{ route('classes.edit', ':id') }}".replace(':id', classeId);
                        console.log('Redirecionando para:', url);
                        window.location.href = url;
                    }
                },
                
                eventContent: function(arg) {
                    // Customizar o conteúdo do evento
                    let html = `
                        <div class="fc-event-main-frame">
                            <div class="fc-event-title-container">
                                <div class="fc-event-title fc-sticky font-bold">
                                    ${arg.event.title}
                                </div>
                                ${arg.event.extendedProps.instructor ? 
                                    `<div class="fc-event-title fc-sticky text-xs opacity-90">
                                        👨‍🏫 ${arg.event.extendedProps.instructor}
                                    </div>` : ''
                                }
                            </div>
                        </div>
                    `;
                    
                    return { html: html };
                }
            });

            calendar.render();
            console.log('Calendário renderizado!');
        });
    </script>

    <style>
        .fc-event {
            transition: transform 0.2s;
        }
        .fc-event:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</x-app-layout>
