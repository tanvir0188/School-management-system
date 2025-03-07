<?php

namespace App\Http\Controllers\Api;

use App\Models\Exam;
use App\Models\Student;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    public function show($id)
    {
        $exam = Exam::find($id);
        if ($exam) {
            return response()->json([
                'status' => true,
                'exam' => $exam,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'Exam not found',
        ], 404);
    }
    public function getExams(Request $request)
    {
        // Validate the request
        $request->validate([
            'search' => 'nullable|string',
        ]);

        // Start the query
        $query = DB::table('exams')
            ->join('exam_types', 'exams.exam_type_id', '=', 'exam_types.id')
            ->join('classes', 'exams.class_id', '=', 'classes.id')
            ->select(
                'exam_types.name as exam_type_name',
                'classes.name as class_name',
                'exams.id as exam_id',
                'exams.subject',
                'exams.full_marks',
                'exams.exam_date'
            )
            ->orderBy('exam_date', 'desc');

        // Add search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->orWhere('exam_types.name', 'like', '%' . $search . '%')
                    ->orWhere('classes.name', 'like', '%' . $search . '%')
                    ->orWhere('exams.subject', 'like', '%' . $search . '%')
                    ->orWhere('exams.full_marks', 'like', '%' . $search . '%')
                    ->orWhere('exams.exam_date', 'like', '%' . $search . '%');
            });
        }

        // Paginate the results
        $exams = $query->paginate(10);

        // Return the response
        if ($exams->total() > 0) {
            return response()->json([
                'status' => true,
                'exams' => $exams,
                'examCount' => $exams->total(),
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

    public function examByTypeWithResult($id)
    {
        $examResults = DB::table('exams')
            ->join('classes', 'exams.class_id', '=', 'classes.id')
            ->join('students', 'students.class_id', '=', 'classes.id')
            ->leftJoin('exams_results', function ($join) {
                $join->on('exams_results.exam_id', '=', 'exams.id')
                    ->on('exams_results.student_id', '=', 'students.id');
            })
            ->where('exams.exam_type_id', $id)
            ->select(
                'exams.exam_date',
                'classes.name as class_name',
                'students.name as student_name',
                'exams.subject',
                'students.student_id',
                'exams.full_marks',
                DB::raw("COALESCE(exams_results.marks, 'Pending') as mark") // If no marks, show 'Pending'
            )
            ->paginate(5);

        if ($examResults->total() > 0) {
            return response()->json([
                'status' => true,
                'message' => 'Exam results fetched successfully',
                'examResultsCount' => $examResults->total(),
                'data' => $examResults,

            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No exam results found',
        ], 404);
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
