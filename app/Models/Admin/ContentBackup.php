<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentBackup extends Model
{
    protected $fillable = ['script'];

    protected $table = 'content_backups';
    protected $casts = [
        'script' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    use HasFactory;
}
