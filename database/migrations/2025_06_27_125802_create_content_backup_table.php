<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_backups', function (Blueprint $table) {
            $table->id();
            $table->longText('script');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_backups');
    }
};
