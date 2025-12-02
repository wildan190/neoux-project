<?php

namespace Database\Factories;

use App\Modules\Company\Domain\Models\Company;
use App\Modules\Company\Domain\Models\CompanyDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyDocumentFactory extends Factory
{
    protected $model = CompanyDocument::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'file_path' => 'company_documents/'.$this->faker->uuid().'.pdf',
        ];
    }
}
