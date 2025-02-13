<?php

namespace App\Http\Controllers\Api;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ExamType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        // Fetch exam types with exam count in one query
        $examCounts = ExamType::leftJoin('exams', 'exam_types.id', '=', 'exams.exam_type_id')
            ->select('exam_types.id', 'exam_types.name', DB::raw('COUNT(exams.id) as exam_count'))
            ->groupBy('exam_types.id', 'exam_types.name')
            ->orderBy('exam_types.name', 'asc')
            ->get();

        // Check if any exam types exist
        if ($examCounts->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No exam types found'
            ], 404);
        }

        // Return the response
        return response()->json([
            'status' => true,
            'data' => $examCounts
        ], 200);
    }
}
