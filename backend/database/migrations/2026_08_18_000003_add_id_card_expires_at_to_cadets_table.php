<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('cadets', function (Blueprint $table): void { $table->date('id_card_expires_at')->nullable()->after('status')->index(); }); }
    public function down(): void { Schema::table('cadets', fn (Blueprint $table) => $table->dropColumn('id_card_expires_at')); }
};
