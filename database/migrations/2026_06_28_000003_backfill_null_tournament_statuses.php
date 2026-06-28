<?php

use App\Models\Tournament;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('tournaments')
            ->whereNull('status')
            ->update(['status' => Tournament::STATUS_RECRUITING]);
    }

    public function down(): void
    {
        // Keep historical status data as-is on rollback.
    }
};
