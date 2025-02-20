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
    public function getLoginInfos($id)
    {
        $teacher = Teacher::with(['profile', 'section.class'])->find($id);

        if (!$teacher) {
            return response()->json([
                'status' => false,
                'message' => 'Teacher not found',
            ], 404);
        }

        $classes = [];
        $hasDesignatedSections = false;

        // Process sections only if they exist
        if ($teacher->section->isNotEmpty()) {
            $hasDesignatedSections = true;

            foreach ($teacher->section as $section) {
                // Skip sections without a valid class relationship
                if (!$section->class) continue;

                $classId = $section->class->id;
                $className = $section->class->name;


                if (!isset($classes[$classId])) {
                    $classes[$classId] = [
                        'class_name' => $className,
                        'section' => $section->name,
                        'sectionId' => $section->id
                    ];
                }
            }
        }

        return response()->json([
            'status' => true,
            'data' => [
                'has_designated_sections' => $hasDesignatedSections,
                'classes' => $classes ?: null,
                'teacher_profile' => $teacher->profile ?? null
            ]
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
}
