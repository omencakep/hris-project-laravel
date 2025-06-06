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

        $content = $request->input('content');
        $feedback = Feedback::create([
            'employee_id' => $employeeId,
            'content' => $content,
            'submitted_at' => now(),
            'sentiment' => $this->analyze($content), // default null, bisa ditambahkan analisis nanti
        ]);

        return response()->json([
            'message' => 'Feedback submitted successfully.',
            'feedback' => $feedback,
        ], 201);
    }

    public function analyze(string $text): ?string
    {
        $url = 'http://103.127.96.228:5000/analyze'; // Endpoint REST API

        $headers = [
            'Content-Type: application/json',
            'api-key: yyWq@TAN2j#@j@SCKDjTVqdZxW6N60zJJ4^v2#Y6d7b6C3y!f#0Xx5YBV!NT&mZt'
        ];

        $payload = json_encode([
            'text' => $text
        ]);

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch) || $httpCode !== 200) {
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        $data = json_decode($response, true);

        return $data['sentiment'] ?? null;
    }
}
