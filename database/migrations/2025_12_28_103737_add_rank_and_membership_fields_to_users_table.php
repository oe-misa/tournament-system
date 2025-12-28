<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 段位（ranksへの参照）
            $table->foreignId('rank_id')
                ->nullable()
                ->after('password')
                ->constrained('ranks')
                ->nullOnDelete();

            // 年間登録期限（ユーザーの現在状態を素早く参照するため）
            $table->date('membership_expires_at')
                ->nullable()
                ->after('rank_id');

            // 管理者フラグ
            $table->boolean('is_admin')
                ->default(false)
                ->after('membership_expires_at');

            $table->index(['rank_id']);
            $table->index(['membership_expires_at']);
            $table->index(['is_admin']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rank_id');
            $table->dropColumn(['membership_expires_at', 'is_admin']);
        });
    }
};
