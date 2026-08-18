<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::table('courses')->update(['is_instructor_course' => true]);
        DB::table('warrants')->where('status', 'valid')->update(['status' => 'active']);
        Schema::table('warrants', function (Blueprint $table): void {
            $table->index(['cadet_id', 'course_id', 'status', 'expires_at'], 'warrants_validity_lookup');
        });
    }

    public function down(): void
    {
        Schema::table('warrants', function (Blueprint $table): void {
            $table->dropIndex('warrants_validity_lookup');
        });
    }
};
