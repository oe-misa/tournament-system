<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('note');
            $table->date('payment_confirmed_on')->nullable()->after('payment_confirmed_at');
        });
    }
    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'payment_confirmed_on']);
        });
    }
};
