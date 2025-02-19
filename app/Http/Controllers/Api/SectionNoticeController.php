<?php

namespace App\Http\Controllers\Api;

use App\Models\Section;
use Illuminate\Http\Request;
use App\Models\SectionNotice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SectionNoticeController extends Controller
{
    public function index()
    {
        $sectionNotices = SectionNotice::with(['section.class'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        if ($sectionNotices->total() > 0) {
            return response()->json([
                'status' => true,
                'sectionNotices' => $sectionNotices->map(function ($notice) {
                    return [
                        'id' => $notice->id,
                        'title' => $notice->title,
                        'content' => $notice->content,
                        'created_at' => $notice->created_at,
                        'section_name' => $notice->section->name ?? 'N/A',
                        'class_name' => $notice->section->class->name ?? 'N/A',
                    ];
                }),
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No announcements yet',
        ], 404);
    }
    public function indexBySection($id)
    {
        // Check if the section exists
        $section = Section::find($id);
        if (!$section) {
            return response()->json([
                'status' => false,
                'message' => 'Section not found',
            ], 404);
        }

        // Get all notices for the given section ID
        $sectionNotices = SectionNotice::where('sec_id', $id)->paginate(5);

        // Check if notices exist
        if ($sectionNotices->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No notices found for this section',
            ], 404);
        }

        // Return the notices
        return response()->json([
            'status' => true,
            'notices' => $sectionNotices,
            'noticeCount' => $sectionNotices->count(),
        ], 200);
    }

    public function show($id)
    {
        $sectionNotice = SectionNotice::with(['section.class'])->find($id);
        if (!$sectionNotice) {
            return response()->json([
                'status' => false,
                'message' => "Couldn't find the announcement",
            ], 404);
        }
        return response()->json([
            'status' => true,
            'sectionNotice' => [
                'id' => $sectionNotice->id,
                'title' => $sectionNotice->title,
                'content' => $sectionNotice->content,
                'created_at' => $sectionNotice->created_at,
                'section_name' => $sectionNotice->section->name ?? 'N/A',
                'class_name' => $sectionNotice->section->class->name ?? 'N/A',
            ],
        ], 200);
    }
    public function destroy($id)
    {
        $teacher = Auth::guard('sanctum:api-teacher')->user();
        if (!$teacher) {
            return response()->json([
                'status' => false,
                'error' => 'Unauthorized. Please log in as a teacher.'
            ], 401);
        }

        // Fetch the notice
        $sectionNotice = SectionNotice::find($id);
        if (!$sectionNotice) {
            return response()->json([
                'status' => false,
                'error' => 'Notice not found.'
            ], 404);
        }

        // Ensure the teacher owns the section of the notice
        $teacherSectionIds = Section::where('teacher_id', $teacher->id)->pluck('id')->toArray();
        if (!in_array($sectionNotice->sec_id, $teacherSectionIds)) {
            return response()->json([
                'status' => false,
                'error' => 'Unauthorized. You can only delete notices for sections you teach.'
            ], 403);
        }

        try {
            $sectionNotice->delete();
            return response()->json([
                'status' => true,
                'message' => 'Notice deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $teacher = Auth::guard('sanctum:api-teacher')->user();
        if (!$teacher) {
            return response()->json([
                'status' => false,
                'error' => 'Unauthorized. Please log in as a teacher.'
            ], 401);
        }
        $teacherSectionIds = Section::where('teacher_id', $teacher->id)->pluck('id')->toArray();

        $sectionNoticesValidator = Validator::make(
            $request->all(),
            [
                'sec_id' => ['required', 'integer', function ($attribute, $value, $fail) use ($teacherSectionIds) {
                    if (!in_array($value, $teacherSectionIds)) {
                        $fail('Unauthorized. You can only post notices for sections you teach.');
                    }
                }],
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]
        );
        if ($sectionNoticesValidator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $sectionNoticesValidator->errors(),
            ], 422);
        }
        try {
            $sectionNotice = SectionNotice::create([
                'sec_id' => $request->sec_id,
                'title' => $request->title,
                'content' => $request->content,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Notice posted successfully',
                'sectionNotice' => $sectionNotice,
            ], 201);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $teacher = Auth::guard('sanctum:api-teacher')->user();
        if (!$teacher) {
            return response()->json([
                'status' => false,
                'error' => 'Unauthorized. Please log in as a teacher.'
            ], 401);
        }

        // Fetch the notice
        $sectionNotice = SectionNotice::find($id);
        if (!$sectionNotice) {
            return response()->json([
                'status' => false,
                'error' => 'Notice not found.'
            ], 404);
        }

        // Ensure the teacher owns the section of the notice
        $teacherSectionIds = Section::where('teacher_id', $teacher->id)->pluck('id')->toArray();
        if (!in_array($sectionNotice->sec_id, $teacherSectionIds)) {
            return response()->json([
                'status' => false,
                'error' => 'Unauthorized. You can only update notices for sections you teach.'
            ], 403);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'sec_id' => ['required', 'integer', function ($attribute, $value, $fail) use ($teacherSectionIds) {
                if (!in_array($value, $teacherSectionIds)) {
                    $fail('Unauthorized. You can only assign notices to sections you teach.');
                }
            }],
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $sectionNotice->update([
                'sec_id' => $request->sec_id,
                'title' => $request->title,
                'content' => $request->content,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Notice updated successfully',
                'sectionNotice' => $sectionNotice,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Something went wrong. Please try again.',
                'details' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }
}
