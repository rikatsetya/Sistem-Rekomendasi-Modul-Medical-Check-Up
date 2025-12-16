<?php

namespace Database\Factories;

use App\Models\Value;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ValueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Value::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nilai' => $this->faker->text(255),
            'tahun' => $this->faker->text(255),
            'user_id' => \App\Models\User::factory(),
            'sub_category_id' => \App\Models\SubCategory::factory(),
        ];
    }
}
