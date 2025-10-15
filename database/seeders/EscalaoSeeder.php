<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Escalao;

class EscalaoSeeder extends Seeder
{
    public function run(): void
    {
        $escaloes = [
            ['start_date' => '2014-04-28', 'end_date' => '2025-10-05', 'name' => 'Infantis'],
            ['start_date' => '2012-04-28', 'end_date' => '2014-04-27', 'name' => 'Iniciados'],
            ['start_date' => '2010-02-10', 'end_date' => '2012-04-27', 'name' => 'Juvenis'],
            ['start_date' => '2008-02-10', 'end_date' => '2010-02-09', 'name' => 'Cadetes'],
            ['start_date' => '2006-02-10', 'end_date' => '2008-02-09', 'name' => 'Juniores'],
            ['start_date' => '1899-12-31', 'end_date' => '2006-05-08', 'name' => 'Seniores'],
        ];

        foreach ($escaloes as $escalao) {
            Escalao::updateOrCreate($escalao);
        }
    }
}
