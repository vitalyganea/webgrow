<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scripts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->enum('type', ['inline', 'external'])->default('inline'); // Script type
            $table->text('content')->nullable(); // For inline scripts
            $table->enum('position', ['head', 'body_top', 'body_bottom'])->default('body_bottom'); // Where to place the script
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scripts');
    }
};
