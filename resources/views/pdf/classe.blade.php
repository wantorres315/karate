<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Classe</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10px; 
            margin: 10px; 
        }

        .card {
            border: 2px solid #000;
            border-radius: 12px;
            padding: 5px;
            width: 100%;
            box-sizing: border-box;
            background: #fff;
        }

        table { 
            border-collapse: collapse; 
            width: 100%; 
            table-layout: fixed; 
        }

        td, th { 
            border: 1px solid #ccc; 
            padding: 4px; 
            vertical-align: middle;
            word-wrap: break-word;
            text-align: center;
        }

        h3 { 
            color: #EC2111; 
            margin: 5px 0; 
            border-bottom: 1px solid #EC2111; 
            padding-bottom: 2px; 
            font-size: 12px;
        }

        .percentage {
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
            min-width: 50px;
        }

        .percentage.high { background-color: #10b981; color: white; }
        .percentage.medium { background-color: #f59e0b; color: white; }
        .percentage.low { background-color: #ef4444; color: white; }

        p { margin: 2px 0; font-size: 9px; }
    </style>
</head>
<body>

<div class="card">
    <table>
        <tr>
            <td colspan="2" style="border: none; text-align:center;">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="width: 150px;">
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px; text-align: left;">Turma: {{$classe->name}}</td>
           
            <td style="font-size: 9px; text-align: right;">
                <?php
                    $cidade = "Pombal";
                    $data = new DateTime();
                    $formatter = new IntlDateFormatter('pt_BR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                    echo "{$cidade}, " . $formatter->format($data);
                ?>
            </td>
        </tr>
        <tr>
             <td style="font-size: 12px; text-align: left;">Descrição: {{$classe->description}}</td>
             <td style="text-align: left;">Instrutores: <br>{!! implode("<br>",$classe->instructors->pluck("name")->toArray()) ?? '—' !!}</td>
        </tr>
        <tr>
             <td style="font-size: 12px; text-align: left;">Horário: {{ $classe->start_time }} - {{ $classe->end_time }}</td>
             <td style="text-align: left;">Dias da Semana: <br>
                @php
                    $daysMap = [
                        'Monday' => 'Segunda-feira',
                        'Tuesday' => 'Terça-feira',
                        'Wednesday' => 'Quarta-feira',
                        'Thursday' => 'Quinta-feira',
                        'Friday' => 'Sexta-feira',
                        'Saturday' => 'Sábado',
                        'Sunday' => 'Domingo',
                        'monday' => 'Segunda-feira',
                        'tuesday' => 'Terça-feira',
                        'wednesday' => 'Quarta-feira',
                        'thursday' => 'Quinta-feira',
                        'friday' => 'Sexta-feira',
                        'saturday' => 'Sábado',
                        'sunday' => 'Domingo',
                    ];

                    $weekDays = [];
                    if (!empty($classe->week_days)) {
                        $days = is_array($classe->week_days) 
                            ? $classe->week_days 
                            : json_decode($classe->week_days, true);
                        
                        if (is_array($days)) {
                            foreach ($days as $day) {
                                $weekDays[] = $daysMap[$day] ?? $day;
                            }
                        }
                    }
                @endphp
                {{ !empty($weekDays) ? implode(', ', $weekDays) : '—' }}
             </td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 11px; text-align: left;">
                <strong>Total de Aulas:</strong> {{ $classeData['total_lessons'] }} | 
                <strong>Total de Alunos:</strong> {{ $classeData['total_students'] }}
            </td>
        </tr>
    </table>
    
    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <th colspan="7" style="background-color: #EC2111; color: white; font-size: 12px;">ALUNOS E FREQUÊNCIA</th>
        </tr>
        <tr style="background-color: #f3f4f6;">
            <th>Foto</th>
            <th>Nº KAK</th>
            <th>Nome</th>
            <th>Nascimento</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>Frequência</th>
        </tr>
        @foreach($studentsData as $data)
        @php
            $student = $data['student'];
            $percentage = $data['percentage'];
            
            // Definir classe CSS baseado na porcentagem
            if ($percentage >= 75) {
                $percentClass = 'high';
            } elseif ($percentage >= 50) {
                $percentClass = 'medium';
            } else {
                $percentClass = 'low';
            }
        @endphp
        <tr>
            <td>
                @if($student->photo && file_exists(public_path('storage/' . $student->photo)))
                    <img src="{{ public_path('storage/' . $student->photo) }}" alt="Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                @else
                    <img src="{{ public_path('images/club.png') }}" alt="Sem foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                @endif
            </td>
            <td>{{ $student->number_kak }}</td>
            <td style="text-align: left;">{{ $student->name }}</td>
            <td>{{ \Carbon\Carbon::parse($student->birth_date)->format("d/m/Y") }}</td>
            <td style="font-size: 8px;">{{ $student->cell_number }}<br>{{ $student->phone_number }}</td>
            <td style="font-size: 8px;">{{ $student->user->email }}</td>
            <td>
                <span class="percentage {{ $percentClass }}">
                    {{ $percentage }}%
                </span>
                <br>
                <span style="font-size: 8px; color: #666;">
                    ({{ $data['attendances'] }}/{{ $data['total_lessons'] }})
                </span>
            </td>
        </tr>
        @endforeach
    </table>
</div>

</body>
</html>
