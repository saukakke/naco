<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('states', function (Blueprint $table): void { $table->id(); $table->string('name')->unique(); $table->string('code')->nullable()->unique(); $table->timestamps(); });
        Schema::create('lgas', function (Blueprint $table): void { $table->id(); $table->foreignId('state_id')->constrained()->cascadeOnDelete(); $table->string('name'); $table->unique(['state_id','name']); $table->timestamps(); });
        Schema::create('wards', function (Blueprint $table): void { $table->id(); $table->foreignId('lga_id')->constrained()->cascadeOnDelete(); $table->string('name'); $table->string('code')->nullable(); $table->unique(['lga_id','name']); $table->timestamps(); });
    }
    public function down(): void { Schema::dropIfExists('wards'); Schema::dropIfExists('lgas'); Schema::dropIfExists('states'); }
};
