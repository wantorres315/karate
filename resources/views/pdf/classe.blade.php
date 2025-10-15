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
            padding: 2px; 
            vertical-align: top; 
            word-wrap: break-word;
        }

        h3 { 
            color: #EC2111; 
            margin: 5px 0; 
            border-bottom: 1px solid #EC2111; 
            padding-bottom: 2px; 
            font-size: 12px;
        }

        .label { 
            font-weight: bold; 
            display: inline-block; 
            margin-right: 5px; 
        }

        .value {
            display: inline-block;
            padding: 2px 6px;
            min-width: 60px;
            border-radius: 4px;
            
            font-size: 9px;
        }

        .value.full, 
        .value.full_100 {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding-left: 4px;
            height: 20px;
            border-radius: 4px;
           
            font-size: 9px;
        }

        .photo-box {
            text-align: center;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .photo-box img {
            max-width: 100px;
            max-height: 110px;
            object-fit: cover;
            border-radius: 4px;
        }

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
            <td style="font-size: 12px;">Turma: {{$classe->name}}</td>
           
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
             <td style="font-size: 12px;">Descricao: {{$classe->description}}</td>
             <td>Instrutores: <br>{!! implode("<br>",$classe->instructors->pluck("name")->toArray()) ?? '—' !!}</td></td>
        </tr>
        <tr>
             <td style="font-size: 12px;">Horario: {{ $classe->start_time }} - {{ $classe->end_time }}</td>
             <td>Dias da Semana: <br>
                            @php
                            $daysMap = [
                                'Monday' => 'Segunda-feira',
                                'Tuesday' => 'Terça-feira',
                                'Wednesday' => 'Quarta-feira',
                                'Thursday' => 'Quinta-feira',
                                'Friday' => 'Sexta-feira',
                                'Saturday' => 'Sábado',
                                'Sunday' => 'Domingo',
                            ];

                            $weekDays = [];
                            if (!empty($classe->week_days)) {
                                foreach (json_decode($classe->week_days) as $day) {
                                    $weekDays[] = $daysMap[$day] ?? $day;
                                }
                            }
                        @endphp
                     {{ !empty($weekDays) ? implode(', ', $weekDays) : '—' }}
                    </td></td>
        </tr>
    </table>
    <table style="width='100%'">
        <tr><td colspan = 6 style="width='100%;text-align='center'"><h2>Alunos</h2></td></tr>
        <tr>
            <td style="width: 65%;">
            </td>
            <td style="width: 65%;">
                Nº KAK
            </td>
            <td style="width: 65%;">
                Nome
            </td>
            <td style="width: 65%;">
                Nascimento
            </td>
            <td style="width: 65%;">
                Telefone
            </td>
            <td style="width: 65%;">
                E-mail
            </td>
        </tr>
        @foreach($classe->students as $student)
        <tr>
            <td style= "text-align='center'">
                <img src="{{ public_path($student->photo) }}" alt="Logo" style="width: 50px;">
            </td>
            <td>
                {{$student->number_kak}}
            </td>
            <td>
                {{$student->name}}
            </td>
            <td>
                {{\Carbon\Carbon::parse($student->birth_date)->format("d/m/Y")}}
            </td>
            <td>
                {{$student->cell_number}}/ {{$student->phone_number}}
            </td>
            <td>
                {{$student->user->email}}
            </td>
        </tr>
        @endforeach
    </table>
</div>

</body>
</html>
