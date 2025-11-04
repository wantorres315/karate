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
use Illuminate\Database\Seeder;
use App\Role as RoleEnum;
use Spatie\Permission\Models\Role as SpatieRole;

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

            // Garante que os roles do Enum existam (Spatie)
            foreach (\App\Role::cases() as $case) {
                SpatieRole::firstOrCreate(['name' => $case->value]);
            }

            foreach ($records as $record) {
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
                $name = trim($record["NOME"] ?? 'usuario');
                $email = trim($record['E-MAIL']);
                
                // Verifica se já existe usuário
                $user = User::where('email', $email)->first();
                  $this->info("\nProcessando: " . ($record['NOME'] ?? 'N/A'));
              
                if ($user) {
                    // Verifica se já existe família para este usuário
                    $family = \App\Models\Family::where('user_id', $user->id)->first();
                    if (!$family) {
                        // Cria família se não existir
                        $family = \App\Models\Family::create([
                            'name' => $user->name,
                            'user_id' => $user->id,
                        ]);
                       
                        $user->profiles->each(function($profile) use ($family) {
                            $profile->families()->syncWithoutDetaching([$family->id]);
                        });
                        $this->info("Família criada para usuário existente : " . $user->email);
                    }
                    // Cria profile e vincula à família
                    $profile = Profile::create([
                        'user_id' => $user->id, // não vincula direto ao user
                        "name" => $name,
                        'number_kak' => str_replace(".", "", $record['Nº KAK']),
                        'number_fnkp' => $record['FNKP'] ?? null,
                        'number_jks' => $record['Nº JKS'] ?? null,
                        'number_cit' => $record['CIT'] ?? null,
                        'number_tptd' => $record['TPTD'] ?? null,
                        'arbitrator_id' => $arbitratorId,
                        'document_type' => $record['DOCIDENTIFICACAO'] ?? null,
                        'document_number' => $record['NIDENTIFICACAO'] ?? null,
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
                        'club_id' => Club::where("id",$record['DOJO'])->first()->id ?? null,
                        "status" => strtolower(trim($record['ESTADO'] ?? 'ativo')) == 'inativo' ? 'inactive' : 'active',
                        "photo" => "/storage/clubs/logos/club.png",
                    ]);
                    
                    $profile->families()->syncWithoutDetaching([$family->id]);
                } else {
                    // Cria usuário e profile
                    $password = str_replace(".", "", $record['Nº KAK'] ?? "password");
                    $this->info("Usuario sem familia criada com senha padrao: ".  $password); 
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => bcrypt($password),
                    ]);
                                        // Cria profile e vincula à família
                    $profile = Profile::create([
                        'user_id' => $user->id,
                        "name" => $name,
                        'number_kak' => str_replace(".", "", $record['Nº KAK']),
                        'number_fnkp' => $record['FNKP'] ?? null,
                        'number_jks' => $record['Nº JKS'] ?? null,
                        'number_cit' => $record['CIT'] ?? null,
                        'number_tptd' => $record['TPTD'] ?? null,
                        'arbitrator_id' => $arbitratorId,
                        'document_type' => $record['DOCIDENTIFICACAO'] ?? null,
                        'document_number' => $record['NIDENTIFICACAO'] ?? null,
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
                        'club_id' => Club::where("id",$record['DOJO'])->first()->id ?? null,
                        "status" => strtolower(trim($record['ESTADO'] ?? 'ativo')) == 'inativo' ? 'inactive' : 'active',
                        "photo" => "/storage/clubs/logos/club.png",
                    ]);
                   //$profile->families()->syncWithoutDetaching([$family->id]);
                }
                // === Regras de Role (todo usuário precisa ter uma) ===
                if ($user->email === 'rmpsilva@gmail.com') {
                    // Apenas este como super_admin
                    $user->syncRoles([\App\Role::SUPER_ADMIN->value]);
                } else {
                    // Papel principal pelo TIPO DE MEMBRO
                    $tipo = strtolower(trim($record['TIPO DE MEMBRO'] ?? ''));
                    switch ($tipo) {
                        case 'treinador grau i':
                            $user->assignRole(\App\Role::TREINADOR_GRAU_I->value);
                            break;
                        case 'treinador grau ii':
                            $user->assignRole(\App\Role::TREINADOR_GRAU_II->value);
                            break;
                        case 'treinador grau iii':
                            $user->assignRole(\App\Role::TREINADOR_GRAU_III->value);
                            break;
                        default:
                            // padrão praticante
                            $user->assignRole(\App\Role::PRATICANTE->value);
                            break;
                    }

                    // Se for árbitro no CSV, adiciona role de árbitro também
                    if (!empty($record['ÁRBITRO'])) {
                        $user->assignRole(\App\Role::ARBITRATOR->value);
                    }

                    // Garantia: se por algum motivo ainda não tem role, define praticante
                    if (!$user->roles()->exists()) {
                        $user->assignRole(\App\Role::PRATICANTE->value);
                    }
                }
                // === fim regras de Role ===

                $bar->advance();
            }

            $bar->finish();
            $this->info("\nImportação concluída com sucesso!");
        } catch (Exception $e) {
            $this->error("Erro ao ler o arquivo CSV: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}





