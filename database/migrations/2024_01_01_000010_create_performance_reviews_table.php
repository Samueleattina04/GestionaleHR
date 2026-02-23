<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('reviewer_id');
            $table->string('period');
            $table->integer('year');
            $table->integer('productivity_score')->nullable();
            $table->integer('quality_score')->nullable();
            $table->integer('teamwork_score')->nullable();
            $table->integer('initiative_score')->nullable();
            $table->integer('attendance_score')->nullable();
            $table->decimal('overall_score', 3, 1)->nullable();
            $table->text('strengths')->nullable();
            $table->text('improvements')->nullable();
            $table->text('goals')->nullable();
            $table->text('comments')->nullable();
            $table->enum('status', ['draft', 'submitted', 'acknowledged'])->default('draft');
            $table->timestamps();
            $table->foreign('reviewer_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('performance_reviews');
    }
};
