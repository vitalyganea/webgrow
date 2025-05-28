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
        'content',
    ];


    public function contents()
    {
        return $this->hasMany(PageContent::class)->orderBy('order');
    }

}
