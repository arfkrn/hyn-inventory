<?php

namespace Database\Factories;

use App\Models\Bahan;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BahanMasuk;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BahanMasukItem>
 */
class BahanMasukItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bahan_masuk_id' => BahanMasuk::factory(),
            'bahan_id' => Bahan::factory(),
            'jumlah' => fake()->numberBetween(1, 20)
        ];
    }
}
