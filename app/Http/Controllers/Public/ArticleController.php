<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Admin\Page;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('public/articles/index');
    }
}
