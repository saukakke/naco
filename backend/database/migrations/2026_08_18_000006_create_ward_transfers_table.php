<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('ward_transfers', function(Blueprint $table):void{
            $table->id(); $table->foreignId('cadet_id')->constrained()->cascadeOnDelete(); $table->foreignId('from_ward_id')->constrained('wards'); $table->foreignId('to_ward_id')->constrained('wards');
            $table->string('reference')->unique(); $table->string('status')->default('pending_source_hcs')->index(); $table->text('reason')->nullable();
            $table->timestamp('source_hcs_released_at')->nullable(); $table->foreignId('source_hcs_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('source_lga_acknowledged_at')->nullable(); $table->foreignId('source_lga_acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('source_state_acknowledged_at')->nullable(); $table->foreignId('source_state_acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('destination_hcs_accepted_at')->nullable(); $table->foreignId('destination_hcs_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('destination_lga_acknowledged_at')->nullable(); $table->foreignId('destination_lga_acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('destination_state_acknowledged_at')->nullable(); $table->foreignId('destination_state_acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('national_approved_at')->nullable(); $table->foreignId('national_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable(); $table->timestamps();
        });
    }
    public function down():void{Schema::dropIfExists('ward_transfers');}
};
