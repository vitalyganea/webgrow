<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Admin\Page;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        // Fix the query: `first()` returns a single model, so you can't chain `where()` after it.
        // You probably want to filter first, then get the first result with its contents.
        $homePage = Page::where('language', 'ro')->with('contents')->first();

        // Pass $homePage to the view
        return view('public.home.index', ['homePage' => $homePage]);
    }
}
