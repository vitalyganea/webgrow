<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->uuid('group_id')->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            //
        });
    }
};
