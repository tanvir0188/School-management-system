<?php

namespace App\Http\Controllers\Api;

use App\Models\Exam;
use App\Models\Student;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
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
    public function getExamResults(Request $request)
    {
        // Validate the request
        $request->validate([
            'search' => 'nullable|string',
        ]);

        // Start the query
        $query = DB::table('exams')
            ->join('exam_types', 'exams.exam_type_id', '=', 'exam_types.id')
            ->join('classes', 'exams.class_id', '=', 'classes.id')
            ->leftJoin('sections', 'classes.id', '=', 'sections.class_id')
            ->leftJoin('students', function ($join) {
                $join->on('students.class_id', '=', 'classes.id')
                    ->on('students.sec_id', '=', 'sections.id');
            })
            ->leftJoin('exams_results', function ($join) {
                $join->on('exams_results.exam_id', '=', 'exams.id')
                    ->on('exams_results.student_id', '=', 'students.id');
            })
            ->select(
                'exam_types.name as exam_type_name',
                'exams.subject',
                'classes.name as class_name',
                'sections.name as section_name',
                'students.name as student_name',
                'students.student_id as student_id',
                'exams.full_marks',
                'exams.id as exam_id',
                'students.id as s_id',
                'exams_results.id as result_id',
                DB::raw('IFNULL(exams_results.marks, "Pending") as marks'),
                'exams.exam_date'
            )
            ->whereNotNull('sections.name')
            ->whereNotNull('students.id')
            ->orderBy('exams.exam_date', 'desc');

        // Add search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->orWhere('exam_types.name', 'like', '%' . $search . '%')
                    ->orWhere('exams.subject', 'like', '%' . $search . '%')
                    ->orWhere('classes.name', 'like', '%' . $search . '%')
                    ->orWhere('sections.name', 'like', '%' . $search . '%')
                    ->orWhere('students.name', 'like', '%' . $search . '%')
                    ->orWhere('students.student_id', 'like', '%' . $search . '%')
                    ->orWhere('exams.full_marks', 'like', '%' . $search . '%')
                    ->orWhere('exams.exam_date', 'like', '%' . $search . '%')
                    ->orWhere('exams_results.marks', 'like', '%' . $search . '%');
            });
        }

        // Paginate the results
        $results = $query->paginate(10);

        // Return the response
        if ($results->total() > 0) {
            return response()->json([
                'status' => true,
                'results' => $results,
                'resultCount' => $results->total(),
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'No results found',
        ], 404);
    }
    public function resultByStudent($id)
    {
        // Build a derived table that computes the highest marks per exam
        $highestMarksQuery = DB::table('exams_results')
            ->select('exam_id', DB::raw('IFNULL(MAX(marks), "Pending") as highest'))
            ->groupBy('exam_id');

        // Main query to get the student's exam results, joining the derived table for highest marks
        $results = DB::table('exams')
            ->join('exam_types', 'exams.exam_type_id', '=', 'exam_types.id')
            ->join('classes', 'exams.class_id', '=', 'classes.id')
            ->leftJoin('sections', 'classes.id', '=', 'sections.class_id')
            ->leftJoin('students', function ($join) {
                $join->on('students.class_id', '=', 'classes.id')
                    ->on('students.sec_id', '=', 'sections.id');
            })
            ->leftJoin('exams_results', function ($join) {
                $join->on('exams_results.exam_id', '=', 'exams.id')
                    ->on('exams_results.student_id', '=', 'students.id');
            })
            ->leftJoinSub($highestMarksQuery, 'highest_marks', function ($join) {
                $join->on('exams.id', '=', 'highest_marks.exam_id');
            })
            ->select(
                'exam_types.name as exam_type_name',
                'exams.subject',
                'exams.full_marks',
                'exams_results.id as result_id',
                DB::raw('IFNULL(exams_results.marks, "Pending") as marks'),
                'exams.exam_date',
                'highest_marks.highest',
                'exams.id as exam_id'
            )
            ->where('students.id', $id) // Filter by student ID
            ->whereNotNull('sections.name')
            ->whereNotNull('students.student_id')
            ->orderBy('exams.exam_date', 'desc')
            ->get();

        if ($results->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No results found for the student',
            ], 404);
        }

        // Format the results grouped by exam type name
        $formattedResults = [];
        foreach ($results as $result) {
            $formattedResults[$result->exam_type_name][] = [
                'subject'    => $result->subject,
                'full_marks' => $result->full_marks,
                'result_id'  => $result->result_id,
                'marks'      => $result->marks,
                'highest'    => $result->highest,
                'exam_date'  => $result->exam_date,
            ];
        }

        return response()->json([
            'status'  => true,
            'results' => $formattedResults,
        ], 200);
    }





    // public function getExamResultsBySubject(Request $request, $subject)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'search' => 'nullable|string',
    //     ]);

    //     // Start the query
    //     $query = DB::table('exams')
    //         ->join('classes', 'exams.class_id', '=', 'classes.id')
    //         ->leftJoin('sections', 'classes.id', '=', 'sections.class_id')
    //         ->leftJoin('students', function ($join) {
    //             $join->on('students.class_id', '=', 'classes.id')
    //                 ->on('students.sec_id', '=', 'sections.id');
    //         })
    //         ->leftJoin('exams_results', function ($join) {
    //             $join->on('exams_results.exam_id', '=', 'exams.id')
    //                 ->on('exams_results.student_id', '=', 'students.id');
    //         })
    //         ->select(
    //             'classes.name as class_name',
    //             'sections.name as section_name',
    //             'students.name as student_name',
    //             'students.student_id as student_id',
    //             'exams.full_marks',
    //             'exams_results.id as result_id',
    //             DB::raw('IFNULL(exams_results.marks, "Pending") as marks'),
    //             'exams.exam_date'
    //         )
    //         ->where('exams.subject', $subject) // Filter by subject
    //         ->whereNotNull('sections.name')
    //         ->whereNotNull('students.student_id')
    //         ->orderBy('exams.exam_date', 'desc');

    //     // Add search functionality
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->orWhere('classes.name', 'like', '%' . $search . '%')
    //                 ->orWhere('sections.name', 'like', '%' . $search . '%')
    //                 ->orWhere('students.name', 'like', '%' . $search . '%')
    //                 ->orWhere('students.student_id', 'like', '%' . $search . '%')
    //                 ->orWhere('exams.full_marks', 'like', '%' . $search . '%')
    //                 ->orWhere('exams.exam_date', 'like', '%' . $search . '%')
    //                 ->orWhere('exams_results.marks', 'like', '%' . $search . '%');
    //         });
    //     }

    //     // Paginate the results
    //     $results = $query->paginate(10);

    //     // Return the response
    //     if ($results->total() > 0) {
    //         return response()->json([
    //             'status' => true,
    //             'results' => $results,
    //             'resultCount' => $results->total(),
    //         ], 200);
    //     }

    //     return response()->json([
    //         'status' => false,
    //         'message' => 'No results found for the subject: ' . $subject,
    //     ], 404);
    // }

    public function patchMark(Request $request, $exam_id, $student_id)
    {
        $validator = Validator::make($request->all(), [
            'marks' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ], 422);
        }
        $exam = Exam::find($exam_id);
        $student = Student::find($student_id);
        $examResult = ExamResult::where('exam_id', $exam_id)->where('student_id', $student_id)->first();

        if (!$exam || !$student) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid exam or student',
            ], 422);
        }

        if (!$examResult) {
            return response()->json([
                'status' => false,
                'message' => 'Exam result not found',
            ], 404);
        }

        if ($exam->class_id !== $student->class_id) {
            return response()->json([
                'status' => false,
                'message' => 'This student is not enrolled in the class for this exam.',
            ], 422);
        }

        if ($request->marks > $exam->full_marks) {
            return response()->json([
                'status' => false,
                'message' => "Marks cannot be greater than the full marks ({$exam->full_marks}) for this exam.",
            ], 422);
        }

        try {
            $examResult->update([
                'marks' => $request->marks,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Exam marks updated successfully',
                'exam_result' => $examResult,
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update exam marks',
                'error' => $th->getMessage(),
            ], 500);
        }
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

        $exam = Exam::find($request->exam_id);
        $student = Student::find($request->student_id);

        if (!$exam || !$student) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid exam or student',
            ], 422);
        }


        if ($exam->class_id !== $student->class_id) {
            return response()->json([
                'status' => false,
                'message' => "This student is not enrolled in the class for this exam.",
            ], 422);
        }


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
                    Rule::unique('exam_results')->where(function ($query) use ($request, $examResult) {
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


        $exam = Exam::find($request->exam_id);
        $student = Student::find($request->student_id);

        if (!$exam || !$student) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid exam or student',
            ], 422);
        }


        if ($exam->class_id !== $student->class_id) {
            return response()->json([
                'status' => false,
                'message' => "This student is not enrolled in the class for this exam.",
            ], 422);
        }


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
