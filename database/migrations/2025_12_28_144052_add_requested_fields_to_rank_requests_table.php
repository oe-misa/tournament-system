<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rank_requests', function (Blueprint $table) {
            // まだ無い前提で追加（すでにあるなら migration 実行時にエラーになるので教えて）
            $table->unsignedBigInteger('requested_rank_id')->nullable()->after('status');
            $table->unsignedTinyInteger('requested_level')->nullable()->after('requested_rank_id');
            $table->text('note')->nullable()->after('requested_level');

            $table->index('requested_rank_id');
            $table->index('requested_level');

            // ranks テーブルが存在する前提
            $table->foreign('requested_rank_id')
                ->references('id')
                ->on('ranks')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rank_requests', function (Blueprint $table) {
            // 外部キー -> インデックス -> カラム の順で落とす
            $table->dropForeign(['requested_rank_id']);
            $table->dropIndex(['requested_rank_id']);
            $table->dropIndex(['requested_level']);

            $table->dropColumn(['requested_rank_id', 'requested_level', 'note']);
        });
    }
};
