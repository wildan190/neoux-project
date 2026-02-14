<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Modules\User\Models\User;
use Modules\Company\Models\Company;
use Spatie\Permission\Models\Role;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_different_roles_in_different_companies()
    {
        // Seed roles
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $company1 = Company::factory()->create(['user_id' => User::factory()->create()->id]); // Not owner
        $company2 = Company::factory()->create(['user_id' => User::factory()->create()->id]); // Not owner

        // Assign 'approver' in company 1
        setPermissionsTeamId($company1->id);
        $user->assignRole('approver');

        // Assign 'buyer' in company 2
        setPermissionsTeamId($company2->id);
        $user->assignRole('buyer');

        // Verify company 1
        setPermissionsTeamId($company1->id);
        $this->assertTrue($user->hasRole($company1->id, 'approver'));
        $this->assertFalse($user->hasRole($company1->id, 'buyer'));
        $this->assertEquals('approver', $user->getRoleInCompany($company1->id));

        // Verify company 2
        setPermissionsTeamId($company2->id);
        $this->assertTrue($user->hasRole($company2->id, 'buyer'));
        $this->assertFalse($user->hasRole($company2->id, 'approver'));
        $this->assertEquals('buyer', $user->getRoleInCompany($company2->id));
    }

    public function test_owner_always_has_all_permissions()
    {
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->hasRole($company->id, 'any_role'));
        $this->assertEquals('owner', $user->getRoleInCompany($company->id));
    }
}
