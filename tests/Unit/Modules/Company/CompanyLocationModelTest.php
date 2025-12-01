<?php
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);


use App\Modules\Company\Domain\Models\CompanyLocation;
use App\Modules\Company\Domain\Models\Company;
use App\Modules\User\Domain\Models\User;

test('company location belongs to a company', function () {
    $user = User::factory()->create();
    $company = Company::factory()->create(['user_id' => $user->id]);
    $location = CompanyLocation::factory()->create([
        'company_id' => $company->id,
        'address' => 'Test Address 123',
    ]);

    expect($location->company)
        ->toBeInstanceOf(Company::class)
        ->and($location->company->id)->toBe($company->id);
});

test('company location has address field', function () {
    $user = User::factory()->create();
    $company = Company::factory()->create(['user_id' => $user->id]);
    $location = CompanyLocation::factory()->create([
        'company_id' => $company->id,
        'address' => '123 Main Street, City, Country',
    ]);

    expect($location->address)->toBe('123 Main Street, City, Country');
});

test('company can have multiple locations', function () {
    $user = User::factory()->create();
    $company = Company::factory()->create(['user_id' => $user->id]);

    CompanyLocation::factory()->create([
        'company_id' => $company->id,
        'address' => 'Location 1',
    ]);

    CompanyLocation::factory()->create([
        'company_id' => $company->id,
        'address' => 'Location 2',
    ]);

    expect($company->locations)->toHaveCount(2);
});
