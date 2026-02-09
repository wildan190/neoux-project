<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

use Modules\Company\Domain\Models\Company;
use Modules\Company\Domain\Models\CompanyDocument;
use Modules\Company\Domain\Models\CompanyLocation;
use Modules\User\Domain\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('company can be created with valid data', function () {
    $company = Company::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Test Company',
        'business_category' => 'Technology',
        'category' => 'supplier',
    ]);

    expect($company)
        ->toBeInstanceOf(Company::class)
        ->and($company->name)->toBe('Test Company')
        ->and($company->business_category)->toBe('Technology')
        ->and($company->category)->toBe('supplier')
        ->and($company->status)->toBe('pending')
        ->and($company->user_id)->toBe($this->user->id);
});

test('company belongs to a user', function () {
    $company = Company::factory()->create(['user_id' => $this->user->id]);

    expect($company->user)
        ->toBeInstanceOf(User::class)
        ->and($company->user->id)->toBe($this->user->id);
});

test('company has many documents', function () {
    $company = Company::factory()->create(['user_id' => $this->user->id]);

    $document = CompanyDocument::factory()->create([
        'company_id' => $company->id,
    ]);

    expect($company->documents)
        ->toHaveCount(1)
        ->and($company->documents->first())->toBeInstanceOf(CompanyDocument::class)
        ->and($company->documents->first()->id)->toBe($document->id);
});

test('company has many locations', function () {
    $company = Company::factory()->create(['user_id' => $this->user->id]);

    $location = CompanyLocation::factory()->create([
        'company_id' => $company->id,
        'address' => 'Test Location Address',
    ]);

    expect($company->locations)
        ->toHaveCount(1)
        ->and($company->locations->first())->toBeInstanceOf(CompanyLocation::class)
        ->and($company->locations->first()->address)->toBe('Test Location Address');
});

test('company category must be valid enum value', function () {
    $company = Company::factory()->create([
        'user_id' => $this->user->id,
        'category' => 'buyer',
    ]);

    expect($company->category)->toBeIn(['buyer', 'supplier', 'vendor']);
});

test('company status defaults to pending', function () {
    $company = Company::factory()->create(['user_id' => $this->user->id]);

    expect($company->status)->toBe('pending');
});
