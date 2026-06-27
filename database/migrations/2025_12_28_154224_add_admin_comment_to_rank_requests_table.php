<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rank_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('rank_requests', 'admin_comment')) {
                $table->text('admin_comment')->nullable()->after('note');
            }
        });
    }

    public function down(): void
    {
    }
};
