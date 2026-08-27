<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const FOREIGN_KEY = 'users_cadet_id_foreign';
    private const UNIQUE = 'users_cadet_id_unique';

    public function up(): void
    {
        if (!Schema::hasColumn('users', 'cadet_id') || !Schema::hasTable('cadets')) {
            return;
        }

        if (DB::connection()->getDriverName() !== 'pgsql') {
            Schema::table('users', function (Blueprint $table): void {
                $table->foreign('cadet_id', self::FOREIGN_KEY)
                    ->references('service_number')->on('cadets')->nullOnDelete();
                $table->unique('cadet_id', self::UNIQUE);
            });
            return;
        }

        // Remove stale FK constraints attached specifically to users.cadet_id.
        // This makes a previously interrupted Railway deployment recoverable.
        $constraints = DB::select(<<<'SQL'
            SELECT con.conname
            FROM pg_constraint con
            JOIN pg_class rel ON rel.oid = con.conrelid
            WHERE rel.relname = 'users'
              AND con.contype = 'f'
              AND con.conkey @> ARRAY[(
                  SELECT attnum FROM pg_attribute
                  WHERE attrelid = rel.oid AND attname = 'cadet_id'
              )::smallint[]]
        SQL);

        foreach ($constraints as $constraint) {
            $name = (string) $constraint->conname;
            if ($name !== self::FOREIGN_KEY && preg_match('/^[A-Za-z0-9_]+$/', $name)) {
                DB::statement('ALTER TABLE "users" DROP CONSTRAINT IF EXISTS "' . $name . '"');
            }
        }

        $hasForeignKey = DB::selectOne(
            'SELECT 1 FROM pg_constraint WHERE conname = ? AND conrelid = ?::regclass LIMIT 1',
            [self::FOREIGN_KEY, 'users']
        ) !== null;

        $hasUnique = DB::selectOne(
            'SELECT 1 FROM pg_constraint WHERE conname = ? AND conrelid = ?::regclass LIMIT 1',
            [self::UNIQUE, 'users']
        ) !== null;

        Schema::table('users', function (Blueprint $table) use ($hasForeignKey, $hasUnique): void {
            if (!$hasForeignKey) {
                $table->foreign('cadet_id', self::FOREIGN_KEY)
                    ->references('service_number')->on('cadets')->nullOnDelete();
            }
            if (!$hasUnique) {
                $table->unique('cadet_id', self::UNIQUE);
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(self::UNIQUE);
            $table->dropForeign(self::FOREIGN_KEY);
        });
    }
};
