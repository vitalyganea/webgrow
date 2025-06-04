<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class BaseLayout extends Component
{
    public $title;

    public function __construct($title = null)
    {
        $this->title = $title;
    }

    public function render(): View|Closure|string
    {
        $routeName = request()->route()?->getName();
        $uri = request()->path(); // Gets the URI like "admin/dashboard"

        $authRoutes = [
            'admin.register',
            'admin.login',
            'admin.password.request',
            'admin.password.email',
            'admin.password.reset',
            'admin.password.store',
        ];

        // Case 1: Authenticated user and route starts with "admin"
        if (Auth::check() && str_starts_with($uri, 'admin')) {
            return view('components.admin.index');
        }

        // Case 2: Guest user accessing one of the allowed auth routes
        if (Auth::guest() && in_array($routeName, $authRoutes)) {
            return view('components.admin.index');
        }

        // Case 3: All other routes
        return view('public.base');
    }
}
