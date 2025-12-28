<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();

            // entry / cancelled など（必要になったら追加）
            $table->string('status', 20)->default('entry');

            $table->timestamps();

            // 同一ユーザーが同一大会に複数エントリーできない
            $table->unique(['user_id', 'tournament_id']);

            $table->index(['tournament_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
