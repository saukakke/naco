<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{Schema::create('report_periods',function(Blueprint $t){$t->id();$t->unsignedSmallInteger('year');$t->unsignedTinyInteger('period');$t->date('starts_on');$t->date('ends_on');$t->date('due_on');$t->string('status')->default('open');$t->timestamps();$t->unique(['year','period']);});}public function down():void{Schema::dropIfExists('report_periods');}};
