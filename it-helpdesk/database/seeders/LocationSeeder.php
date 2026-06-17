<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Head Office', 'building' => 'Main Building', 'floor' => '1', 'room' => 'Lobby'],
            ['name' => 'Finance Room', 'building' => 'Main Building', 'floor' => '2', 'room' => 'Finance'],
            ['name' => 'Server Room', 'building' => 'Main Building', 'floor' => '3', 'room' => 'Server'],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(['name' => $location['name']], $location + ['is_active' => true]);
        }
    }
}
