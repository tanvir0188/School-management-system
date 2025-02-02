<?php

namespace App\Http\Controllers\Api;

use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ExamResultController extends Controller
{
    //
    public function index()
    {
        $examResults = ExamResult::with(['student', 'exam'])
            ->select('id', 'exam_id', 'student_id', 'marks')
            ->paginate(10);

        // Check if there are any exam results
        if ($examResults->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No exam results found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'examResults' => $examResults->map(function ($examResult) {
                return [
                    'id' => $examResult->id,
                    'exam_id' => $examResult->exam_id,
                    'exam_name' => $examResult->exam->subject ?? 'N/A',
                    'student_id' => $examResult->student->student_id ?? 'N/A',  // Get student ID from the 'students' table
                    'student_name' => $examResult->student->name ?? 'N/A',
                    'exam_date' => $examResult->exam->exam_date ?? 'N/A',
                    'marks' => $examResult->marks,
                ];
            }),
        ], 200);
    }


    public function store(Request $request)
    {
        $examResultValidator = Validator::make(
            $request->all(),
            [
                'exam_id' => 'required|exists:exams,id',
                'student_id' => 'required|exists:students,id',
                'marks' => 'required|numeric|min:0',
                'exam_id' => [
                    'required',
                    'exists:exams,id',
                    Rule::unique('exams_results')->where(function ($query) use ($request) {
                        return $query->where('student_id', $request->student_id);
                    })
                ],
            ]
        );

        if ($examResultValidator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $examResultValidator->errors(),
            ], 422);
        }


        // Retrieve the full_marks for the given exam
        $exam = Exam::find($request->exam_id);

        if ($request->marks > $exam->full_marks) {
            return response()->json([
                'status' => false,
                'message' => "Marks cannot be greater than the full marks ({$exam->full_marks}) for this exam.",
            ], 422);
        }

        try {
            $examResult = ExamResult::create([
                'exam_id' => $request->input('exam_id'),
                'student_id' => $request->input('student_id'),
                'marks' => $request->input('marks'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Exam result stored successfully',
                'exam_result' => $examResult,
            ], 201);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to store exam result',
                'error' => $th->getMessage(),
            ], 500);
        }
    }



    public function update(Request $request, $id)
    {
        $examResult = ExamResult::find($id);
        if (!$examResult) {
            return response()->json([
                'status' => false,
                'message' => 'Exam result not found',
            ], 404);
        }

        $examResultValidator = Validator::make(
            $request->all(),
            [
                'exam_id' => 'required|exists:exams,id',
                'student_id' => [
                    'required',
                    'exists:students,id',
                    // Ensure the student is not trying to update the result for the same exam they already have a result for
                    Rule::unique('exams_results')->where(function ($query) use ($request, $examResult) {
                        return $query->where('exam_id', $request->exam_id)
                            ->where('student_id', $request->student_id)
                            ->where('id', '<>', $examResult->id); // Exclude current record from the uniqueness check
                    })
                ],
                'marks' => 'required|numeric|min:0',
            ]
        );

        if ($examResultValidator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $examResultValidator->errors(),
            ], 422);
        }

        // Retrieve the full_marks for the given exam
        $exam = Exam::find($request->exam_id);

        if ($request->marks > $exam->full_marks) {
            return response()->json([
                'status' => false,
                'message' => "Marks cannot be greater than the full marks ({$exam->full_marks}) for this exam.",
            ], 422);
        }

        try {
            $examResult->update([
                'exam_id' => $request->input('exam_id'),
                'student_id' => $request->input('student_id'),
                'marks' => $request->input('marks'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Exam result updated successfully',
                'exam_result' => $examResult,
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update exam result',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        $examResult = ExamResult::find($id);
        if (!$examResult) {
            return response()->json([
                'status' => false,
                'message' => 'Exam result not found',
            ], 404);
        }

        try {
            $examResult->delete();

            return response()->json([
                'status' => true,
                'message' => 'Exam result deleted successfully',
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete exam result',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function show($id)
    {
        $examResult = ExamResult::with(['exam', 'student'])->find($id);

        if (!$examResult) {
            return response()->json([
                'status' => false,
                'message' => 'Exam result not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'student_id' => $examResult->student->student_id,
            'exam_subject' => $examResult->exam->subject,
            'exam_type' => $examResult->exam->exam_type,
            'exam_date' => $examResult->exam->exam_date,
        ], 200);
    }
}
