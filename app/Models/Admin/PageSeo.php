<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSeo extends Model
{
    protected $fillable = ['seo_tag', 'page_group_id', 'value', 'seo_tag_id', 'lang'];

    use HasFactory;

    public function tag()
    {
        return $this->belongsTo(SeoTag::class, 'seo_tag_id');
    }
}
