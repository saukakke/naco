<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table): void {
            $table->id(); $table->string('code', 1)->unique(); $table->string('name'); $table->timestamps();
        });
        Schema::create('rank_categories', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->string('slug')->unique(); $table->unsignedInteger('order')->unique(); $table->timestamps();
        });
        Schema::create('ranks', function (Blueprint $table): void {
            $table->id(); $table->foreignId('rank_category_id')->constrained()->cascadeOnDelete(); $table->string('name'); $table->string('slug')->unique(); $table->unsignedInteger('order')->unique(); $table->timestamps();
        });
        Schema::create('cadets', function (Blueprint $table): void {
            $table->string('service_number')->primary(); $table->string('first_name'); $table->string('middle_name')->nullable(); $table->string('last_name'); $table->string('phone')->nullable(); $table->string('email')->nullable()->index(); $table->string('gender')->nullable(); $table->date('date_of_birth')->nullable(); $table->foreignId('unit_id')->constrained()->restrictOnDelete(); $table->foreignId('rank_id')->constrained()->restrictOnDelete(); $table->string('status')->default('active'); $table->timestamps();
        });
        Schema::create('courses', function (Blueprint $table): void {
            $table->id(); $table->string('code')->unique(); $table->string('name'); $table->text('description')->nullable(); $table->unsignedInteger('duration_days')->nullable(); $table->string('status')->default('active'); $table->timestamps();
        });
        Schema::create('cadet_course', function (Blueprint $table): void {
            $table->id(); $table->string('cadet_id'); $table->foreign('cadet_id')->references('service_number')->on('cadets')->cascadeOnDelete(); $table->foreignId('course_id')->constrained()->cascadeOnDelete(); $table->string('status')->default('enrolled'); $table->date('completed_at')->nullable(); $table->string('result')->nullable(); $table->timestamps(); $table->unique(['cadet_id','course_id']);
        });
        Schema::create('posts', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->string('slug')->unique(); $table->enum('level', ['national','state','lga','ward']); $table->text('description')->nullable(); $table->timestamps();
        });
        Schema::create('post_assignments', function (Blueprint $table): void {
            $table->id(); $table->string('cadet_id'); $table->foreign('cadet_id')->references('service_number')->on('cadets')->cascadeOnDelete(); $table->foreignId('post_id')->constrained()->cascadeOnDelete(); $table->date('start_date'); $table->date('end_date')->nullable(); $table->string('status')->default('active'); $table->string('reference')->nullable()->unique(); $table->timestamps();
        });
        Schema::create('warrants', function (Blueprint $table): void {
            $table->id(); $table->string('cadet_id'); $table->foreign('cadet_id')->references('service_number')->on('cadets')->cascadeOnDelete(); $table->foreignId('course_id')->constrained()->restrictOnDelete(); $table->string('warrant_number')->unique(); $table->string('type'); $table->date('issued_at'); $table->date('expires_at')->nullable(); $table->string('status')->default('valid'); $table->string('document_path')->nullable(); $table->timestamps();
        });
        Schema::create('promotions', function (Blueprint $table): void {
            $table->id(); $table->string('cadet_id'); $table->foreign('cadet_id')->references('service_number')->on('cadets')->cascadeOnDelete(); $table->foreignId('from_rank_id')->constrained('ranks')->restrictOnDelete(); $table->foreignId('to_rank_id')->constrained('ranks')->restrictOnDelete(); $table->date('promoted_at'); $table->text('reason')->nullable(); $table->string('reference')->unique(); $table->string('document_path')->nullable(); $table->string('status')->default('approved'); $table->timestamps();
        });
        Schema::create('demotions', function (Blueprint $table): void {
            $table->id(); $table->string('cadet_id'); $table->foreign('cadet_id')->references('service_number')->on('cadets')->cascadeOnDelete(); $table->foreignId('from_rank_id')->constrained('ranks')->restrictOnDelete(); $table->foreignId('to_rank_id')->constrained('ranks')->restrictOnDelete(); $table->date('demoted_at'); $table->text('reason')->nullable(); $table->string('reference')->unique(); $table->string('document_path')->nullable(); $table->string('status')->default('approved'); $table->timestamps();
        });
    }
    public function down(): void { foreach (['demotions','promotions','warrants','post_assignments','cadet_course','courses','cadets','ranks','rank_categories','units'] as $table) Schema::dropIfExists($table); }
};
