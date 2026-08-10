<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('membership_notifications',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->string('type');$t->unsignedInteger('fiscal_year');$t->timestamps();$t->unique(['user_id','type','fiscal_year']);});} public function down():void{Schema::dropIfExists('membership_notifications');}};
