<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Reservation;

class ReservationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Reservation::factory()
        ->times(20)
        ->create();
    }
}
