<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Language;
use App\Models\Admin\Page;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::paginate(10);
        return view('admin.language.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.language.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
        ]);

        // If checked as main, unset others
        if ($request->has('main')) {
            Language::query()->update(['main' => null]);
        }

        Language::create([
            'name' => $request->name,
            'code' => $request->code,
            'main' => $request->has('main') ? 1 : null,
        ]);

        return redirect()->route('admin.get.languages')->with('success', 'Language created successfully.');
    }

    public function edit(Language $language)
    {
        return view('admin.language.edit', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($request->has('main')) {
            Language::query()->update(['main' => null]);
        }

        $language->update([
            'name' => $request->name,
            'main' => $request->has('main') ? 1 : null,
            'code' => $request->code
        ]);

        return redirect()->route('admin.get.languages')->with('success', 'Language updated successfully.');
    }

    public function destroy(Language $language)
    {
        // Count total languages
        $totalLanguages = Language::count();

        // Prevent deletion if only one remains
        if ($totalLanguages <= 1) {
            return redirect()->route('admin.get.languages')->with('error', 'You cannot delete the last remaining language.');
        }

        // Delete all pages for the language
        Page::where('language', $language->code)->each(function ($page) {
            $page->delete();
        });

        // Delete the language itself
        $language->delete();

        return redirect()->route('admin.get.languages')->with('success', 'Language and its pages deleted successfully.');
    }
}

