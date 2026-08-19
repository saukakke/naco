<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $tables = [
        'users','cadet_course','post_assignments','warrants','promotions','demotions','instructors',
        'unit_transfers','id_card_renewal_applications','ward_transfers','personnel_documents',
    ];

    public function up(): void
    {
        if (!Schema::hasTable('cadets') || !Schema::hasColumn('cadets', 'id')) {
            return;
        }

        DB::statement('PRAGMA foreign_keys = OFF');
        try {
            foreach ($this->tables as $table) {
                if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'cadet_id')) {
                    continue;
                }

                Schema::table($table, function (Blueprint $schema) use ($table): void {
                    $schema->string('cadet_service_number')->nullable();
                });

                DB::statement("UPDATE {$table} SET cadet_service_number = (SELECT service_number FROM cadets WHERE cadets.id = {$table}.cadet_id)");

                DB::table($table)->whereNotNull('cadet_id')->whereNull('cadet_service_number')->exists();

                Schema::table($table, function (Blueprint $schema): void {
                    $schema->dropColumn('cadet_id');
                });
                Schema::table($table, function (Blueprint $schema): void {
                    $schema->renameColumn('cadet_service_number', 'cadet_id');
                });
            }

            foreach ($this->tables as $table) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, 'cadet_id')) {
                    Schema::table($table, function (Blueprint $schema): void {
                        $schema->foreign('cadet_id')->references('service_number')->on('cadets')->cascadeOnDelete();
                    });
                }
            }
        } finally {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    public function down(): void
    {
        // Existing service-number references are intentionally retained. Reverting them
        // safely would require reconstructing the previous integer FK values.
    }
};
