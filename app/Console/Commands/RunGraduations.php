<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\GraduationUser;
use App\Models\Graduation;
use App\Models\Profile;
use League\Csv\Reader;
use League\Csv\Exception;

class RunGraduations extends Command
{
    protected $signature = 'app:run-graduations {file : Caminho do arquivo CSV}';
    protected $description = 'Importa graduações do CSV para o banco de dados';

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

            foreach ($records as $data) {

                $data = array_map(function ($value) {
                    if ($value === null) return null;

                    // Primeiro força pra UTF-8
                    $value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');

                    // Opcional: corrige lixo residual tipo ï¿½
                    $value = str_replace("ï¿½", "º", $value);

                    return trim($value);
                }, $data);
                // Converte a data para o formato Y-m-d
                $date = null;

                if (!empty($data['DATA'])) {
                    $dateString = $data['DATA'];

                    $formats = ['d-m-Y', 'd.m.Y'];

                    foreach ($formats as $format) {
                        try {
                            $date = Carbon::createFromFormat($format, $dateString)->format('Y-m-d');
                            break; // se deu certo, sai do loop
                        } catch (\Exception $e) {
                            continue; // tenta o próximo formato
                        }
                    }
                }
                $profileId = Profile::where("number_kak", str_replace(".", "", $data['MEMBRO']))->first()->id ?? null;
                $graduation = Graduation::where("name", $data['GRADUACAO'])->first() ?? null;
                // Insere no banco
                if (!$profileId) {
                    $this->error("Usuário não encontrado para MEMBRO: " . $data['MEMBRO']);
                    continue; // pula para o próximo registro
                }
                if(!$graduation) {
                    $this->error("Graduação não encontrada: " . $data['GRADUACAO']);
                    continue; // pula para o próximo registro
                }
                GraduationUser::updateOrCreate([
                    'profile_id' => $profileId,
                    'graduation_id' => Graduation::where("name", $data['GRADUACAO'])->first()->id,
                ],[
                    'date' => $date,
                    'value' => $data['VALOR'] ?: null,
                    'kihon' => $data['KIHON'] ?: null,
                    'kata' => $data['KATA'] ?: null,
                    'kumite' => $data['KUMITE'] ?: null,
                    'location' => $data['LOCAL'] ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

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
