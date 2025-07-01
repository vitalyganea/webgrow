<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormRequest extends Model
{
    use HasFactory;

    protected $table = 'form_requests';

    protected $fillable = [
        'url',
        'requestBody',
        'seen',
    ];
}
