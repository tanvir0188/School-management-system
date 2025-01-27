<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentAuthController extends Controller
{
    //

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
                'errors' => $validator->errors()->all(),
            ], 401);
        }

        $student = Student::where('student_id', $request->student_id)->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $student->createToken('StudentToken')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'student' => $student,
            'token' => $token,
        ]);
    }
    public function logout(Request $request)
    {
        $student = $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
            'student' => $student,
            'status' => true,
        ], 200);
    }
}
