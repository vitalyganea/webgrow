<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('public.series.index');
    }
}
