<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rank_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 申請先の段位
            $table->foreignId('rank_id')->constrained('ranks')->restrictOnDelete();

            // pending / approved / rejected
            $table->string('status', 20)->default('pending');

            // 申請理由など（任意）
            $table->text('comment')->nullable();

            $table->dateTime('requested_at');
            $table->dateTime('approved_at')->nullable(); // 承認/却下日時（名称は従来仕様に合わせ）

            // 処理した管理者（任意）
            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'requested_at']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rank_requests');
    }
};
