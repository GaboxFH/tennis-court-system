<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word,
            'date' => $this->faker->dateTime($max = 'now', $timezone = null),
            'court' => $this->faker->randomDigit(1, 17),
            'startTime' => $this->faker->time(),
            'duration' => $this->faker->time(),
            'user_id' => 1,
            'players' => $this->faker->name,
        ];
    }
}
