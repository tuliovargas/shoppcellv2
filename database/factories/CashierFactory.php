<?php

namespace Database\Factories;

use App\Models\Cashier;
use Illuminate\Database\Eloquent\Factories\Factory;

class CashierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cashier::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'total_value' => rand(50, 1550),
            'user_id' => 1,
            'created_at' => $this->faker->dateTimeBetween('-2 years')
        ];
    }
}
