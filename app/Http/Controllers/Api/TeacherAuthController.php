<?php

namespace App\Http\Controllers\Api;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherAuthController extends Controller
{
    //

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required',
                'password' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
                'errors' => $validator->errors()->all(),
            ], 401); // Use 401 Unauthorized status code
        }

        $teacher = Teacher::where('email', $request->email)->first();

        if (!$teacher || !Hash::check($request->password, $teacher->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',

            ], 401);
        }

        $token = $teacher->createToken('TeacherToken')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'teacher' => $teacher,
            'token' => $token,
        ]);
    }
    public function logout(Request $request)
    {
        $teacher = $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
            'user' => $teacher,
            'status' => true,
        ], 200);
    }
}
