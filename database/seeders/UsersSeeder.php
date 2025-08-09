<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $empresaId = DB::table('empresas')->where('ruc','1790012345001')->value('id');
        $localId   = DB::table('locales')->where('empresa_id',$empresaId)->value('id');

        // Usuario SuperAdmin global de la empresa
        $userId = DB::table('usuarios')->updateOrInsert(
            ['empresa_id' => $empresaId, 'email' => 'superadmin@vendepro.io'],
            [
                'local_id' => $localId,
                'nombre'   => 'Super Admin',
                'password_hash' => Hash::make('VendePro#2025'),
                'telefono' => null,
                'activo'   => 1,
                'debe_cambiar_password' => 0,
            ]
        );

        // Recuperar id real (updateOrInsert no lo retorna)
        $userId = DB::table('usuarios')->where('empresa_id',$empresaId)->where('email','superadmin@vendepro.io')->value('id');

        $rolId = DB::table('roles')->where('codigo','SUPER_ADMIN')->value('id');

        if ($userId && $rolId) {
            DB::table('usuario_roles')->updateOrInsert(
                ['usuario_id' => $userId, 'rol_id' => $rolId],
                []
            );
        }

        // Usuario Administrador de local
        $adminLocalId = DB::table('usuarios')->updateOrInsert(
            ['empresa_id' => $empresaId, 'email' => 'adminlocal@vendepro.io'],
            [
                'local_id' => $localId,
                'nombre'   => 'Admin Local',
                'password_hash' => Hash::make('VendePro#2025'),
                'activo'   => 1,
            ]
        );
        $adminLocalId = DB::table('usuarios')->where('empresa_id',$empresaId)->where('email','adminlocal@vendepro.io')->value('id');
        $rolAdmin = DB::table('roles')->where('codigo','ADMIN_LOCAL')->value('id');
        if ($adminLocalId && $rolAdmin) {
            DB::table('usuario_roles')->updateOrInsert(
                ['usuario_id' => $adminLocalId, 'rol_id' => $rolAdmin],
                []
            );
        }
    }
}
