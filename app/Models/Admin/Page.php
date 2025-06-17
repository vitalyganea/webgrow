<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'language',
        'group_id'
    ];


    protected static function booted()
    {
        static::deleting(function ($page) {
            $page->contents()->delete();
        });
    }

    public function contents()
    {
        return $this->hasMany(PageContent::class)->orderBy('order');
    }

    public function seoValues()
    {
        return $this->hasMany(PageSeo::class, 'page_group_id', 'group_id')
            ->with('tag');
    }

}
