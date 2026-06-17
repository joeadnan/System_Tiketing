<?php

namespace Database\Seeders;

use App\Models\SlaPolicy;
use Illuminate\Database\Seeder;

class SlaPolicySeeder extends Seeder
{
    public function run(): void
    {
        $policies = [
            ['priority_code' => 'P1', 'priority_label' => 'Kritis', 'response_minutes' => 15, 'resolution_minutes' => 240],
            ['priority_code' => 'P2', 'priority_label' => 'Tinggi', 'response_minutes' => 30, 'resolution_minutes' => 480],
            ['priority_code' => 'P3', 'priority_label' => 'Sedang', 'response_minutes' => 120, 'resolution_minutes' => 2880],
            ['priority_code' => 'P4', 'priority_label' => 'Rendah', 'response_minutes' => 240, 'resolution_minutes' => 7200],
        ];

        foreach ($policies as $policy) {
            SlaPolicy::updateOrCreate(
                ['priority_code' => $policy['priority_code']],
                $policy + ['warning_percentage' => 20, 'is_active' => true]
            );
        }
    }
}
