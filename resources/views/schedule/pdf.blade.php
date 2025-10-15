<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{$name}}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10px; 
            margin: 10px; 
        }

        .card {
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
            padding: 2px; 
            vertical-align: top; 
            word-wrap: break-word;
        }

        h3 { 
            color: #EC2111; 
            margin: 5px 0; 
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
           
            font-size: 9px;
        }

        p { margin: 2px 0; font-size: 9px; }
    </style>
</head>
<body>

<div class="card">
    <table>
        <tr>
            <td style="width: 25%; text-align: left;">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="width: 150px;">
            </td>
             <td style="text-align: left;">
                <span style="font-size: 16px;">Calendário de Atividades</span>
                <br><span style="font-size: 10px;">Calendário Integrado KAK/JKSP/FNKP</span>
            </td>
             <td style="font-size: 20px; text-align: right;">
                <?php
                   echo date('Y', strtotime($startDate)) . '/' . date('Y', strtotime($endDate));
                 ?>
            </td>
        </tr>
        <tr>
           <td colspan="3" style="border-top:2px solid #000; padding-top: 5px; text-align: left;height:20px"></td>
        </tr>
    </table>
    <table style="width='100%;margin-top:20px'">
        <tr>
            <td style= "background-color: #000000; color: #FFFFFF;border: 1px solid #ffffff">
                DATA
            </td>
            <td style= "width:20px;background-color: #000000; color: #FFFFFF;border: 1px solid #ffffff">
                TIPO
            </td>
            <td style= "background-color: #000000; color: #FFFFFF;border: 1px solid #ffffff">
                DESCRIÇÃO DA ATIVIDADE
            </td>
            <td style= "background-color: #000000; color: #FFFFFF;border: 1px solid #ffffff">
                LOCAL
            </td>
            <td style= "background-color: #000000; color: #FFFFFF;border: 1px solid #ffffff;text-align:center">
                ORGANIZAÇÃO
            </td>
        </tr>
        @foreach($groupedEvents as $month => $events)
            <tr>
                <td colspan="5" style="background-color: #cecece; text-align: center; padding: 5px; font-weight: bold;">
                    {{ \Carbon\Carbon::parse($events->first()->start)->translatedFormat('F Y') }}
                </td>
           </tr>
            @foreach($events as $event)
                <tr>
                    <td  style="width:20%">
                        {{ \Carbon\Carbon::parse($event->start)->format("d/m/Y") }}
                        -
                        {{ \Carbon\Carbon::parse($event->end)->format("d/m/Y") }}
                    </td>
                    @php 
                        $color = $event->color ?: '#FFFFFF';
                    @endphp
                    <td style="background-color: {{$color}};text-align:center;width:5%;">{{ $event->type }}</td>
                    <td style="width:45%;padding:5px">{{ $event->title }}</td>
                    <td  style="width:15%">{{ $event->location }}</td>
                    <td  style="width:15%;text-align:center">{{ $event->organization }}</td>
                </tr>
            @endforeach

            <div style="page-break-after: always;"></div>
        @endforeach

    </table>
</div>

</body>
</html>
