<?php

namespace App\Http\Controllers\Api;

use App\Models\ExamType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ExamTypeController extends Controller
{
    //
    public function index()
    {
        $examTypes['examTypes'] = ExamType::orderBy('name', 'asc')->paginate(5);

        if ($examTypes['examTypes']->total() > 0) {
            return response()->json([
                'status' => true,
                'examTypes' => $examTypes,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No exam types found',
        ], 404);
    }

    public function store(Request $request)
    {
        $examTypeValidator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|unique:exam_types,name',
            ]
        );
        if ($examTypeValidator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $examTypeValidator->errors(),
            ], 422);
        }
        try {
            $examType = ExamType::create([
                'name' => $request->input('name'),

            ]);
            return response()->json([
                'status' => true,
                'message' => 'Exam type created successfully',
            ], 201);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create exam type',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $examTypeValidator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|unique:exam_types,name,' . $id,
            ]
        );


        if ($examTypeValidator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $examTypeValidator->errors(),
            ], 422);
        }

        try {
            $examType = ExamType::find($id); // Find the exam type by ID

            if (!$examType) {
                return response()->json([
                    'status' => false,
                    'message' => 'Exam type not found',
                ], 404);
            }

            $examType->update([
                'name' => $request->input('name'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Exam type updated successfully',
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update exam type',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $examType = ExamType::find($id); // Find the exam type by ID
            if (!$examType) {
                return response()->json([
                    'status' => false,
                    'message' => 'Exam type not found',
                ], 404);
            }
            $examType->delete();
            return response()->json([
                'status' => true,
                'message' => 'Exam type deleted successfully',
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete exam type',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
