<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClubInstructors;
use League\Csv\Reader;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Club;

class RunInstructor extends Command
{
    protected $signature = 'app:run-instructors {file : Caminho do arquivo CSV}';
    protected $description = 'Importar instrutores do CSV para a tabela instructors ';

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

                $user = User::whereHas('profile', function ($q) use ($record) {
                    $q->whereRaw("REPLACE(number_kak, '.', '') = ?", [$record['NUMERO_MEMBRO']]);
                })->first();

                if (!$user) {
                    $this->warn("Usuário não encontrado: {$record['NUMERO_MEMBRO']}");
                    $bar->advance();
                    continue;
                }

                $club = Club::where('id', $record['NUMERO_KAK'])->first();

                if (!$club) {
                    $this->warn("Clube não encontrado: {$record['NUMERO_KAK']}");
                    $bar->advance();
                    continue;
                }


                ClubInstructors::updateOrCreate(
                    [
                        'club_id' => $club->id,
                        'user_id' => $user->id,
                    ],
                    [] // sem campos adicionais, só garante a existência
                );

                $bar->advance();
            }
            $bar->finish();
            $this->info("Importação de instrutores concluída!");
        } catch (\Exception $e) {
            $this->error("Erro ao processar o arquivo CSV: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
