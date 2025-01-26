<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()->all()], 422);
        }
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);
        return response()->json([
            'message' => 'Admin created successfully',
            'admin' => $admin,
        ], 201);
    }
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
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

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',

            ], 401);
        }

        $token = $admin->createToken('AdminToken')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'user' => $admin,
            'token' => $token,
        ]);
    }

    public function studentRegister(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:students',
                'password' => 'required|min:8',
                'student_id' => 'required|unique:students',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Student registration failed',
                'errors' => $validator->errors()->all(),
            ], 400);
        }
        $student = Student::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'student_id' => $request->student_id,

        ]);

        return response()->json([
            'status' => true,
            'message' => 'Student registered successfully',
            'student' => $student,
        ], 201); // 201 Created

    }

    public function teacherRegister(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:teachers',
                'password' => 'required|min:8',

            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Teacher registration failed',
                'errors' => $validator->errors()->all(),
            ], 400);
        }
        $teacher = Teacher::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Teacher registered successfully',
            'teacher' => $teacher,
        ], 201); // 201 Created

    }
}
