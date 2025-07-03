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
            'seo_tag' => 'required|string|max:50|unique:seo_tags',
            'type' => 'required|in:text,image',
            'tag_format' => 'required|string',
        ]);

        SeoTag::create($request->all());

        return redirect()->route('admin.seo_tags.index')
            ->with('success', 'SEO Tag created successfully.');
    }

    public function edit(SeoTag $seoTag)
    {
        return view('admin.dashboard.seo_tags.edit', compact('seoTag'));
    }

    public function update(Request $request, SeoTag $seoTag)
    {
        $request->validate([
            'seo_tag' => 'required|string|max:50|unique:seo_tags,seo_tag,' . $seoTag->id,
            'type' => 'required|in:text,image',
            'tag_format' => 'required|string',
        ]);

        $seoTag->update($request->all());

        return redirect()->route('admin.seo_tags.index')
            ->with('success', 'SEO Tag updated successfully.');
    }

    public function destroy(SeoTag $seoTag)
    {
        $seoTag->delete();

        return redirect()->route('admin.seo_tags.index')
            ->with('success', 'SEO Tag deleted successfully.');
    }
}
