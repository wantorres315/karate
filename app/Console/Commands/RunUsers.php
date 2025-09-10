<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Profile;
use App\Models\Arbitrator;
use League\Csv\Reader;
use League\Csv\Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;

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

                // 1️⃣ Criar ou atualizar usuário
                if($record["NOME"]){

                    $user = User::updateOrCreate(
                        ['email' => $record['E-MAIL'] ?? Str::uuid() . '@example.com'],
                        [
                            'name' => $record['NOME'],
                            'password' => bcrypt('secret'), // senha padrão
                        ]
                    );
    
                    // 2️⃣ Criar árbitro, se necessário
                    $arbitratorId = null;
                    if (!empty($record['ÁRBITRO'])) {
                        $arbitrator = Arbitrator::firstOrCreate(['name' => $record['ÁRBITRO']]);
                        $arbitratorId = $arbitrator->id;
                    }
    
                    // 3️⃣ Tratar datas
                    $birthDate = !empty($record['DATA DE NASCIMENTO']) 
                        ? Carbon::createFromFormat('d-m-Y', $record['DATA DE NASCIMENTO'])->format('Y-m-d')
                        : null;
    
                    $admissionDate = !empty($record['ADMISSÃO']) 
                        ? Carbon::createFromFormat('d-m-Y', $record['ADMISSÃO'])->format('Y-m-d')
                        : null;
    
                    // 4️⃣ Criar ou atualizar profile vinculado ao usuário
                    Profile::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'number_kak' => $record['Nº KAK'],
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
                            'club_id' => $record
                        ]
                    );
                }

                $bar->advance();
            }

            $bar->finish();
            $this->info("\nImportação concluída com sucesso!");

        } catch (Exception $e) {
            $this->error("Erro ao ler o CSV: " . $e->getMessage());
            return 1;
        } catch (\Exception $e) {
            $this->error("Erro ao salvar dados: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
