<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{

    private function saveBase64Image($base64String, $prefix = 'photo')
    {
        // Cek dan ambil jenis file dari base64
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $typeMatch)) {
            $extension = strtolower($typeMatch[1]); // contoh: jpeg, png, webp

            $base64Data = substr($base64String, strpos($base64String, ',') + 1);
            $image = base64_decode($base64Data);

            $fileName = $prefix . '_' . time() . '_' . uniqid() . '.' . $extension;
            $filePath = public_path('uploads/attendances/' . $fileName);

            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            file_put_contents($filePath, $image);

            return 'uploads/attendances/' . $fileName;
        }

        return null;
    }


    public function checkIn(Request $request)
    {

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'location_latitude' => 'nullable|numeric',
            'location_longitude' => 'nullable|numeric',
            'photo' => 'required|string', // base64 photo
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employeeId = $user->id;
        $photoPath = $this->saveBase64Image($request->photo, 'checkin');

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
            'check_in_photo' => $photoPath,
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
            'photo' => 'required|string', // base64 photo
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employeeId = $user->id;
        $photoPath = $this->saveBase64Image($request->photo, 'checkout');

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
            'check_out_photo' => $photoPath,
            'location_latitude' => $request->location_latitude ?? $attendance->location_latitude,
            'location_longitude' => $request->location_longitude ?? $attendance->location_longitude,
        ]);

        return response()->json([
            'message' => 'Check-out successful',
            'attendance' => $attendance,
        ]);
    }

    // Get absen 3 hari terakhir by employee_id
    public function recentAttendances()
    {
        $user = Auth::user();

        $attendances = Attendance::where('employee_id', $user->id)
            ->whereDate('date', '>=', now()->subDays(2)->toDateString()) // Hari ini, kemarin, dua hari lalu
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'message' => 'Last 3 days attendance data retrieved.',
            'attendances' => $attendances,
        ]);
    }

    // Get absen hari ini by employee_id
    public function todayAttendance()
    {
        $user = Auth::user();

        $attendance = Attendance::where('employee_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'No attendance record found for today.',
            ], 404);
        }

        return response()->json([
            'message' => 'Today\'s attendance retrieved.',
            'attendance' => $attendance,
        ]);
    }
}
