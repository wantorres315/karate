<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Club;
use League\Csv\Reader;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RunClubs extends Command
{
    protected $signature = 'app:run-clubs {file : Caminho do arquivo CSV}';
    protected $description = 'Importar clubes do CSV para a tabela clubs';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("Arquivo não encontrado: {$filePath}");
            return 1;
        }

        try {
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            $records = iterator_to_array($csv->getRecords());

            $bar = $this->output->createProgressBar(count($records));
            $bar->start();

            foreach ($records as $record) {
                Club::updateOrCreate(
                    ['name' => $record['NOME CLUBE']], // chave única (pode trocar para Nº KAK)
                    [
                        'acronym' => $record['SIGLA'] ?? null,
                        'responsible_name' => $record['RESPONSÁVEL CLUBE'] ?? null,
                        'responsible_position' => $record['INSTRUTOR RESPONSÁVEL'] ?? null,
                        'address' => $record['MORADA'] ?? null,
                        'postal_code' => $record['CÓD. POSTAL'] ?? null,
                        'city' => $record['LOCALIDADE'] ?? null,
                        'phone_number' => $record['TELEFONE'] ?? null,
                        'cell_number' => $record['TELEMÓVEL'] ?? null,
                        'username_fnkp' => $record['USERNAME FNKP'] ?? null,
                        'username_password_fnkp' => $record['PASSWORD FNKP'] ?? null,
                        'email' => $record['E-MAIL'] ?? null,
                        'website' => $record['SITE'] ?? null,
                        'certificate_fnkp' => $record['Cert FNKP'] ?? null,
                        'status_year' => $record['ESTADO'] ?? null,
                        'status' => $record['ESTADO']  !== "Inativo" ? 'active' : 'inactive',
                    ]
                );

                $bar->advance();
            }

            $bar->finish();
            $this->info("\nImportação de clubes concluída!");
        } catch (\Exception $e) {
            $this->error("Erro: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
