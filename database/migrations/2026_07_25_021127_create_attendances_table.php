<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {

            $table->id();

            $table->unsignedTinyInteger('semester');

            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            $table->foreignId('assign_class_id')
                ->constrained('assign_class')
                ->cascadeOnDelete();

            $table->date('date');
            $table->time('time')->nullable();
            $table->enum('status', ['Present']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
