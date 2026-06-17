<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TicketCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Akses & Akun', 'default_level' => 'L1'],
            ['name' => 'Hardware', 'default_level' => 'L1'],
            ['name' => 'Jaringan / Konektivitas', 'default_level' => 'L2'],
            ['name' => 'Software / Aplikasi', 'default_level' => 'L1'],
            ['name' => 'ERP / Sistem Bisnis', 'default_level' => 'L2'],
            ['name' => 'Email & Kolaborasi', 'default_level' => 'L1'],
            ['name' => 'Infrastruktur / Server', 'default_level' => 'L2'],
            ['name' => 'Keamanan', 'default_level' => 'L3'],
        ];

        foreach ($categories as $category) {
            TicketCategory::updateOrCreate(
                ['code' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'default_level' => $category['default_level'],
                    'is_active' => true,
                ]
            );
        }
    }
}
