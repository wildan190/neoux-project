<?php

namespace Database\Factories;

use App\Modules\Company\Domain\Models\Company;
use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->company(),
            'business_category' => $this->faker->randomElement(['Technology', 'Manufacturing', 'Retail', 'Services']),
            'category' => $this->faker->randomElement(['buyer', 'supplier', 'vendor']),
            'status' => 'pending',
            'logo' => null,
            'npwp' => $this->faker->numerify('##.###.###.#-###.###'),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
            'phone' => $this->faker->phoneNumber(),
            'tag' => $this->faker->words(3, true),
            'country' => $this->faker->country(),
            'registered_date' => $this->faker->date(),
            'address' => $this->faker->address(),
            'description' => $this->faker->paragraph(),
        ];
    }
}
