<?php

namespace App\Http\Controllers\Api;

use App\Models\Exam;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    //
    public function index()
    {
        $exams = Exam::orderBy('exam_date', 'desc')->paginate(5);

        if ($exams->total() > 0) {
            return response()->json([
                'status' => true,
                'exams' => $exams,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'No exams found',
        ], 404);
    }
    public function indexWithoutPagination()
    {
        $exams = Exam::orderBy('exam_date', 'desc')->get();
        if ($exams->count() > 0) {
            return response()->json([
                'status' => true,
                'exams' => $exams,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'No exams found',
        ], 404);
    }

    public function getStudentsByExam($exam_id)
    {
        // ✅ Retrieve the exam
        $exam = Exam::find($exam_id);

        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'Exam not found',
            ], 404);
        }

        // ✅ Fetch students with the same class_id as the exam
        $students = Student::where('class_id', $exam->class_id)->get();
        if ($students->count() > 0) {
            return response()->json([
                'status' => true,
                'exam_id' => $exam_id,
                'class_id' => $exam->class_id,
                'students' => $students,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No students found',
        ], 404);
    }

    public function store(Request $request)
    {
        $examValidator = Validator::make(
            $request->all(),
            [
                'exam_type_id' => 'required|exists:exam_types,id',
                'subject' => 'required|string|max:50',
                'class_id' => 'required|exists:classes,id', // Fixed space issue
                'exam_date' => 'required|date_format:Y-m-d',
                'full_marks' => 'required|numeric|between:5,100',
            ]
        );

        if ($examValidator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $examValidator->errors(),
            ], 422);
        }

        try {
            $exam = Exam::create([
                'exam_type_id' => $request->input('exam_type_id'),
                'subject' => $request->input('subject'),
                'class_id' => $request->input('class_id'),
                'exam_date' => $request->input('exam_date'),
                'full_marks' => $request->input('full_marks')
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Exam created successfully',
                'exam' => $exam,
            ], 201);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create new exam',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $exam = Exam::find($id);
        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'Exam not found',
            ], 404);
        }

        $examValidator = Validator::make(
            $request->all(),
            [
                'exam_type_id' => 'required|exists:exam_types,id',
                'subject' => 'required|string|max:50',
                'class_id' => 'required|exists:classes,id',
                'exam_date' => 'required|date_format:Y-m-d',
                'full_marks' => 'required|numeric'
            ]
        );

        if ($examValidator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $examValidator->errors(),
            ], 422);
        }

        try {
            $exam->update([
                'exam_type_id' => $request->input('exam_type_id'),
                'subject' => $request->input('subject'),
                'class_id' => $request->input('class_id'),
                'exam_date' => $request->input('exam_date'),
                'full_marks' => $request->input('full_marks')
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Exam updated successfully',
                'exam' => $exam,
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update exam',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function destroy($id)
    {
        $exam = Exam::find($id);
        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'Exam not found',
            ], 404);
        }

        try {
            $exam->delete();
            return response()->json([
                'status' => true,
                'message' => 'Exam deleted successfully',
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete exam',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
