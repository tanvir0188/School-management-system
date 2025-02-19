<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoticeController extends Controller
{
    //
    public function index()
    {
        $notices['notices'] = Notice::orderBy('created_at', 'desc')->paginate(5);
        if ($notices['notices']->total() > 0) {
            return response()->json([
                'status' => true,
                'notices' => $notices,
                'noticeCount' => $notices['notices']->total(),
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No notices found',
        ], 404);
    }
    public function getNoticeCount()
    {
        $count = Notice::count();
        if ($count) {
            return response()->json([
                'status' => true,
                'noticeCount' => $count,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No notices found',
        ]);
    }
    public function store(Request $request)
    {
        $noticeValidator = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'content' => 'required',
            ]
        );
        if ($noticeValidator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $noticeValidator->errors()->all(),
            ], 422); // Unprocessable Entity
        }
        try {
            $notice = Notice::create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Notice created successfully',
                'notice' => $notice,
            ], 201); // Created
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function show($id)
    {
        $notice = Notice::find($id);
        if ($notice) {
            return response()->json([
                'status' => true,
                'notice' => $notice,
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'Notice not found',
        ], 404);
    }
    public function update(Request $request, $id)
    {
        $noticeValidator = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'content' => 'required',
            ]
        );
        if ($noticeValidator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $noticeValidator->errors()->all(),
            ], 422); // Unprocessable Entity
        }
        $notice = Notice::find($id);
        if (!$notice) {
            return response()->json([
                'status' => false,
                'message' => 'Notice not found',
            ], 404);
        }
        try {
            $notice->title = $request->input('title');
            $notice->content = $request->input('content');
            $notice->save();
            return response()->json([
                'status' => true,
                'message' => 'Notice updated successfully',

            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function destroy($id)
    {
        $notice = Notice::find($id);
        if (!$notice) {
            return response()->json([
                'status' => false,
                'message' => 'Notice not found',
            ], 404);
        }
        try {
            $notice->delete();
            return response()->json([
                'status' => true,
                'message' => 'Notice deleted successfully',
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
