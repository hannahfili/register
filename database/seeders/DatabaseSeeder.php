<?php

namespace Database\Seeders;

use App\Models\Sclass;
use Database\Factories\SclassFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Sclass::factory()->create([
            'name' => 'A',
            'class_start' => date_create('2021-09-01'),
            'class_end' => date_create('2025-09-01'),
        ]);
        Sclass::factory()->create([
            'name' => 'B',
            'class_start' => date_create('2019-09-01'),
            'class_end' => date_create('2023-09-01'),
        ]);
        Sclass::factory()->create([
            'name' => 'C',
            'class_start' => date_create('2019-09-01'),
            'class_end' => date_create('2023-09-01'),
        ]);
        Sclass::factory()->create([
            'name' => 'D',
            'class_start' => date_create('2019-09-01'),
            'class_end' => date_create('2023-09-01'),
        ]);
        // $this->call(SclassFactory::class);
    }
}
