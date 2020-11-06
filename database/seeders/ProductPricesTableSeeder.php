<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\ProductPrice;

class ProductPricesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductPrice::factory()
            ->times(50)
            ->create();
    }
}
