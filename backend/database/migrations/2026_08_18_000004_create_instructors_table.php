<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('instructors',function(Blueprint $table):void{$table->id();$table->string('cadet_id')->unique();$table->foreign('cadet_id')->references('service_number')->on('cadets')->cascadeOnDelete();$table->string('status')->default('active')->index();$table->timestamps();});} public function down():void{Schema::dropIfExists('instructors');} };
