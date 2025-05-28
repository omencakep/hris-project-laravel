<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request; // Import Request
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash; // Untuk hashing password
use Illuminate\Support\Facades\DB; // Untuk transaksi database
use Carbon\Carbon; // Untuk tanggal join_date

class AuthController extends Controller
{

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'string|max:20',
            'address' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            // 'department_id' => 'required|exists:departments,department_id', // Asumsi department_id wajib dan harus ada
            // 'join_date' => 'nullable|date', // Jika tidak diisi manual, kita bisa pakai now()
            // 'status' => 'nullable|string', // Default 'active'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Gunakan Transaksi Database untuk memastikan atomicity
        // Jika salah satu gagal, keduanya tidak akan tersimpan
        try {
            DB::beginTransaction();

            // Buat Record User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Gunakan Hash::make
            ]);

            $employee = Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'user_id' => $user->id,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'position' => $request->position,
                'department_id' => $request->department_id,
                'join_date' => Carbon::now()->toDateString(), // Tanggal gabung otomatis hari ini
                'status' => 'active', // Status default
            ]);

            DB::commit();

            // Mengembalikan user dan employee yang baru dibuat
            return response()->json([
                'user' => $user,
                'employee' => $employee
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika ada error

            return response()->json(['error' => 'Registration failed. Please try again later.'], 500);
        }
    }


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}
