<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Language;
use App\Models\Admin\Page;
use App\Models\Admin\PageSeo;
use App\Models\Admin\SeoTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
            ->with(['contents', 'seoValues.tag'])
            ->get()
            ->keyBy('language');

        $languages = Language::all();
        $seoTags = SeoTag::all();

        if ($pages->isEmpty()) {
            return redirect()->route('admin.get.pages')->with('error', 'Page not found.');
        }

        $blockContents = [];
        $seoData = [];

        foreach ($pages as $langCode => $page) {
            // Page content blocks
            foreach ($page->contents as $content) {
                $blockContents[$langCode][$content->id] = [
                    'content' => $content->content,
                    'type' => $content->type,
                ];
            }

            // SEO values indexed by seo_tag_id for easier lookup
            $seoValuesByTagId = $page->seoValues
                ->where('lang', $langCode)
                ->keyBy('seo_tag_id');

            // Match each tag to its value or null
            foreach ($seoTags as $tag) {
                $seoData[$langCode][$tag->seo_tag] = $seoValuesByTagId[$tag->id]->value ?? null;
            }
        }

        return view('admin.dashboard.pages.edit', compact('pages', 'languages', 'group_id', 'blockContents', 'seoData', 'seoTags'));
    }

    public function update(Request $request, $group_id)
    {
        $data = $request->validate([
            'pages.*.title' => 'required|string|max:255',
            'pages.*.slug' => 'required|string|max:255',
            'pages.*.language_code' => 'required|string|exists:languages,code',
            'pages.*.blocks' => 'nullable|array',
            'pages.*.blocks.*.content' => 'nullable|string',
            'pages.*.blocks.*.type' => 'required|string',
            'pages.*.blocks_order' => 'nullable|array',
            'pages.*.blocks_order.*' => 'nullable|integer',
            'pages.*.deleted_blocks' => 'nullable|array',
            'pages.*.deleted_blocks.*' => 'nullable|integer|exists:page_contents,id',
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

            // Handle deleted blocks
            if (!empty($pageData['deleted_blocks']) && is_array($pageData['deleted_blocks'])) {
                foreach ($pageData['deleted_blocks'] as $blockId) {
                    if ($blockId) {
                        $page->contents()->where('id', $blockId)->delete();
                    }
                }
            }

            // Save blocks content and order
            if (!empty($pageData['blocks']) && is_array($pageData['blocks'])) {
                foreach ($pageData['blocks'] as $blockId => $blockData) {
                    $pageContent = $page->contents()
                        ->where('id', $blockId)
                        ->where('language_code', $languageCode)
                        ->first();

                    $order = 0;
                    if (!empty($pageData['blocks_order'][$blockId])) {
                        $order = (int) $pageData['blocks_order'][$blockId];
                    }

                    if ($pageContent) {
                        $pageContent->update([
                            'content' => $blockData['content'],
                            'type' => $blockData['type'],
                            'order' => $order,
                        ]);
                    } else {
                        $page->contents()->create([
                            'language_code' => $languageCode,
                            'content' => $blockData['content'],
                            'type' => $blockData['type'],
                            'order' => $order,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.edit.page', $group_id)->with('success', 'Page updated.');
    }

    public function destroy($pagesGroupId)
    {
        // Get all pages by group_id
        $pages = Page::where('group_id', $pagesGroupId)->get();

        // Delete seo
        PageSeo::where('page_group_id', $pagesGroupId)->delete();

        // Loop through each page to delete its contents and then the page
        foreach ($pages as $page) {
            // Delete related content blocks
            $page->contents()->delete();
            // Delete the page itself
            $page->delete();
        }

        return redirect()->route('admin.get.pages')->with('success', 'Pages and their content blocks deleted.');
    }

    public function updateSeo(Request $request, $group_id)
    {
        $request->validate([
            'language_code' => 'required|string',
            'seo' => 'required|array',
            'seo_images' => 'nullable|array',
            'seo_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $languageCode = $request->input('language_code');
        $seoData = $request->input('seo');
        $seoImages = $request->file('seo_images');

        foreach ($seoData as $tag => $value) {
            $seoTag = SeoTag::where('seo_tag', $tag)->first();

            if ($seoTag) {
                $finalValue = $value;

                // Handle image uploads for image type SEO tags
                if ($seoTag->type === 'image' && isset($seoImages[$tag])) {
                    $file = $seoImages[$tag];
                    $filename = time() . '_' . $tag . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('seo-images', $filename, 'public');
                    $finalValue = Storage::url($path);
                } elseif ($seoTag->type === 'image' && empty($value)) {
                    // If it's an image field but no new image uploaded and no existing value, skip
                    continue;
                }

                if (!is_null($finalValue)) {
                    PageSeo::updateOrCreate(
                        [
                            'page_group_id' => $group_id,
                            'seo_tag_id' => $seoTag->id,
                            'lang' => $languageCode,
                        ],
                        ['value' => $finalValue]
                    );
                }
            }
        }

        return redirect()->route('admin.edit.page', $group_id)->with('success', 'SEO updated successfully.');
    }

    public function uploadSeoImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('seo-images', $filename, 'public');
        $url = Storage::url($path);

        return response()->json(['location' => $url]);
    }
}
