<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\Graduation;


class GraduationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $graduations = [
            ['name' => '9º KYU', 'color' => 'white '],
            ['name' => '9/8º KYU', 'color' => 'white_yellow'],
            ['name' => '8º KYU', 'color' => 'yellow'],
            ['name' => '8/7º KYU', 'color' => 'yellow_orange🟡🟠'],
            ['name' => '7º KYU', 'color' => 'orange'],
            ['name' => '7/6º KYU', 'color' => 'orange_green'],
            ['name' => '6º KYU', 'color' => 'green'],
            ['name' => '6/5º KYU', 'color' => 'green_blue'],
            ['name' => '5º KYU', 'color' => 'blue'],
            ['name' => '5/4º KYU', 'color' => 'blue_purple'],
            ['name' => '4º KYU', 'color' => 'purple'],
            ['name' => '4/3º KYU', 'color' => 'purple_brown'],
            ['name' => '3º KYU', 'color' => 'brown'],
            ['name' => '2º KYU', 'color' => 'brown'],
            ['name' => '1º KYU', 'color' => 'brown'],
            ['name' => '1º DAN', 'color' => 'black'],
            ['name' => '2º DAN', 'color' => 'black'],
            ['name' => '3º DAN', 'color' => 'black'],
            ['name' => '4º DAN', 'color' => 'black'],
            ['name' => '5º DAN', 'color' => 'black'],
            ['name' => '6º DAN', 'color' => 'red_black'],
            ['name' => '7º DAN', 'color' => 'red_black'],
            ['name' => '8º DAN', 'color' => 'red_black'],
            ['name' => '9º DAN', 'color' => 'red'],
            ['name' => '10º DAN', 'color' => 'red'],
        ];

        foreach ($graduations as $graduation) {
            Graduation::updateOrCreate(['name' => $graduation['name']], ['color' => $graduation['color']]);
        }
    }
}
