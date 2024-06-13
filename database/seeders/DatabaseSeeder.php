<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Hamza',
            'email' => 'hamza@hcodestudio.fr'
        ])->each(function ($user) {
            \App\Models\Site::factory(5)->create([
                'user_id' => $user->id
            ])->each(function ($site) {
                \App\Models\Todo::factory(5)->create([
                    'user_id' => $site->user_id,
                    'site_id' => $site->id
                ]);
            });
        });

        \App\Models\User::factory(5)->create([])->each(function ($user) {
            \App\Models\Site::factory(5)->create([
                'user_id' => $user->id
            ])->each(function ($site) {
                \App\Models\Todo::factory(5)->create([
                    'user_id' => $site->user_id,
                    'site_id' => $site->id
                ]);
            });
        });
    }
}
