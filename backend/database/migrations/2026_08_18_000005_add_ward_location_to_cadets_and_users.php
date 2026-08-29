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

        foreach ([
            'ward_id' => ['after' => 'cadet_id', 'table' => 'wards'],
            'lga_id' => ['after' => 'ward_id', 'table' => 'lgas'],
            'state_id' => ['after' => 'lga_id', 'table' => 'states'],
        ] as $column => $config) {
            if (! Schema::hasColumn('users', $column)) {
                Schema::table('users', function (Blueprint $table) use ($column, $config): void {
                    $table->foreignId($column)->nullable()->after($config['after']);
                });
            }

            $foreignKeyExists = collect(Schema::getForeignKeys('users'))
                ->contains(fn (array $foreignKey): bool => in_array($column, $foreignKey['columns'] ?? [], true));

            if (! $foreignKeyExists) {
                Schema::table('users', function (Blueprint $table) use ($column, $config): void {
                    $table->foreign($column)
                        ->references('id')
                        ->on($config['table'])
                        ->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['state_id', 'lga_id', 'ward_id'] as $column) {
            if (Schema::hasColumn('users', $column)) {
                $foreignKeyExists = collect(Schema::getForeignKeys('users'))
                    ->contains(fn (array $foreignKey): bool => in_array($column, $foreignKey['columns'] ?? [], true));

                if ($foreignKeyExists) {
                    Schema::table('users', function (Blueprint $table) use ($column): void {
                        $table->dropForeign([$column]);
                    });
                }

                Schema::table('users', function (Blueprint $table) use ($column): void {
                    $table->dropColumn($column);
                });
            }
        }

        if (Schema::hasColumn('cadets', 'ward_id')) {
            $foreignKeyExists = collect(Schema::getForeignKeys('cadets'))
                ->contains(fn (array $foreignKey): bool => in_array('ward_id', $foreignKey['columns'] ?? [], true));

            if ($foreignKeyExists) {
                Schema::table('cadets', function (Blueprint $table): void {
                    $table->dropForeign('cadets_ward_id_foreign');
                });
            }

            Schema::table('cadets', function (Blueprint $table): void {
                $table->dropColumn('ward_id');
            });
        }
    }
};
