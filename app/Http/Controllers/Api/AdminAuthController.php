<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            // 'confirm_password' => 'required|same:password',
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
                'errors' => $validator->errors()->all(),
            ], 400);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }


        // If no token exists, create a new one
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
                'class_id' => 'required|integer|exists:classes,id',
                'sec_id' => [
                    'required',
                    'integer',
                    'exists:sections,id',
                    function ($attribute, $value, $fail) use ($request) {
                        $section = Section::where('id', $value)
                            ->where('class_id', $request->class_id)
                            ->exists();
                        if (!$section) {
                            $fail('The selected section does not belong to the specified class.');
                        }
                    },
                ],

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
            'class_id' => $request->class_id,
            'sec_id' => $request->sec_id,
            'student_id' => $request->student_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Student registered successfully',
            'student' => $student,
        ], 201);
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

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
            'status' => true,
        ], 200);
    }
    public function teachersWithoutPagination()
    {
        $teachers['teachers'] = Teacher::orderBy('name', 'asc')->get();
        if ($teachers['teachers']->count() > 0) {
            return response()->json([
                'status' => true,
                'teachers' => $teachers,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'No teacher users found',
        ]);
    }
    public function teachers()
    {
        $teachers['teachers'] = Teacher::orderBy('name', 'asc')->paginate(10);
        if ($teachers['teachers']->count() > 0) {
            return response()->json([
                'status' => true,
                'teachers' => $teachers,
                'teacherCount' => $teachers['teachers']->total(),
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'No teacher users found',
        ]);
    }
    public function showTeacher($id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher) {
            return response()->json([
                'status' => false,
                'message' => 'Teacher not found',

            ], 404); // 404 Not Found
        }
        return response()->json([
            'status' => true,
            'teacher' => $teacher,

        ], 200);
    }
    public function students()
    {
        $students['students'] = Student::orderBy('name', 'asc')->paginate(10);
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
        $student = Student::find($id);
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
    public function deleteStudent($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found',
            ], 404); // 404 Not Found
        }

        // Delete the student's profile photo if it exists
        if ($student->profile && $student->profile->photo) {
            $photoPath = public_path('students/' . $student->profile->photo); // here profile is the method in Student model
            if (file_exists($photoPath)) {
                unlink($photoPath); // Delete the photo from the public path
            }
        }

        // Delete the student's profile and the student record
        $student->profile()->delete();
        $student->delete();

        return response()->json([
            'status' => true,
            'message' => 'Student and associated profile deleted successfully',
        ], 200);
    }

    public function deleteTeacher($id)
    {
        $teacher = Teacher::find($id);
        if (!$teacher) {
            return response()->json([
                'status' => false,
                'message' => 'Teacher not found',
            ], 404); // 404 Not Found
        }
        $teacher->delete();
        return response()->json([
            'status' => true,
            'message' => 'Teacher deleted successfully',
        ], 200);
    }
}
