<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherProfile;
use Exception;
use Illuminate\Support\Facades\Validator;

class TeacherProfileController extends Controller
{
    //
    public function index()
    {
        $teacherProfiles['teacherProfiles'] = TeacherProfile::orderBy('full_name', 'asc')->paginate(10);
        if ($teacherProfiles['teacherProfiles']->total() > 0) {
            return response()->json([
                'status' => true,
                'teacherProfiles' => $teacherProfiles,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No teacher profiles found',
        ], 404);
    }
    public function store(Request $request)
    {
        $teacherProfileValidator = Validator::make(
            $request->all(),
            [
                'full_name' => 'required',
                'teacher_id' => 'required|exists:teachers,id',
                'phone_number' => 'required',
                'address' => 'required',
                'age' => 'required|numeric|min:6',
                'father_name' => 'nullable',
                'mother_name' => 'nullable',
                'photo' =>  'nullable|mimes:jpg,jpeg,png,gif',
                'description' => 'nullable|min:20',
                'position' => 'required',

            ]
        );
        if ($teacherProfileValidator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid input',
                'errors' => $teacherProfileValidator->errors()->all(),
            ], 422); // Unprocessable Entity
        }
        $img = $request->file('photo');
        $imgName = time() . '.' . $img->getClientOriginalExtension();
        $img->move(public_path() . '/teachers', $imgName);
        try {
            //code...
            $teacherProfile = TeacherProfile::create([
                'full_name' => $request->full_name,
                'teacher_id' => $request->teacher_id,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'age' => $request->age,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'photo' => $imgName,
                'description' => $request->description,
                'position' => $request->position,

            ]);
            if ($teacherProfile) {
                return response()->json([
                    'status' => true,
                    'message' => 'Teacher profile created successfully',
                    'teacherProfile' => $teacherProfile,
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'Failed to create teacher profile',

            ], 500); // Internal Server Error
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $teacherProfileValidator = Validator::make(
            $request->all(),
            [
                'full_name' => 'required',
                'teacher_id' => 'required|exists:teachers,id',
                'phone_number' => 'required',
                'address' => 'required',
                'age' => 'required|numeric|min:6',
                'father_name' => 'nullable',
                'mother_name' => 'nullable',
                'photo' =>  'nullable|mimes:jpg,jpeg,png,gif',
                'description' => 'nullable|min:20',
                'position' => 'required',

            ]
        );
        if ($teacherProfileValidator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid input',
                'errors' => $teacherProfileValidator->errors()->all(),
            ], 422); // Unprocessable Entity
        }
        $teacherProfile = TeacherProfile::find($id);
        if (!$teacherProfile) {
            return response()->json([
                'status' => false,
                'message' => 'Teacher profile not found',
            ], 404); // Not Found
        }
        $teacherProfile->full_name = $request->input('full_name');
        $teacherProfile->teacher_id = $request->input('teacher_id');
        $teacherProfile->phone_number = $request->input('phone_number');
        $teacherProfile->address = $request->input('address');
        $teacherProfile->age = $request->input('age');
        $teacherProfile->father_name = $request->input('father_name');
        $teacherProfile->mother_name = $request->input('mother_name');
        $teacherProfile->description = $request->input('description');
        $teacherProfile->position = $request->input('position');

        if ($request->hasFile('photo')) {
            if ($teacherProfile->photo && file_exists(public_path('teachers/' . $teacherProfile->photo))) {
                unlink(public_path('teachers/' . $teacherProfile->photo));
            }

            // Save the new image
            $img = $request->file('photo');
            $imgName = time() . '.' . $img->getClientOriginalExtension();
            $img->move(public_path('teachers'), $imgName);

            $teacherProfile->photo = $imgName;
        } else {

            if ($teacherProfile->photo && file_exists(public_path('teachers/' . $teacherProfile->photo))) {
                unlink(public_path('teachers/' . $teacherProfile->photo));
            }
            $teacherProfile->photo = null; // Set the photo column to null
        }
        $teacherProfile->save();
        return response()->json([
            'status' => true,
            'message' => 'Teacher profile updated successfully',
            'data' => $teacherProfile,
        ], 200); // OK
    }

    public function show($id)
    {
        $teacherProfile = TeacherProfile::find($id);
        if (!$teacherProfile) {
            return response()->json([
                'status' => false,
                'message' => 'Teacher profile not found',
            ], 404); // Not Found
        }
        return response()->json([
            'status' => true,
            'teacherProfile' => $teacherProfile,
        ], 200);
    }
}
