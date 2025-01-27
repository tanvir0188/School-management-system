<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
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

    public function students()
    {
        // Only retrieve 'name' and 'email' fields for all students
        $students['students'] = Student::select('name', 'email')
            ->orderBy('name', 'asc')
            ->paginate(10);

        if ($students['students']->count() > 0) {
            return response()->json([
                'status' => true,
                'students' => $students,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'No student users found',
        ], 404);
    }

    public function showStudent($id)
    {
        // Only retrieve 'name' and 'email' fields for a specific student
        $student = Student::select('name', 'email')->find($id);

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found',
            ], 404); // 404 Not Found
        }

        return response()->json([
            'status' => true,
            'student' => $student,
        ], 200);
    }
}
