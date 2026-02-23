<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->unsignedBigInteger('author_id');
            $table->enum('target', ['all', 'department', 'role', 'individual']);
            $table->unsignedBigInteger('target_department_id')->nullable();
            $table->string('target_role')->nullable();
            $table->unsignedBigInteger('target_user_id')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->foreign('author_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('target_department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('target_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('communications');
    }
};
