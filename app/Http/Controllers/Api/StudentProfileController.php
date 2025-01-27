<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\StudentProfile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StudentProfileController extends Controller
{
    //
    public function index()
    {
        $studentProfiles['student_profiles'] = StudentProfile::orderBy('full_name', 'asc')->paginate(10);
        if ($studentProfiles['student_profiles']->total() > 0) {
            return response()->json([
                'status' => true,
                'studentProfiles' => $studentProfiles,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'No student profiles found',
        ], 404);
    }
    public function store(Request $request)
    {
        $validateStudentProfile = Validator::make(
            $request->all(),
            [
                'full_name' => 'required',
                'student_id' => 'required|exists:students,id',
                'phone_number' => 'required',
                'address' => 'required',
                'age' => 'required|numeric|min:6',
                'father_name' => 'nullable',
                'mother_name' => 'nullable',
                'photo' =>  'nullable|mimes:jpg,jpeg,png,gif',
            ]
        );
        if ($validateStudentProfile->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validateStudentProfile->errors()->all(),
            ], 422); // Unprocessable Entity
        }

        $img = $request->file('photo');
        $imgName = time() . '.' . $img->getClientOriginalExtension();
        $img->move(public_path() . '/students', $imgName);

        $studentProfile = StudentProfile::create([
            'full_name' => $request->full_name,
            'student_id' => $request->student_id,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'age' => $request->age,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'photo' => $imgName,

        ]);
        return response()->json([
            'status' => true,
            'message' => 'Student profile created successfully',
            'studentProfile' => $studentProfile,
        ], 201); // Created
    }
    public function show($id)
    {
        $studentProfile = StudentProfile::find($id);
        if (!$studentProfile) {
            return response()->json([
                'status' => false,
                'message' => 'Student profile not found',

            ], 404); // Not Found
        }
        return response()->json([
            'status' => true,
            'studentProfile' => $studentProfile,
        ], 200); // OK
    }

    public function update(Request $request, $id)
    {
        $validateStudentProfile = Validator::make(
            $request->all(),
            [
                'full_name' => 'required',
                'student_id' => 'required|exists:students,id',
                'phone_number' => 'required',
                'address' => 'required',
                'age' => 'required|numeric|min:6',
                'father_name' => 'nullable',
                'mother_name' => 'nullable',
                'photo' =>  'nullable|mimes:jpg,jpeg,png,gif',
            ]
        );

        if ($validateStudentProfile->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validateStudentProfile->errors()->all(),
            ], 422); // Unprocessable Entity
        }

        $studentProfile = StudentProfile::find($id);
        if (!$studentProfile) {
            return response()->json([
                'status' => false,
                'message' => 'Student profile not found',
            ], 404); // Not Found
        }

        // Update fields
        $studentProfile->full_name = $request->input('full_name');
        $studentProfile->student_id = $request->input('student_id');
        $studentProfile->phone_number = $request->input('phone_number');
        $studentProfile->address = $request->input('address');
        $studentProfile->age = $request->input('age');
        $studentProfile->father_name = $request->input('father_name');
        $studentProfile->mother_name = $request->input('mother_name');

        if ($request->hasFile('photo')) {
            if ($studentProfile->photo && file_exists(public_path('students/' . $studentProfile->photo))) {
                unlink(public_path('students/' . $studentProfile->photo));
            }

            // Save the new image
            $img = $request->file('photo');
            $imgName = time() . '.' . $img->getClientOriginalExtension();
            $img->move(public_path('students'), $imgName);

            $studentProfile->photo = $imgName;
        } else {

            if ($studentProfile->photo && file_exists(public_path('students/' . $studentProfile->photo))) {
                unlink(public_path('students/' . $studentProfile->photo));
            }
            $studentProfile->photo = null; // Set the photo column to null
        }

        $studentProfile->save();

        return response()->json([
            'status' => true,
            'message' => 'Student profile updated successfully',
            'studentProfile' => $studentProfile,
        ], 200); // OK
    }
}
