<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::table('courses',function(Blueprint $table):void{$table->decimal('fee',12,2)->default(0)->after('status');$table->boolean('is_instructor_course')->default(false)->after('fee');});} public function down():void{Schema::table('courses',function(Blueprint $table):void{$table->dropColumn(['fee','is_instructor_course']);});} };
