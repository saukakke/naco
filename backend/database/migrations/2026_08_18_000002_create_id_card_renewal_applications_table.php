<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('id_card_renewal_applications',function(Blueprint $table):void{$table->id();$table->string('cadet_id');$table->foreign('cadet_id')->references('service_number')->on('cadets')->cascadeOnDelete();$table->string('reference')->unique();$table->date('current_card_expires_at');$table->string('status')->default('pending')->index();$table->text('reason')->nullable();$table->string('payment_reference')->nullable()->unique();$table->timestamp('payment_verified_at')->nullable();$table->timestamp('approved_at')->nullable();$table->timestamp('issued_at')->nullable();$table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();$table->timestamps();});} public function down():void{Schema::dropIfExists('id_card_renewal_applications');} };
