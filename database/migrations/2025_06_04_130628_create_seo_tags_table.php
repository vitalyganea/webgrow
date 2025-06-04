<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_tags', function (Blueprint $table) {
            $table->id();
            $table->text('seo_tag');
            $table->timestamps();
        });

        DB::table('seo_tags')->insert([
            ['seo_tag' => 'title', 'created_at' => now(), 'updated_at' => now()],
            ['seo_tag' => 'description', 'created_at' => now(), 'updated_at' => now()],
            ['seo_tag' => 'keywords', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_tags');
    }
};
