<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('rank_requests')->where('status', '0')->update(['status' => 'pending']);
        DB::table('rank_requests')->where('status', '1')->update(['status' => 'approved']);
        DB::table('rank_requests')->where('status', '2')->update(['status' => 'rejected']);
    }

    public function down(): void
    {
        DB::table('rank_requests')->where('status', 'pending')->update(['status' => '0']);
        DB::table('rank_requests')->where('status', 'approved')->update(['status' => '1']);
        DB::table('rank_requests')->where('status', 'rejected')->update(['status' => '2']);
    }
};
