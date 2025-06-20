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
            $table->text('tag_format')->nullable();
            $table->timestamps();
        });

        DB::table('seo_tags')->insert([
            [
                'seo_tag' => 'title',
                'tag_format' => null, // Explicitly set to null for clarity
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'seo_tag' => 'description',
                'tag_format' => '<meta name="description" content="{{value}}">',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'seo_tag' => 'keywords',
                'tag_format' => '<meta name="keywords" content="{{value}}">',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_tags');
    }
};
