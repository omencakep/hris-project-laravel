<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            ['name' => 'Alice Johnson',     'email' => 'alice.johnson@example.com',     'position' => 'HR Manager',          'phone' => '081234567890', 'address' => 'Jl. Melati No.1, Jakarta'],
            ['name' => 'Bob Smith',         'email' => 'bob.smith@example.com',         'position' => 'Finance Analyst',     'phone' => '081298765432', 'address' => 'Jl. Kenanga No.12, Bandung'],
            ['name' => 'Charlie Davis',     'email' => 'charlie.davis@example.com',     'position' => 'Marketing Lead',      'phone' => '082112345678', 'address' => 'Jl. Mawar No.45, Surabaya'],
            ['name' => 'Diana Moore',       'email' => 'diana.moore@example.com',       'position' => 'Sales Executive',     'phone' => '083134567890', 'address' => 'Jl. Anggrek No.3, Medan'],
            ['name' => 'Ethan Brown',       'email' => 'ethan.brown@example.com',       'position' => 'R&D Engineer',        'phone' => '081355544433', 'address' => 'Jl. Dahlia No.67, Yogyakarta'],
            ['name' => 'Fiona Clark',       'email' => 'fiona.clark@example.com',       'position' => 'Customer Support',    'phone' => '082198765432', 'address' => 'Jl. Flamboyan No.8, Semarang'],
            ['name' => 'George Lewis',      'email' => 'george.lewis@example.com',      'position' => 'IT Technician',       'phone' => '081223344556', 'address' => 'Jl. Cempaka No.19, Bali'],
            ['name' => 'Hannah Martin',     'email' => 'hannah.martin@example.com',     'position' => 'Operations Manager',  'phone' => '083344556677', 'address' => 'Jl. Kenari No.24, Malang'],
            ['name' => 'Ian Thompson',      'email' => 'ian.thompson@example.com',      'position' => 'Legal Advisor',       'phone' => '082299887766', 'address' => 'Jl. Kamboja No.30, Palembang'],
            ['name' => 'Julia White',       'email' => 'julia.white@example.com',       'position' => 'Procurement Officer', 'phone' => '081377788899', 'address' => 'Jl. Teratai No.11, Balikpapan'],
        ];

        foreach ($employees as $index => $emp) {
            // Insert into users
            $userId = DB::table('users')->insertGetId([
                'name' => $emp['name'],
                'email' => $emp['email'],
                'password' => Hash::make('password'), // default password
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert into employees
            DB::table('employees')->insert([
                'employee_id' => $userId, // sama dengan user_id
                'name' => $emp['name'],
                'email' => $emp['email'],
                'phone_number' => $emp['phone'],
                'address' => $emp['address'],
                'position' => $emp['position'],
                'department_id' => $index + 1, // 1 - 10
                'join_date' => now()->subDays(100 + $index),
                'status' => 'active',
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
