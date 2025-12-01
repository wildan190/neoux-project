<?php
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

use App\Modules\Company\Presentation\Http\Requests\StoreCompanyRequest;

test('store company request requires name', function () {
    $request = StoreCompanyRequest::create('/companies', 'POST', [
        'business_category' => 'Technology',
        'category' => 'supplier',
    ]);

    $request->setContainer(app());
    $request->validateResolved();

    expect(false)->toBeTrue(); // This should fail validation
})->throws(\Illuminate\Validation\ValidationException::class);

test('store company request requires business_category', function () {
    $request = StoreCompanyRequest::create('/companies', 'POST', [
        'name' => 'Test Company',
        'category' => 'supplier',
    ]);

    $request->setContainer(app());
    $request->validateResolved();

    expect(false)->toBeTrue(); // This should fail validation
})->throws(\Illuminate\Validation\ValidationException::class);

test('store company request requires valid category', function () {
    $request = StoreCompanyRequest::create('/companies', 'POST', [
        'name' => 'Test Company',
        'business_category' => 'Technology',
        'category' => 'invalid-category',
    ]);

    $request->setContainer(app());
    $request->validateResolved();

    expect(false)->toBeTrue(); // This should fail validation
})->throws(\Illuminate\Validation\ValidationException::class);

test('store company request accepts valid category values', function ($category) {
    $rules = (new StoreCompanyRequest())->rules();

    $data = [
        'name' => 'Test Company',
        'business_category' => 'Technology',
        'category' => $category,
    ];

    $validator = validator($data, $rules);

    expect($validator->passes())->toBeTrue();
})->with(['buyer', 'supplier', 'vendor']);

test('store company request passes with valid data', function () {
    $rules = (new StoreCompanyRequest())->rules();

    $data = [
        'name' => 'Test Company',
        'business_category' => 'Technology',
        'category' => 'supplier',
        'email' => 'test@company.com',
        'phone' => '1234567890',
    ];

    $validator = validator($data, $rules);

    expect($validator->passes())->toBeTrue();
});
