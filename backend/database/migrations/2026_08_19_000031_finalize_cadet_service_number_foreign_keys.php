<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasTable('cadets') && Schema::hasColumn('users', 'cadet_id')) {
            $foreignKeyExists = collect(Schema::getForeignKeys('users'))
                ->contains(fn (array $foreignKey): bool => in_array('cadet_id', $foreignKey['columns'] ?? [], true));

            if (! $foreignKeyExists) {
                Schema::table('users', function (Blueprint $table): void {
                    $table->foreign('cadet_id')
                        ->references('service_number')
                        ->on('cadets')
                        ->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'cadet_id')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropForeign(['cadet_id']);
            });
        }
    }
};
