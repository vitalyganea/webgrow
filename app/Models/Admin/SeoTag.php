<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoTag extends Model
{
    protected $fillable = ['seo_tag', 'type', 'tag_format'];

    use HasFactory;

    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';

    public function getTypeOptions()
    {
        return [
            self::TYPE_TEXT => 'Text',
            self::TYPE_IMAGE => 'Image',
        ];
    }

    public function isImageType()
    {
        return $this->type === self::TYPE_IMAGE;
    }
}
