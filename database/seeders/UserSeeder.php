<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@example.com'], // Tolok ukur keunikan (jika email ini ada, dia di-update. Jika tidak, dia dibuat baru)
            [
                'name' => 'Arief Kurniawan',
                'password' => bcrypt('admin123'), // Direkomendasikan ganti password yang lebih kuat saat fix production nanti
            ]
        );

        $user->assignRole('admin');
    }
}
