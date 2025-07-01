<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\FormRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Request;

class FormController extends Controller
{
    public function index()
    {
        $formRequests = FormRequest::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.dashboard.form_requests.index', compact('formRequests'));
    }

    public function markSeen(Request $request, $id)
    {
        try {
            $formRequest = FormRequest::findOrFail($id);
            $formRequest->seen = 1;
            $formRequest->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to mark as seen'], 500);
        }
    }
}
