<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            background-color: #f9f9f9;
            color: #333;
        }
        h1, h2, h3, h4 {
            margin: 10px 0 5px;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        ul {
            margin: 0;
            padding-left: 18px;
        }
        li::marker {
            content: "🍴 ";
        }
        img {
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .box {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #fefefe;
        }
        .meal-table {
            width: 100%;
            border: none;
        }
        .meal-table td {
            border: none;
            vertical-align: top;
        }
        .section {
            background-color: #eef4f8;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .section p {
            margin: 4px 0;
        }
    </style>
</head>
<body>

    <h1> Anamnese Nutricional</h1>

   <div class="section" style="display: flex; align-items: center; gap: 15px;">
    <table style="border-collapse: collapse; border: none;">
        <thead>
            <tr>
                 <td style="border: none;">
                    <div>
                        <h2><i class="fa fa-users"></i> Dados do Paciente</h2>
                        <p><strong>Nome:</strong> {{ $user->name }}</p>
                        <p><strong>Sexo:</strong>
                            {{
                                match($user->sex) {
                                    'male' => 'Masculino',
                                    'female' => 'Feminino',
                                    default => ucfirst($user->sex)
                                }
                            }}
                            @php $imc = $pesos->last()?->weight/(($user->height*$user->height)/10000) @endphp 
                        <p><strong>Data de nascimento:</strong> {{ \Carbon\Carbon::parse($user->birth)->format('d/m/Y') }}</p>
                        <p><strong>Idade:</strong> {{ \Carbon\Carbon::parse($user->birth)->age }} anos</p>
                        <p><strong>Altura:</strong> {{ $user->height }} cm</p>
                        <p><strong>Inicio do tratamento:</strong> {{ \Carbon\Carbon::parse($todosPesos->first()?->date)->format('d/m/Y')}}</p>
                        <p><strong>Data do relatório:</strong> {{ $periodo["inicio"] }} até {{ $periodo["fim"] }}</p>
                        <p><strong>Perda de peso total:</strong> {{ $pesos->first()?->weight - $pesos->last()?->weight }} kg </p>
                        <p><strong>IMC:</strong> {{ round($imc, 2) }}  </p>

                    </div>
                </td>
                 <td style="border: none;text-align:center">
                    <div style="flex-shrink: 0;">
                        @php
                            $photoPath = auth()->user()->photo ? public_path( auth()->user()->photo) : public_path('assets/avatars/default.png');
                            $imageData = base64_encode(file_get_contents($photoPath));
                            $mimeType = mime_content_type($photoPath);
                        @endphp
                            <img src="data:{{ $mimeType }};base64,{{ $imageData }}" 
                                alt="Foto do paciente" 
                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 2px solid #ccc;">
                    </div>
                </td>
            </tr>
        </thead>
    </table>
   
    
</div>


    <h3>📈 Evolução do Peso</h3>

    @if($grafico_peso_base64)
        <img src="{{ $grafico_peso_base64 }}" alt="Gráfico de peso" style="max-width: 100%; border: 1px solid #ccc; border-radius: 6px;">
    @else
        <p>Não foi possível gerar o gráfico de peso.</p>
    @endif

    <h3>📈 Exercícios</h3>

    @if($grafico_peso_base64Exercises)
        <img src="{{ $grafico_peso_base64Exercises }}" alt="Gráfico de Exercícios" style="max-width: 100%; border: 1px solid #ccc; border-radius: 6px;">
    @else
        <p>Não foi possível gerar o gráfico de peso.</p>
    @endif

    <!-- Tabela dos pesos -->
    <h3>📊 Histórico de Pesos</h3>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Peso (kg)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesos as $peso)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($peso->date)->format('d/m/Y') }}</td>
                    <td>{{ number_format($peso->weight, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>⚙️ Composição Corporal</h3>

@php
    $metrics = [
        'body_fat' => 'Gordura (%)',
        'musculo_skeletal' => 'Músculo (kg)',
        'body_weight_without_fat' => 'Peso sem Gordura (kg)',
        'subcutaneous_fat' => 'Gordura Subcutânea (%)',
        'visceral_fat' => 'Gordura Visceral',
        'body_water' => 'Água (%)',
        'muscle_mass' => 'Massa Muscular (kg)',
        'bone_mass' => 'Massa Óssea (kg)',
        'protein' => 'Proteína (%)',
        'metabolic_age' => 'Idade Metabólica',
    ];
    $perRow = 5; 
@endphp

@php
    $first = $pesos->first();
    $last = $pesos->last();
@endphp


<table style="width: 100%; border-collapse: separate; border-spacing: 8px;">
    @foreach (array_chunk($metrics, $perRow, true) as $chunk)
        <tr>
            @foreach ($chunk as $key => $label)
                @php
                    $firstValue = $first->$key ?? null;
                    $lastValue = $last->$key ?? null;
                    $diff = null;
                    $percent = null;

                    if (is_numeric($firstValue) && is_numeric($lastValue)) {
                        $diff = $lastValue - $firstValue;
                        $percent = $firstValue != 0
                            ? round(($diff / $firstValue) * 100, 1)
                            : null;
                    }
                @endphp

                <td style="
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    padding: 6px 8px;
                    background-color: #fff;
                    box-shadow: 0 0 4px rgba(0,0,0,0.1);
                    text-align: center;
                    font-size: 12px;
                    width: 110px;
                    height: 90px;
                    vertical-align: middle;
                ">
                    {{-- Nome da métrica --}}
                    <div style="font-weight: 600; color: #2c3e50; margin-bottom: 4px;">
                        {{ $label }}
                    </div>

                    {{-- Último valor --}}
                    <div style="color: #444; font-size: 18px;">
                        {{ $lastValue ?? '-' }}
                    </div>

                    {{-- Diferença e porcentagem --}}
                    @if(!is_null($diff))
                        <div style="font-size: 12px; color: {{ $diff >= 0 ? 'green' : 'red' }};">
                            {{ $diff >= 0 ? '+' : '' }}{{ $diff }}
                            @if(!is_null($percent))
                                ({{ $percent }}%)
                            @endif
                        </div>
                    @endif
                </td>
            @endforeach
        </tr>
    @endforeach
</table>




</body>
</html>
