<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreign('cadet_id', 'users_cadet_id_foreign')
                ->references('service_number')
                ->on('cadets')
                ->nullOnDelete();

            $table->unique('cadet_id', 'users_cadet_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique('users_cadet_id_unique');
            $table->dropForeign('users_cadet_id_foreign');
        });
    }
};
