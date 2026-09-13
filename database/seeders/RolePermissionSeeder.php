<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Referentiel des roles et permissions.
 *
 * Deux permissions suffisent au perimetre actuel : `prompts.moderate` (file
 * de validation) et `platform.manage` (back-office complet — comptes,
 * referentiel, ventes, audit). Un role `user` sans aucune permission peut
 * neanmoins creer, vendre et acheter des prompts : ces actions se
 * verifient par propriete (voir `OwnershipViolationException`), pas par
 * permission.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['prompts.moderate', 'platform.manage'];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $admin = Role::findOrCreate('admin');
        $admin->syncPermissions($permissions);

        $moderator = Role::findOrCreate('moderator');
        $moderator->syncPermissions(['prompts.moderate']);

        Role::findOrCreate('user');
    }
}
