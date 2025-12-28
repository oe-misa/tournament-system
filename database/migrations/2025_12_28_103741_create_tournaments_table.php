<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();

            // 開催日
            $table->date('event_date');

            // 申込締切
            $table->dateTime('entry_deadline')->nullable();

            // 定員（nullなら無制限）
            $table->unsignedInteger('capacity')->nullable();

            // 参加可能最低段位（A案）
            $table->unsignedTinyInteger('min_rank_level')->default(0);

            $table->timestamps();

            $table->index(['event_date']);
            $table->index(['entry_deadline']);
            $table->index(['min_rank_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
