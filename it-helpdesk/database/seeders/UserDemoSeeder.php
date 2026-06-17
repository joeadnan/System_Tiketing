<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserDemoSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::first();
        $location = Location::first();

        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@demo.local', 'role' => 'super_admin', 'level' => null],
            ['name' => 'IT Manager', 'email' => 'manager@demo.local', 'role' => 'manager', 'level' => null],
            ['name' => 'L1 Helpdesk', 'email' => 'l1@demo.local', 'role' => 'l1_agent', 'level' => 'L1'],
            ['name' => 'L2 Technical Support', 'email' => 'l2@demo.local', 'role' => 'l2_agent', 'level' => 'L2'],
            ['name' => 'L3 Developer', 'email' => 'l3@demo.local', 'role' => 'l3_agent', 'level' => 'L3'],
            ['name' => 'Demo User', 'email' => 'user@demo.local', 'role' => 'user', 'level' => null],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'department_id' => $department?->id,
                    'location_id' => $location?->id,
                    'role' => $user['role'],
                    'level' => $user['level'],
                    'is_active' => true,
                ]
            );
        }
    }
}
