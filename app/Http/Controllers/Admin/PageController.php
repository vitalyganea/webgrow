<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Language;
use App\Models\Admin\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        // Step 1: Paginate unique group_ids
        $groupIds = Page::select('group_id')
            ->groupBy('group_id')
            ->latest(\DB::raw('MIN(id)')) // order by first created
            ->paginate(10);

        // Step 2: Fetch all pages for these group_ids
        $pages = Page::whereIn('group_id', $groupIds->pluck('group_id'))
            ->where('language', Language::first()->code) // use 'en' or your default language
            ->get()
            ->keyBy('group_id');

        // Step 3: Inject default language page into paginated object
        $paginatedPages = $groupIds->map(function ($group) use ($pages) {
            return $pages[$group->group_id] ?? null;
        })->filter();

        // Step 4: Create new paginator
        $finalPages = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedPages,
            $groupIds->total(),
            $groupIds->perPage(),
            $groupIds->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.dashboard.pages.index', ['pages' => $finalPages]);
    }

    public function create()
    {
        return view('admin.dashboard.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
        ]);

        $originalSlug = $data['slug'];
        $groupId = (string) Str::uuid(); // ✅ Called only ONCE

        Language::get()->each(function ($language) use ($data, $originalSlug, $groupId) {
            $data['slug'] = $originalSlug . '_' . $language->code;
            $data['language'] = $language->code;
            $data['group_id'] = $groupId; // ✅ Same for all
            Page::create($data);
        });

        return redirect()->route('admin.get.pages')->with('success', 'Page created.');
    }

    public function edit($group_id)
    {
        // Retrieve all pages for the given group_id, keyed by language code
        $pages = Page::where('group_id', $group_id)
            ->with('contents') // Eager load related contents
            ->get()
            ->keyBy('language');

        // Retrieve all available languages
        $languages = Language::all();

        // Redirect if no pages are found
        if ($pages->isEmpty()) {
            return redirect()->route('admin.get.pages')->with('error', 'Page not found.');
        }

        // Prepare block contents per language and block name
        $blockContents = [];

        foreach ($pages as $langCode => $page) {
            foreach ($page->contents as $content) {
                $blockContents[$langCode][$content->block_name] = $content->content;
            }
        }

        return view('admin.dashboard.pages.edit', compact('pages', 'languages', 'group_id', 'blockContents'));
    }

    public function update(Request $request, $group_id)
    {
        $data = $request->validate([
            'pages.*.title' => 'required|string|max:255',
            'pages.*.slug' => 'required|string|max:255',
            'pages.*.language_code' => 'required|string|exists:languages,code',
            'pages.*.blocks' => 'nullable|array',
            'pages.*.blocks.*' => 'nullable|string',
            'pages.*.blocks_order' => 'nullable|array',
            'pages.*.blocks_order.*' => 'nullable|integer',
        ]);

        // Fetch existing pages for the group_id
        $existingPages = Page::where('group_id', $group_id)->get()->keyBy('language');

        foreach ($data['pages'] as $languageCode => $pageData) {
            $page = $existingPages[$languageCode] ?? null;

            // Check slug uniqueness ignoring current group
            $slugCount = Page::where('slug', $pageData['slug'])
                ->where('group_id', '!=', $group_id)
                ->count();

            if ($slugCount > 0) {
                return back()->withErrors(['pages.' . $languageCode . '.slug' => 'The slug must be unique.'])->withInput();
            }

            if ($page) {
                $page->update([
                    'title' => $pageData['title'],
                    'slug' => $pageData['slug'],
                ]);
            } else {
                $page = Page::create([
                    'title' => $pageData['title'],
                    'slug' => $pageData['slug'],
                    'language' => $languageCode,
                    'group_id' => $group_id,
                ]);
            }

            // Save blocks content and order
            if (!empty($pageData['blocks']) && is_array($pageData['blocks'])) {
                foreach ($pageData['blocks'] as $blockName => $blockContent) {
                    $pageContent = $page->contents()
                        ->where('block_name', $blockName)
                        ->where('language_code', $languageCode)
                        ->first();

                    $order = 0;
                    if (!empty($pageData['blocks_order'][$blockName])) {
                        $order = (int) $pageData['blocks_order'][$blockName];
                    }

                    if ($pageContent) {
                        $pageContent->update([
                            'content' => $blockContent,
                            'order' => $order,
                        ]);
                    } else {
                        $page->contents()->create([
                            'block_name' => $blockName,
                            'language_code' => $languageCode,
                            'content' => $blockContent,
                            'order' => $order,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.get.pages')->with('success', 'Page updated.');
    }


    public function destroy($pagesGroupId)
    {
        // Get all pages by group_id
        $pages = Page::where('group_id', $pagesGroupId)->get();

        // Loop through each page to delete its contents and then the page
        foreach ($pages as $page) {
            // Delete related content blocks
            $page->contents()->delete();

            // Delete the page itself
            $page->delete();
        }

        return redirect()->route('admin.get.pages')->with('success', 'Pages and their content blocks deleted.');
    }

    public function editHtml()
    {
        $filePath = public_path('custom/index.html');

        if (!File::exists($filePath)) {
            File::put($filePath, '<!-- Start editing your page here -->');
        }

        $htmlContent = File::get($filePath);

        return view('admin.edit-html', compact('htmlContent'));
    }

    public function updateHtml(Request $request)
    {
        $request->validate([
            'html_content' => 'required|string',
        ]);

        $filePath = public_path('custom/index.html');
        File::put($filePath, $request->input('html_content'));

        return redirect()->route('admin.editHtml')->with('success', 'HTML updated successfully.');
    }
}
