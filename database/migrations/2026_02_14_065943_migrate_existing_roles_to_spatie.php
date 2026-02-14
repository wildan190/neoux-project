<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $companyUsers = DB::table('company_users')->get();

        foreach ($companyUsers as $companyUser) {
            // Map old roles to new Spatie roles
            $roleName = $companyUser->role;
            if ($roleName === 'admin') {
                $roleName = 'company_admin';
            }

            $role = Role::where('name', $roleName)->first();

            if ($role) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $role->id,
                    'model_type' => 'Modules\User\Models\User',
                    'model_id' => $companyUser->user_id,
                    'company_id' => $companyUser->company_id,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('model_has_roles')
            ->where('model_type', 'Modules\User\Models\User')
            ->delete();
    }
};
