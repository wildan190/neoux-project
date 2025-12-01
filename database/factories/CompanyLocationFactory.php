<?php

namespace Database\Factories;

use App\Modules\Company\Domain\Models\CompanyLocation;
use App\Modules\Company\Domain\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyLocationFactory extends Factory
{
    protected $model = CompanyLocation::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'address' => $this->faker->address(),
        ];
    }
}
