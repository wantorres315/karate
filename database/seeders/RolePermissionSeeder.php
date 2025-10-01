<?php

namespace Database\Seeders;

use App\Permission;
use App\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Models\Role as SpatieRole;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Permission::cases() as $permission) {
            SpatiePermission::updateOrCreate(['name' => $permission->value]);
        }

        $roles = [
            ROLE::TREINADOR_GRAU_I->value => [
            ],
            ROLE::TREINADOR_GRAU_II->value => [
            ],
            ROLE::TREINADOR_GRAU_III->value => [
            ],
            ROLE::ARBITRATOR->value => [
            ],
            ROLE::PRATICANTE->value => [
            ],
            ROLE::SUPER_ADMIN->value => [
                
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = SpatieRole::updateOrCreate(['name' => $roleName]);

            $role->syncPermissions(array_map(fn ($p) => $p->value, $permissions));
        }
    }
}