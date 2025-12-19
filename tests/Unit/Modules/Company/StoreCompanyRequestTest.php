<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

use App\Modules\Company\Presentation\Http\Requests\StoreCompanyRequest;

test('store company request requires name', function () {
    $rules = (new StoreCompanyRequest)->rules();
    $data = [
        'business_category' => 'Technology',
        'category' => 'supplier',
    ];

    $validator = validator($data, $rules);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue();
});

test('store company request requires business_category', function () {
    $rules = (new StoreCompanyRequest)->rules();
    $data = [
        'name' => 'Test Company',
        'category' => 'supplier',
    ];

    $validator = validator($data, $rules);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('business_category'))->toBeTrue();
});

test('store company request requires valid category', function () {
    $rules = (new StoreCompanyRequest)->rules();
    $data = [
        'name' => 'Test Company',
        'business_category' => 'Technology',
        'category' => 'invalid-category',
    ];

    $validator = validator($data, $rules);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('category'))->toBeTrue();
});

test('store company request accepts valid category values', function ($category) {
    $rules = (new StoreCompanyRequest)->rules();

    $data = [
        'name' => 'Test Company',
        'business_category' => 'Technology',
        'category' => $category,
        'documents' => [
            \Illuminate\Http\UploadedFile::fake()->create('doc.pdf', 100),
        ],
    ];

    $validator = validator($data, $rules);

    expect($validator->passes())->toBeTrue();
})->with(['buyer', 'supplier', 'vendor']);

test('store company request passes with valid data', function () {
    $rules = (new StoreCompanyRequest)->rules();

    $data = [
        'name' => 'Test Company',
        'business_category' => 'Technology',
        'category' => 'supplier',
        'email' => 'test@company.com',
        'phone' => '1234567890',
        'documents' => [
            \Illuminate\Http\UploadedFile::fake()->create('doc.pdf', 100),
        ],
    ];

    $validator = validator($data, $rules);

    expect($validator->passes())->toBeTrue();
});
