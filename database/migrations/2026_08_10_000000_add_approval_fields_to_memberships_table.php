<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            // Existing history was created by the former immediate-renewal flow.
            $table->string('status', 30)->default('approved')->after('note');
            $table->foreignId('payment_confirmed_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->dateTime('payment_confirmed_at')->nullable()->after('payment_confirmed_by');
            $table->foreignId('approved_by')->nullable()->after('payment_confirmed_at')->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable()->after('approved_by');
            $table->foreignId('rejected_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->dateTime('rejected_at')->nullable()->after('rejected_by');
            $table->text('admin_comment')->nullable()->after('rejected_at');

            $table->index(['status', 'created_at']);
            $table->unique(['user_id', 'start_date', 'end_date'], 'memberships_user_period_unique');
        });
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropUnique('memberships_user_period_unique');
            $table->dropIndex(['status', 'created_at']);
            $table->dropConstrainedForeignId('payment_confirmed_by');
            $table->dropConstrainedForeignId('approved_by');
            $table->dropConstrainedForeignId('rejected_by');
            $table->dropColumn(['status', 'payment_confirmed_at', 'approved_at', 'rejected_at', 'admin_comment']);
        });
    }
};
