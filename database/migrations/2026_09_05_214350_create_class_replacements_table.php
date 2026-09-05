<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_replacements', function (Blueprint $table) {
            $table->id();

            // Original permanent class
            $table->foreignId('assign_class_id')
                ->constrained('assign_class')
                ->cascadeOnDelete();

            // Teacher who normally teaches the class
            $table->foreignId('original_teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            // Teacher who will replace the original teacher
            $table->foreignId('replacement_teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            // Replacement is for a specific date
            $table->date('date');

            // Replacement class time
            $table->time('start_time');
            $table->time('end_time');

            // Optional reason
            $table->string('reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_replacements');
    }
};