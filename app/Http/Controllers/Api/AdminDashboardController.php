<?php

namespace App\Http\Controllers\Api;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ExamType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    //
    public function getStudentCount()
    {
        $studentCount = Student::count();
        if ($studentCount) {
            return response()->json(['studentCount' => $studentCount], 200);
        }
        return response()->json(['error' => 'No students found'], 404);
    }
    public function getTeacherCount()
    {
        $teacherCount = Teacher::count();
        if ($teacherCount) {
            return response()->json(['teacherCount' => $teacherCount], 200);
        }
        return response()->json(['error' => 'No teachers found'], 404);
    }
    public function getExamTypeCount()
    {
        $examTypeCount = ExamType::count();
        if ($examTypeCount) {
            return response()->json(['examTypeCount' => $examTypeCount], 200);
        }
        return response()->json(['error' => 'No exam types found'], 404);
    }

    public function getExamCountByType()
    {
        // Fetch all exam types ordered by name
        $examTypes = ExamType::orderBy('name', 'asc')->get();

        // Check if exam types exist
        if ($examTypes->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No exam types found',
            ], 404); // 404 Not Found
        }

        // Initialize an array to store the result
        $result = [];

        // Loop through each exam type
        foreach ($examTypes as $examType) {
            // Get the count of exams for the current exam type
            $examCount = Exam::where('exam_type_id', $examType->id)->count();

            // Add the exam type details and count to the result array
            $result[] = [
                'id' => $examType->id,
                'name' => $examType->name,
                'exam_count' => $examCount,
            ];
        }

        // Return the result
        return response()->json([
            'status' => true,
            'data' => $result,
        ], 200); // 200 OK
    }
}
