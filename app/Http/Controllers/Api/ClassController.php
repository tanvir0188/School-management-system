<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    //
    public function index()
    {
        $classes['classes'] = ClassModel::orderBy('name', 'asc')->get();
        if ($classes['classes']->count() > 0) { //use total() for paginate
            return response()->json([
                'status' => true,
                'classes' => $classes,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No classes found',
        ], 404);
    }

    public function store(Request $request)
    {
        $validateClass = Validator::make(
            $request->all(),
            [
                'name' => 'required|integer|min:1|max:12|unique:classes',
            ]
        );
        if ($validateClass->fails()) {
            return response()->json([
                'status' => false,
                'errors' =>  $validateClass->errors()->all(),

            ], 400);
        }

        $class = ClassModel::create([
            'name' => $request->name,
        ]);
        if ($class) {
            return response()->json([
                'status' => true,
                'message' => 'Class created successfully',
            ], 201);
        }
        return response()->json([
            'status' => false,
            'message' => 'Failed to create class',
        ], 500); // Internal Server Error
    }
    public function destroy($id)
    {
        $class = ClassModel::find($id);
        if ($class) {

            try {
                $class->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Class deleted successfully',
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete class',
                ], 500); // Internal Server Error
            }
        }
        return response()->json([
            'status' => false,
            'message' => 'Class not found',
        ],);
    }
}
