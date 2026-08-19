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
        if (! Schema::hasTable('cadets') || ! Schema::hasColumn('cadets', 'id')) {
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

            $foreignKeys = Schema::getForeignKeys($table);
            foreach ($foreignKeys as $foreignKey) {
                if (in_array('cadet_id', $foreignKey['columns'] ?? [], true)) {
                    Schema::table($table, function (Blueprint $schema): void {
                        $schema->dropForeign(['cadet_id']);
                    });
                    break;
                }
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
                $schema->foreign('cadet_id')
                    ->references('service_number')
                    ->on('cadets')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Service-number references are intentionally not converted back to historical IDs.
    }
};
