<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rank_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('rank_requests', 'requested_at')) {
                $table->dateTime('requested_at')->nullable()->after('status');
                $table->index('requested_at');
            }

            if (!Schema::hasColumn('rank_requests', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('note');
                $table->index('approved_by');
            }
            if (!Schema::hasColumn('rank_requests', 'approved_at')) {
                $table->dateTime('approved_at')->nullable()->after('approved_by');
                $table->index('approved_at');
            }

            if (!Schema::hasColumn('rank_requests', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_at');
                $table->index('rejected_by');
            }
            if (!Schema::hasColumn('rank_requests', 'rejected_at')) {
                $table->dateTime('rejected_at')->nullable()->after('rejected_by');
                $table->index('rejected_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rank_requests', function (Blueprint $table) {
            // down は「存在するなら落とす」が丁寧だけど、hasColumn は Blueprint 内で使いにくいので安全策で固定
            // プロジェクト方針で down を使わないならこのままでOKです。

            // ここは必要なら手動で調整してください
        });
    }
};
