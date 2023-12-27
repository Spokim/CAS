<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class Work_shiftFactory extends Factory
{

    public function definition() {
        return [
            'work_date' => $this->faker->date(),
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i'),
            'work_minutes' => $this->faker->numberBetween(0, 480),
            'overtime_minutes' => $this->faker->numberBetween(0, 480),
            'user_id' => function () {
                return UserFactory::new()->create()->id;
            },
        ];
    }
}