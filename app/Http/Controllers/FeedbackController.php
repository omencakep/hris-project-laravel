<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function submitFeedback(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employeeId = $user->id;

        if (!$employeeId) {
            return response()->json(['message' => 'Employee not found for this user.'], 404);
        }

        $feedback = Feedback::create([
            'employee_id' => $employeeId,
            'content' => $request->content,
            'submitted_at' => now(),
            'sentiment' => null, // default null, bisa ditambahkan analisis nanti
        ]);

        return response()->json([
            'message' => 'Feedback submitted successfully.',
            'feedback' => $feedback,
        ], 201);
    }
}
