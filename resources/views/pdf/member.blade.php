<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Membro</title>
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
            <td style="font-size: 12px;">FICHA DE MEMBRO KAK</td>
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
            <td style="width: 65%;">
                <table>
                    <tr><td colspan="5"><h3>Dados de Membro</h3></td></tr>
                    <tr>
                        <td><span class="label">Nº KAK</span></td>
                        <td><span class="label">Nº FNKP</span></td>
                        <td><span class="label">Nº CIT</span></td>
                        <td><span class="label">TPTD Nº</span></td>
                        <td><span class="label">Nº JKS</span></td>
                    </tr>
                    <tr>
                        <td><span class="value">{{ $member['number_kak'] }}</span></td>
                        <td><span class="value">{{ $member['number_fnkp'] }}</span></td>
                        <td><span class="value">{{ $member['cit_number'] }}</span></td>
                        <td><span class="value">{{ $member['tptd_number'] }}</span></td>
                        <td><span class="value">{{ $member['jks_number'] }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="label full">Tipo de Membro</span></td>
                        <td><span class="label">Árbitro</span></td>
                        <td colspan="2"><span class="label full">Data de Admissão</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="value full">{{ $member['member_type'] }}</span></td>
                        <td><span class="value">{{ $member['arbitrator'] }}</span></td>
                        <td colspan="2"><span class="value full">{{ $member['admission_date'] }}</span></td>
                    </tr>
                    <tr><td colspan="5"><h3>Dados Pessoais</h3></td></tr>
                    <tr><td colspan="5"><span class="label">Nome</span></td></tr>
                    <tr><td colspan="5"><span class="value full_100">{{ $member['name'] }}</span></td></tr>
                    <tr>
                        <td colspan="2"><span class="label">Nome do Pai</span></td>
                        <td colspan="3"><span class="label">Nome da Mãe</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="value">{{ $member['father_name'] }}</span></td>
                        <td colspan="3"><span class="value">{{ $member['mother_name'] }}</span></td>
                    </tr>
                </table>
            </td>
            <td style="width: 35%;padding-left:10px">
                <div class="photo-box">
                    <img src="{{ $member['photo'] }}" alt="Foto do Membro">
                </div>
                <h3 style="text-align: center;">Clube do Praticante</h3>
                <p style="font-size: 12px;"><b>Nome:</b> <br>{{ $member['club_name'] }}</p>
                <hr>
                <p style="font-size: 12px;"><b>Sigla:</b><br>     {{ $member['club_sigla'] }}</p>
                <hr>
                <p style="font-size: 12px;"><b>Localidade:</b> <br>{{ $member['club_location'] }}</p>
                <hr>
            </td>
        </tr>

        <tr>
            <td>
                <table>
                    <tr><td colspan="5"><h3>Dados de Contacto</h3></td></tr>
                    <tr>
                        <td colspan="5"><span class="label">Morada</span></td>
                    </tr>
                    <tr>
                        <td colspan="5"><span class="value full_100">{{ $member['address'] }}</span></td>
                    </tr>
                    <tr>
                        <td colspan = 2><span class="label">Cod. Postal</span></td>
                        <td colspan = 3><span class="label">Localidade</span></td>
                    </tr>
                    <tr>
                        <td colspan = 2><span class="value full">{{ $member['postal_code'] }}</span></td>
                        <td colspan = 3><span class="value full">{{ $member['city'] }}</span></td>
                    </tr>
                    <tr>
                        <td colspan = 2><span class="label">Distrito</span></td>
                        <td colspan = 2><span class="label">Telefone</span></td>
                        <td><span class="label">Telemóvel</span></td>
                    </tr>
                    <tr>
                        <td colspan = 2><span class="value full">{{ $member['district'] }}</span></td>
                        <td colspan = 2><span class="value full">{{ $member['phone'] }}</span></td>
                        <td><span class="value">{{ $member['cell'] }}</span></td>
                    </tr>
                    <tr>
                        <td colspan = 5><span class="label">Email</span></td>
                    </tr>
                    <tr>
                        <td colspan="5"><span class="value full_100">{{ $member['email'] }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="label">Pessoa de Contato</span></td>
                        <td colspan="3"><span class="label">Telefone</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="value">{{ $member['contact_name'] }}</span></td>
                        <td colspan="3"><span class="value">{{ $member['contact_phone'] }}</span></td>
                        
                    </tr>
                    <tr>
                        <td colspan = 5><span class="label">Observações/Patologias</span></td>
                    </tr>
                    <tr>
                        <td colspan="5"><span class="value full_100">{{ $member['observations'] }}</span></td>
                    </tr>
                    <tr><td colspan="5"><h3>Graduações</h3></td></tr>
                    <tr>
                        <td colspan="5">
                            @php
                                $half = ceil($graduations->count() / 2);
                                $firstColumn = $graduations->slice(0, $half);
                                $secondColumn = $graduations->slice($half);
                            @endphp
                            <table width="100%" style="table-layout: fixed; border-collapse: collapse;">
                                <tr>
                                    {{-- Primeira coluna --}}
                                    <td width="50%" valign="top" style="padding: 0; border: none;">
                                        <table width="100%" style="table-layout: fixed; border-collapse: collapse;">
                                            <thead>
                                                <tr>
                                                    <th style="font-size: 10px;"></th>
                                                    <th style="font-size: 10px;">Graduação</th>
                                                    <th style="font-size: 10px;">Data</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($firstColumn as $graduation)
                                                <tr>
                                                    <td style="font-size: 9px; text-align: center;">
                                                        @if($graduation['color'])
                                                            <div style="width: 12px; height: 12px; background-color: {{ $graduation['color'] }}; border: 1px solid #000; margin: 0 auto; border-radius: 2px;"></div>
                                                        @endif
                                                    <td style="font-size: 9px;">{{ $graduation['name'] }}</td>
                                                    <td style="font-size: 9px;">{{ $graduation['date'] ?? '-' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>

                                    {{-- Segunda coluna --}}
                                    <td width="50%" valign="top" style="padding: 0; border: none;">
                                        <table width="100%" style="table-layout: fixed; border-collapse: collapse;">
                                            <thead>
                                                <tr>
                                                    <th style="font-size: 10px;"></th>
                                                    <th style="font-size: 10px;">Graduação</th>
                                                    <th style="font-size: 10px;">Data</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($secondColumn as $graduation)
                                                <tr>
                                                    <td style="font-size: 9px; text-align: center;">
                                                        @if($graduation['color'])
                                                            <div style="width: 12px; height: 12px; background-color: {{ $graduation['color'] }}; border: 1px solid #000; margin: 0 auto; border-radius: 2px;"></div>
                                                        @endif
                                                    </td>
                                                    <td style="font-size: 9px;">{{ $graduation['name'] }}</td>
                                                    <td style="font-size: 9px;">{{ $graduation['date'] ?? '-' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 35%;">
                <h3 style="text-align: center;">Dados da Atividade</h3>
                <p style="text-align: center;"><b>Graduação:</b><br> 
                <span style="display: inline-block; width: 12px; height: 12px;  background-color: {{ $member['graduation_color'] }}; border: 1px solid #000; border-radius: 2px; vertical-align: middle; margin-right: 4px;"></span>
                <span style="font-size: 20px;">{{ $member['graduation'] }}</span></p>
                <hr>
                <p style="text-align: center;"><b>Última Atualização:</b><br> {{ $member['last_update'] }}</p>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
