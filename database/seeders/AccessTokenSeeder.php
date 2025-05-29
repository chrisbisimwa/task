<?php

namespace Database\Seeders;

use App\Models\AccessToken;
use Illuminate\Database\Seeder;

class AccessTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AccessToken::factory()
            ->count(5)
            ->create();
    }
}
