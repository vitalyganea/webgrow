<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Admin\FormRequest;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function store(Request $request)
    {
        $requestBody = $request->except('currentUrl');

        FormRequest::create([
            'url' => $request->currentUrl,
            'requestBody' => json_encode($requestBody),
        ]);

        return response()->json(['message' => 'Contact form submitted successfully'], 200);
    }
}
