<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\ProductImage;

class ProductImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductImage::factory()
            ->times(50)
            ->create();
    }
}
