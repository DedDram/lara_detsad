<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('i1il4_ads_city', function (Blueprint $table) {
            $table->id();
            $table->string('alias', 255);
            $table->string('name', 100);
        });
        DB::statement('ALTER TABLE i1il4_ads_city AUTO_INCREMENT = 4');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('i1il4_ads_city');
    }
};
