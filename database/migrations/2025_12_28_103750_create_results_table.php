<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('placing')->nullable();

            $table->integer('score')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();

            // 同一ユーザーの同一大会結果を1件にする
            $table->unique(['user_id', 'tournament_id']);

            $table->index(['tournament_id', 'placing']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
