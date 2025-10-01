<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Profile;
use App\Models\Arbitrator;
use App\Models\Club;
use League\Csv\Reader;
use League\Csv\Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Role;
use App\Permission;

class RunUsers extends Command
{
    protected $signature = 'app:run-users {file : Caminho do arquivo CSV}';
    protected $description = 'Importar usuários do CSV para users + profiles';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("Arquivo não encontrado: {$filePath}");
            return 1;
        }

        try {
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setDelimiter(';');   // CSV usa ; como separador
            $csv->setHeaderOffset(0);  // primeira linha = cabeçalho
            $records = iterator_to_array($csv->getRecords());

            $bar = $this->output->createProgressBar(count($records));
            $bar->start();

            foreach ($records as $record) {
                $estado = strtolower(trim($record['ESTADO'] ?? 'ativo'));
                $statusMap = [
                    'inativo' => 'inactive',
                    'ativo' => 'active',
                ];
                dump( $record['NOME'], $statusMap[$estado] ?? 'active');
                // 1️⃣ Criar ou atualizar usuário
                if($record["NOME"]){

                   $email = $record['E-MAIL'] ?? null;

                    // Se não veio email ou já existe no banco, gera um novo único
                    if (empty($email) || User::where('email', $email)->exists()) {
                        do {
                            $email = Str::uuid() . '@example.com';
                        } while (User::where('email', $email)->exists());
                    }

                    $user = User::updateOrCreate(
                        ['email' => $email],
                        [
                            'name' => $record['NOME'],
                            'password' => bcrypt(str_replace(".", "",$record['Nº KAK'])),
                        ]
                    );

                    // 2️⃣ Criar árbitro, se necessário
                    $arbitratorId = null;
                    if (!empty($record['ÁRBITRO'])) {
                        $arbitrator = Arbitrator::firstOrCreate(['name' => $record['ÁRBITRO']]);
                        $arbitratorId = $arbitrator->id;
                    }
    
                    // 3️⃣ Tratar datas
                    $birthDate = null;

                    if (!empty($record['DATA DE NASCIMENTO'])) {
                        try {
                            $date = Carbon::createFromFormat('d-m-Y', $record['DATA DE NASCIMENTO']);

                            // Verifica se houve erros de parsing
                            $errors = Carbon::getLastErrors();
                            if ($errors['error_count'] === 0 && $errors['warning_count'] === 0) {
                                $birthDate = $date->format('Y-m-d');
                            }
                        } catch (\Exception $e) {
                            $birthDate = null;
                        }
                    }
    
                    $admissionDate = null;

                    if (!empty($record['ADMISSÃO'])) {
                        $dateString = $record['ADMISSÃO'];

                        $formats = ['d-m-Y', 'd.m.Y'];

                        foreach ($formats as $format) {
                            try {
                                $admissionDate = Carbon::createFromFormat($format, $dateString)->format('Y-m-d');
                                break; // se deu certo, sai do loop
                            } catch (\Exception $e) {
                                continue; // tenta o próximo formato
                            }
                        }
                    }
    
                    // 4️⃣ Criar ou atualizar profile vinculado ao usuário
                    Profile::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'number_kak' =>  str_replace(".", "",$record['Nº KAK']),
                            'number_fnkp' => $record['FNKP'] ?? null,
                            'number_jks' => $record['Nº JKS'] ?? null,
                            'number_cit' => $record['CIT'] ?? null,
                            'number_tptd' => $record['TPTD'] ?? null,
                            'arbitrator_id' => $arbitratorId,
                            'document_type' => $record['DOC. IDENTIFICAÇÃO'] ?? null,
                            'document_number' => $record['Nº IDENTIFICAÇÃO'] ?? null,
                            'birth_date' => $birthDate,
                            'father_name' => $record['NOME PAI'] ?? null,
                            'mother_name' => $record['NOME MÃE'] ?? null,
                            'profession' => $record['PROFISSÃO'] ?? null,
                            'address' => $record['MORADA'] ?? null,
                            'postal_code' => $record['CÓD. POSTAL'] ?? null,
                            'city' => $record['LOCALIDADE'] ?? null,
                            'nationality' => $record['NACIONALIDADE'] ?? null,
                            'phone_number' => $record['TELEFONE'] ?? null,
                            'cell_number' => $record['TELEMÓVEL'] ?? null,
                            'admission_date' => $admissionDate,
                            'district' => $record['DISTRITO'] ?? null,
                            'credits' => $record['CRÉDITOS'] ?? null,
                            'graduation' => $record['GRADUAÇÃO'] ?? null,
                            'club_id' => Club::where("id",$record['DOJO'])->first()->id ?? null,
                            "status" => $statusMap[$estado] ?? 'active',
                        ]
                    );
                }
                
                if($arbitratorId){
                    $user->assignRole(Role::ARBITRATOR->value);
                }

                switch ($record['TIPO DE MEMBRO']) {
                    case 'treinador grau I':
                        $user->assignRole(Role::TREINADOR_GRAU_I->value);
                        break;
                    case 'treinador grau II':
                        $user->assignRole(Role::TREINADOR_GRAU_II->value);
                        break;
                    case 'treinador grau III':
                        $user->assignRole(Role::TREINADOR_GRAU_III->value);
                        break;
                     
                    case 'praticante':
                        $user->assignRole(Role::PRATICANTE->value);
                        break;
                    default:
                        $user->assignRole(Role::PRATICANTE->value);
                        break;
                }
                
                $bar->advance();
            }

            $bar->finish();
            $this->info("\nImportação concluída com sucesso!");

        } catch (Exception $e) {
            $this->error("Erro ao ler o CSV: " . $e->getMessage());
            return 1;
        } catch (\Exception $e) {
            $this->error("Erro ao salvar dados: " . $e->getMessage() . " na linha " . $e->getLine());
            return 1;
        }

        return 0;
    }
}
