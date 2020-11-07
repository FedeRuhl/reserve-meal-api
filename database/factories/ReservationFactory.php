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
            'scheduled_date' => $this->faker->dateTimeBetween($startDate = 'now', $endDate = '+3 months', $timezone = null),
            'user_id' => $this->faker->numberBetween(1, 51),
            'product_id' => $this->faker->numberBetween(1, 50),
            'price' => $this->randomFloat($nbMaxDecimals = 2, $min = 0, $max = NULL)
        ];
    }
}
