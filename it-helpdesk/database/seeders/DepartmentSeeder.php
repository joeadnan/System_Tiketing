<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['IT', 'Finance', 'HRD', 'Sales', 'Operations'] as $name) {
            Department::updateOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
