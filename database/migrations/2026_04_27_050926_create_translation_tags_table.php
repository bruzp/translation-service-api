<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translation_tags', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('translation_id');
            $table->bigInteger('tag_id');

            $table->index(['translation_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_tags');
    }
};
