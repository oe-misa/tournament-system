<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();

            // 級: F/E/D/C/B/A
            $table->string('kyu', 1);

            // 段: 無段はNULL、初段=1, 弐段=2, ... 十段=10
            $table->unsignedTinyInteger('dan')->nullable();

            // 比較用レベル: 無段=0, 初段=1, ... 十段=10
            $table->unsignedTinyInteger('level');

            $table->timestamps();

            // 同一級・同一段を重複させない
            $table->unique(['kyu', 'dan']);
            $table->index(['level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ranks');
    }
};
