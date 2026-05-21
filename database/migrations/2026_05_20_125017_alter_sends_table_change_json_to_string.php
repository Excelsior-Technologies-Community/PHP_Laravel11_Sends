<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sends', function (Blueprint $table) {
            $table->string('from')->nullable()->change();
            $table->string('to')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sends', function (Blueprint $table) {
            $table->json('from')->nullable()->change();
            $table->json('to')->nullable()->change();
        });
    }
};