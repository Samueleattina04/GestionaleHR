<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('training_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('provider')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('max_participants')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('training_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['enrolled', 'completed', 'failed', 'cancelled'])->default('enrolled');
            $table->integer('score')->nullable();
            $table->date('completion_date')->nullable();
            $table->string('certificate_path')->nullable();
            $table->timestamps();
            $table->unique(['training_course_id', 'user_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('training_participants');
        Schema::dropIfExists('training_courses');
    }
};
