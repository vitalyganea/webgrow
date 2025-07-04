<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Admin\Language;
use App\Models\Admin\Page;
use App\Models\Admin\SeoTag;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke($langCode = null): View
    {
        $allLanguages = Language::all();

        // If a language code is provided in the URL, validate it exists in database
        if ($langCode) {
            $languageExists = $allLanguages->where('code', $langCode)->isNotEmpty();

            if (!$languageExists) {
                abort(404, 'Language not found');
            }

            // Language exists, store it in session
            Session::put('language', $langCode);
        }

        // Get the language from session, or fall back to the default language where main = 1
        $lang = Session::get('language') ?? Language::where('main', 1)->first()->code ?? 'ro';

        $seoTags = SeoTag::all();

        $homePage = Page::where('language', $lang)
            ->with(['contents', 'seoValues.tag'])
            ->first();

        // Optional: Also check if the home page exists for this language
        if (!$homePage) {
            abort(404, 'Page not found for the selected language');
        }

        $seoTagsWithValues = [];

        foreach ($seoTags as $tag) {
            $value = $homePage->seoValues
                ->where('seo_tag_id', $tag->id)
                ->where('lang', $lang)
                ->first()?->value; // Use null-safe operator in case it's missing

            if ($tag->tag_format == null) {
                $seoTagsWithValues[] = '<' . $tag->seo_tag . '>' . $value . '</' . $tag->seo_tag . '>';
            } else {
                $seoTagsWithValues[] = str_replace("{{value}}", $value, $tag->tag_format);
            }
        }

        return view('public.home.index', [
            'homePage' => $homePage,
            'seoTagsWithValues' => $seoTagsWithValues,
            'currentLanguage' => $lang,
            'allLanguages' => $allLanguages,
        ]);
    }
}
