<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignments = [];

        // Ambil 10 employee_id pertama
        $employeeIds = DB::table('employees')->orderBy('employee_id')->limit(10)->pluck('employee_id');

        $shiftIds = [1, 2]; // Sesuai dengan ShiftSeeder

        $startDate = Carbon::today()->subDays(5);

        foreach ($employeeIds as $index => $employeeId) {
            $assignments[] = [
                'employee_id' => $employeeId,
                'shift_id' => $shiftIds[$index % 2], // bergantian Morning/Night
                'assigned_date' => $startDate->copy()->addDays($index)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('employee_shifts')->insert($assignments);
    }
}
