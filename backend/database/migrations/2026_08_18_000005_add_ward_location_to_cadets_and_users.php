<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cadets', function (Blueprint $table): void {
            if (! Schema::hasColumn('cadets', 'ward_id')) {
                $table->foreignId('ward_id')->nullable()->after('unit_id');
            }
        });

        $cadetWardForeignKeyExists = collect(Schema::getForeignKeys('cadets'))
            ->contains(fn (array $foreignKey): bool => in_array('ward_id', $foreignKey['columns'] ?? [], true));

        if (! $cadetWardForeignKeyExists) {
            Schema::table('cadets', function (Blueprint $table): void {
                $table->foreign('ward_id', 'cadets_ward_id_foreign')
                    ->references('id')
                    ->on('wards')
                    ->nullOnDelete();
            });
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('ward_id')->nullable()->after('cadet_id')->constrained('wards')->nullOnDelete();
            $table->foreignId('lga_id')->nullable()->after('ward_id')->constrained('lgas')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->after('lga_id')->constrained('states')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['lga_id']);
            $table->dropForeign(['ward_id']);
            $table->dropColumn(['state_id', 'lga_id', 'ward_id']);
        });

        Schema::table('cadets', function (Blueprint $table): void {
            $table->dropForeign('cadets_ward_id_foreign');
            $table->dropColumn('ward_id');
        });
    }
};
