<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('cadets', function (Blueprint $table): void { $table->foreignId('ward_id')->nullable()->after('unit_id')->constrained('wards')->nullOnDelete()->index(); });
        Schema::table('users', function (Blueprint $table): void { $table->foreignId('ward_id')->nullable()->after('cadet_id')->constrained('wards')->nullOnDelete()->index(); $table->foreignId('lga_id')->nullable()->after('ward_id')->constrained('lgas')->nullOnDelete()->index(); $table->foreignId('state_id')->nullable()->after('lga_id')->constrained('states')->nullOnDelete()->index(); });
    }
    public function down(): void { Schema::table('users', function (Blueprint $table): void { $table->dropForeign(['state_id']); $table->dropForeign(['lga_id']); $table->dropForeign(['ward_id']); $table->dropColumn(['state_id','lga_id','ward_id']); }); Schema::table('cadets', fn (Blueprint $table) => $table->dropForeign(['ward_id'])->dropColumn('ward_id')); }
};
