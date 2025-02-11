<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Teacher;

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
}
