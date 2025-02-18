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
        $student->makeHidden('password');

        $token = $student->createToken('StudentToken')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'student' => $student,
            'token' => $token,
        ]);
    }
    public function getLoginInfos($id)
    {
        $student = Student::with([
            'class', // Relationship to get the class details
            'section', // Relationship to get the section details
            'section.teacher', // Relationship to get the teacher assigned to the section
            'profile' // Relationship to get the student profile
        ])->find($id);

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found',
            ], 404);
        }

        // Extract the required information
        $className = $student->class->name ?? 'N/A';
        $sectionName = $student->section->name ?? 'N/A';
        $teacherName = $student->section->teacher->name ?? 'N/A';
        $studentProfile = $student->profile ?? null;

        return response()->json([
            'status' => true,
            'data' => [
                'class_name' => $className,
                'section_name' => $sectionName,
                'teacher_name' => $teacherName,
                'student_profile' => $studentProfile,
            ],
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
            'status' => true,
        ], 200);
    }

    public function search(Request $request)
    {
        // Validate the request
        $request->validate([
            'search' => 'nullable|string',
        ]);

        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('student_id', 'like', '%' . $search . '%')
                    ->orWhere('class_id', 'like', '%' . $search . '%')
                    ->orWhere('sec_id', 'like', '%' . $search . '%');
            });
        }

        $students = $query->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Students retrieved successfully',
            'data' => $students,
        ], 200);
    }
}
