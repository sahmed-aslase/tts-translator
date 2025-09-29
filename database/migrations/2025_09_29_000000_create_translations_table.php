<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->text('source_text');
            $table->string('source_lang', 5)->default('en');
            $table->string('target_lang', 5);
            $table->text('translated_text');
            $table->string('voice_name')->nullable();
            $table->float('speech_rate')->nullable();
            $table->float('speech_pitch')->nullable();
            $table->string('audio_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
