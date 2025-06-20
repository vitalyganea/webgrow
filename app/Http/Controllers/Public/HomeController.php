<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Admin\Page;
use App\Models\Admin\SeoTag;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $lang = 'ro';

        $seoTags = SeoTag::all();

        $homePage = Page::where('language', $lang)
            ->with(['contents', 'seoValues.tag'])
            ->first();

        $seoTagsWithValues = [];

        foreach ($seoTags as $tag) {
            $value = $homePage->seoValues
                ->where('seo_tag_id', $tag->id)
                ->where('lang', $lang)
                ->first()?->value; // Use null-safe operator in case it's missing

            if($tag->tag_format == null){
                $seoTagsWithValues[] = '<'.$tag->seo_tag.'>'.$value.'</'.$tag->seo_tag.'>';
            }else{
                $seoTagsWithValues[] = str_replace("{{value}}", $value, $tag->tag_format);
            }
        }

        return view('public.home.index', [
            'homePage' => $homePage,
            'seoTagsWithValues' => $seoTagsWithValues,
        ]);
    }
}
