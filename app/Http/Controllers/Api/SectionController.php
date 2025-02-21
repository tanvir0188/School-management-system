<?php

namespace App\Http\Controllers\Api;

use App\Models\Section;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    //
    public function index()
    {
        $sections['sections'] = Section::orderBy('name', 'asc')->get();
        if ($sections['sections']->count() > 0) {
            return response()->json([
                'status' => true,
                'sections' => $sections
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No sections found'
        ], 404);
    }
    public function sectionByClass($id)
    {
        $sections = Section::where('class_id', $id)->orderBy('name', 'asc')->get();
        if ($sections->count() > 0) {
            return response()->json([
                'status' => true,
                'sections' => $sections
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No sections found'
        ], 404);
    }
    public function studentsBySection($id)
    {
        $students = Student::with('profile') // Load student profile data
            ->where('sec_id', $id)
            ->orderBy('name', 'asc')
            ->get();

        if ($students->isNotEmpty()) {
            return response()->json([
                'status' => true,
                'students' => $students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                        'full_name' => $student->profile->full_name ?? null,
                        'photo' => $student->profile->photo ?? null,
                        'phone_number' => $student->profile->phone_number ?? null,
                        'address' => $student->profile->address ?? null,
                    ];
                }),
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'No students found for this section'
        ], 404);
    }


    public function store(Request $request)
    {
        $validateSection = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:1',
                'class_id' => 'required|integer|exists:classes,id',
                'teacher_id' => 'nullable|integer|exists:teachers,id',
            ]
        );
        if ($validateSection->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validateSection->errors()->all(),
            ], 422);
        }
        if (Section::where('name', $request->name)->where('class_id', $request->class_id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'This section already exists in the selected class.'
            ], 422);
        }
        if (Section::where('class_id', $request->class_id)->where('teacher_id', $request->teacher_id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'This teacher is already assigned to a section in this class.'
            ], 422);
        }
        try {
            Section::create([
                'name' => $request->name,
                'class_id' => $request->class_id,
                'teacher_id' => $request->teacher_id,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Section created successfully',
            ], 201);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create section'
            ], 500);
        }
    }
    public function getSectionCount()
    {
        $count = Section::count();
        if ($count) {
            return response()->json([
                'status' => true,
                'sectionCount' => $count,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No sections found'
        ], 404);
    }
    public function show($id)
    {
        $section = Section::find($id);
        if ($section) {
            return response()->json([
                'status' => true,
                'section' => $section,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'Section not found'
        ], 404);
    }
    public function update(Request $request, $id)
    {
        // Validate input data
        $validateSection = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:1',
                'class_id' => 'required|integer|exists:classes,id',
                'teacher_id' => 'nullable|integer|exists:teachers,id',
            ]
        );

        if ($validateSection->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validateSection->errors()->all(),
            ], 422);
        }

        // Find the section
        $section = Section::find($id);
        if (!$section) {
            return response()->json([
                'status' => false,
                'message' => 'Section not found',
            ], 404);
        }

        // Check if section name already exists for the same class (excluding current section)
        if (Section::where('name', $request->name)
            ->where('class_id', $request->class_id)
            ->where('id', '!=', $id)  // Exclude current section
            ->exists()
        ) {
            return response()->json([
                'status' => false,
                'message' => 'This section name already exists in the selected class.',
            ], 422);
        }

        // Check if the teacher is already assigned to another section in the same class (excluding current section)
        if (Section::where('class_id', $request->class_id)
            ->where('teacher_id', $request->teacher_id)
            ->where('id', '!=', $id)  // Exclude current section
            ->exists()
        ) {
            return response()->json([
                'status' => false,
                'message' => 'This teacher is already assigned to a section in this class.',
            ], 422);
        }


        try {
            // Update section details
            $section->update([
                'name' => $request->name,
                'class_id' => $request->class_id,
                'teacher_id' => $request->teacher_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Section updated successfully',
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update section',
            ], 500);
        }
    }
    public function destroy($id)
    {
        $section = Section::find($id);
        if ($section) {
            try {
                $section->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Section deleted successfully',
                ], 200);
            } catch (\Exception $th) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete section',
                ], 500); // Internal Server Error
            }
        }
        return response()->json([
            'status' => false,
            'message' => 'Section not found',
        ], 404);
    }

    public function getAllSectionsAndClassesWithTeachers()
    {
        $classes = ClassModel::with('sections.teacher')->get(); // Load sections with their teachers

        $result = [];

        foreach ($classes as $class) {
            $result[$class->id] = [
                'class_name' => $class->name,
                'sections' => []
            ];

            foreach ($class->sections as $section) {
                $result[$class->id]['sections'][] = [
                    'section_name' => $section->name,
                    'section_id' => $section->id,
                    'teacher_id' => $section->teacher->id ?? null // Get teacher ID if available
                ];
            }
        }

        return response()->json([
            'status' => true,
            'data' => [
                'classes' => $result ?: null
            ]
        ]);
    }
}
