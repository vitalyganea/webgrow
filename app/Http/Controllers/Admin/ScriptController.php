<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Script;
use Illuminate\Http\Request;

class ScriptController extends Controller
{
    public function index()
    {
        $scripts = Script::paginate(10);
        return view('admin.dashboard.scripts.index', compact('scripts'));
    }

    public function create()
    {
        return view('admin.dashboard.scripts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:inline,external',
            'content' => 'required|string',
            'position' => 'required|in:head,body_top,body_bottom',
        ]);

        Script::create($request->all());

        return redirect()->route('admin.get.scripts')
            ->with('success', 'Script created successfully.');
    }

    public function edit(Script $script)
    {
        return view('admin.dashboard.scripts.edit', compact('script'));
    }

    public function update(Request $request, Script $script)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:inline,external',
            'content' => 'required|string',
            'position' => 'required|in:head,body_top,body_bottom',
        ]);

        $script->update($request->all());

        return redirect()->route('admin.get.scripts')
            ->with('success', 'Script updated successfully.');
    }

    public function destroy(Script $script)
    {
        $script->delete();

        return redirect()->route('admin.get.scripts')
            ->with('success', 'Script deleted successfully.');
    }
}
