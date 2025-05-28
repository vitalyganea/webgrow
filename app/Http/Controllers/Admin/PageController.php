<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->paginate(10);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
        ]);

        Page::create($data);

        return redirect()->route('admin.get.pages')->with('success', 'Page created.');
    }

    public function edit(Page $page)
    {
        $contents = $page->contents;
        return view('admin.pages.edit', compact('page', 'contents'));
    }

    public function update(Request $request, Page $page)
    {
        // Validate
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'contents' => 'array',
            'contents.*.type' => 'required|string|in:title,text,image',
            'contents.*.content' => 'nullable|string',
            'contents.*.image_file' => 'nullable|file|image|max:2048',
        ]);

        // Update page info
        $page->update([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
        ]);

        $submittedContents = $request->input('contents', []);
        $submittedFiles = $request->file('contents', []);

        $existingContentIds = [];

        foreach ($submittedContents as $index => $block) {
            $type = $block['type'];
            $id = $block['id'] ?? null;
            $contentData = null;

            if ($type === 'image') {
                if (isset($submittedFiles[$index]['image_file'])) {
                    $path = $submittedFiles[$index]['image_file']->store('page_contents', 'public');
                    $contentData = $path;
                } else {
                    $contentData = $block['content'] ?? null; // keep existing path
                }
            } else {
                $contentData = $block['content'] ?? null;
            }

            if ($id) {
                // Update existing
                $content = $page->contents()->where('id', $id)->first();
                if ($content) {
                    $content->update([
                        'type' => $type,
                        'content' => $contentData,
                    ]);
                    $existingContentIds[] = $content->id;
                }
            } else {
                // Create new
                $new = $page->contents()->create([
                    'type' => $type,
                    'content' => $contentData,
                ]);
                $existingContentIds[] = $new->id;
            }
        }

        // Delete removed blocks
        $page->contents()->whereNotIn('id', $existingContentIds)->delete();

        return redirect()->route('admin.edit.page', $page)->with('page-updated', 'Page updated successfully!');
    }



    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.get.pages')->with('success', 'Page deleted.');
    }
}
