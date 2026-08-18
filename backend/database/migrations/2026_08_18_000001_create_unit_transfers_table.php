<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unit_transfers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cadet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_unit_id')->constrained('units');
            $table->foreignId('to_unit_id')->constrained('units');
            $table->string('reason')->nullable();
            $table->string('status')->default('pending_release')->index();
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('payment_verified_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('released_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('accepted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_reference')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('unit_transfers'); }
};
