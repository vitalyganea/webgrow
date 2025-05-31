<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    use HasFactory;

    protected $fillable = ['page_id', 'block_name', 'language_code', 'content', 'order'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
