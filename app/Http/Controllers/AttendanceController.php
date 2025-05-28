<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'location_latitude' => 'nullable|numeric',
            'location_longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employeeId = $user->id;

        // Check if already checked in today
        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', now()->toDateString())
            ->first();

        if ($attendance) {
            return response()->json([
                'message' => 'Already checked in today.',
                'attendance' => $attendance,
            ], 400);
        }

        $attendance = Attendance::create([
            'employee_id' => $employeeId,
            'date' => now()->toDateString(),
            'check_in_time' => now()->toTimeString(),
            'location_latitude' => $request->location_latitude,
            'location_longitude' => $request->location_longitude,
            'status' => 'present',
        ]);

        return response()->json([
            'message' => 'Check-in successful',
            'attendance' => $attendance,
        ], 201);
    }

    public function checkOut(Request $request)
    {

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'location_latitude' => 'nullable|numeric',
            'location_longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employeeId = $user->id;

        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', now()->toDateString())
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'You have not checked in today.',
            ], 400);
        }

        if ($attendance->check_out_time) {
            return response()->json([
                'message' => 'Already checked out today.',
                'attendance' => $attendance,
            ], 400);
        }

        $attendance->update([
            'check_out_time' => now()->toTimeString(),
            'location_latitude' => $request->location_latitude ?? $attendance->location_latitude,
            'location_longitude' => $request->location_longitude ?? $attendance->location_longitude,
        ]);

        return response()->json([
            'message' => 'Check-out successful',
            'attendance' => $attendance,
        ]);
    }
}
