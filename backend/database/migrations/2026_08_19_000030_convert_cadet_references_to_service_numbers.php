<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $tables = [
        'users',
        'cadet_course',
        'post_assignments',
        'warrants',
        'promotions',
        'demotions',
        'instructors',
        'unit_transfers',
        'id_card_renewal_applications',
        'ward_transfers',
        'personnel_documents',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('cadets')) {
            return;
        }

        // Current NACO schema already uses service_number as the Cadet primary key.
        // This branch exists only for databases created from an older integer-ID schema.
        if (! Schema::hasColumn('cadets', 'id')) {
            return;
        }

        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'cadet_id')) {
                continue;
            }

            $columnType = Schema::getColumnType($table, 'cadet_id');
            if (! in_array($columnType, ['integer', 'bigint', 'int', 'int4', 'int8'], true)) {
                continue;
            }

            Schema::table($table, function (Blueprint $schema): void {
                $schema->string('cadet_service_number')->nullable();
            });

            DB::table($table)->update([
                'cadet_service_number' => DB::raw(
                    '(SELECT service_number FROM cadets WHERE cadets.id = ' . $table . '.cadet_id)'
                ),
            ]);

            $unresolved = DB::table($table)
                ->whereNotNull('cadet_id')
                ->whereNull('cadet_service_number')
                ->exists();

            if ($unresolved) {
                throw new RuntimeException("Unable to map one or more {$table}.cadet_id values to cadets.service_number.");
            }

            Schema::table($table, function (Blueprint $schema): void {
                $schema->dropColumn('cadet_id');
            });

            Schema::table($table, function (Blueprint $schema): void {
                $schema->renameColumn('cadet_service_number', 'cadet_id');
            });
        }
    }

    public function down(): void
    {
        // The migration is intentionally irreversible because converting service numbers
        // back to historical integer IDs is unsafe after records have been created.
    }
};
