<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_tags', function (Blueprint $table) {
            $table->id();
            $table->string('seo_tag', 50)->unique();
            $table->enum('type', ['text', 'image'])->default('text');
            $table->text('tag_format')->nullable();
            $table->timestamps();
        });

        DB::table('seo_tags')->insert([
            [
                'seo_tag' => 'title',
                'type' => 'text',
                'tag_format' => null, // Explicitly set to null for clarity
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'seo_tag' => 'description',
                'type' => 'text',
                'tag_format' => '<meta name="description" content="{{value}}">',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'seo_tag' => 'keywords',
                'type' => 'text',
                'tag_format' => '<meta name="keywords" content="{{value}}">',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'seo_tag' => 'author',
                'type' => 'text',
                'tag_format' => '<meta name="author" content="{{value}}">',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'seo_tag' => 'viewport',
                'type' => 'text',
                'tag_format' => '<meta name="viewport" content="{{value}}">',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'seo_tag' => 'robots',
                'type' => 'text',
                'tag_format' => '<meta name="robots" content="{{value}}">',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'seo_tag' => 'og:title',
                'type' => 'text',
                'tag_format' => '<meta property="og:title" content="{{value}}">',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'seo_tag' => 'og:description',
                'type' => 'text',
                'tag_format' => '<meta property="og:description" content="{{value}}">',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'seo_tag' => 'og:image',
                'type' => 'image',
                'tag_format' => '<meta property="og:image" content="{{value}}">',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'seo_tag' => 'twitter:card',
                'type' => 'text',
                'tag_format' => '<meta name="twitter:card" content="{{value}}">',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'seo_tag' => 'twitter:title',
                'type' => 'text',
                'tag_format' => '<meta name="twitter:title" content="{{value}}">',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_tags');
    }
};
