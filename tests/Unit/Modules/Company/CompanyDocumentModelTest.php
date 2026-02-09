<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

use Modules\Company\Domain\Models\Company;
use Modules\Company\Domain\Models\CompanyDocument;
use Modules\User\Domain\Models\User;

test('company document belongs to a company', function () {
    $user = User::factory()->create();
    $company = Company::factory()->create(['user_id' => $user->id]);
    $document = CompanyDocument::factory()->create(['company_id' => $company->id]);

    expect($document->company)
        ->toBeInstanceOf(Company::class)
        ->and($document->company->id)->toBe($company->id);
});

test('company document has required file_path', function () {
    $user = User::factory()->create();
    $company = Company::factory()->create(['user_id' => $user->id]);
    $document = CompanyDocument::factory()->create([
        'company_id' => $company->id,
        'file_path' => 'documents/test.pdf',
    ]);

    expect($document->file_path)->toBe('documents/test.pdf');
});
