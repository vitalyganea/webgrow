<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\SeoTag;
use Illuminate\Http\Request;

class SeoTagController extends Controller
{
    public function index()
    {
        $seoTags = SeoTag::paginate(10);
        return view('admin.dashboard.seo_tags.index', compact('seoTags'));
    }

    public function create()
    {
        return view('admin.dashboard.seo_tags.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'seo_tag' => 'required|string',
        ]);

        SeoTag::create([
            'seo_tag' => $request->seo_tag,
        ]);

        return redirect()->route('admin.get.seo-tags')->with('success', 'Seo Tag created successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(SeoTag $seoTag)
    {
        return view('admin.dashboard.seo_tags.edit', compact('seoTag'));
    }

    public function update(Request $request, SeoTag $seoTag)
    {
        $validatedData = $request->validate([
            'seo_tag' => 'required|string',
        ]);

        $seoTag->update($validatedData);
        return redirect()->route('admin.get.seo-tags')->with('success', 'Seo Tag updated successfully.');

    }

    public function destroy(SeoTag $seoTag)
    {
        $seoTag->delete();
        return redirect()->route('admin.get.seo-tags')->with('success', 'Seo Tag deleted successfully.');
    }
}
