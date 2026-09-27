<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('attendance_sessions')) {
            Schema::create('attendance_sessions', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('assign_class_id');
                $table->unsignedBigInteger('teacher_id');
                $table->unsignedBigInteger('subject_id');

                $table->date('date');
                $table->dateTime('start_time');
                $table->dateTime('end_time');

                $table->enum('status', ['Open', 'Closed'])
                    ->default('Open');

                $table->timestamps();

                $table->foreign('assign_class_id')
                    ->references('id')
                    ->on('assign_class')
                    ->onDelete('cascade');

                $table->foreign('teacher_id')
                    ->references('id')
                    ->on('teachers')
                    ->onDelete('cascade');

                $table->foreign('subject_id')
                    ->references('id')
                    ->on('subjects')
                    ->onDelete('cascade');
            });

            return;
        }

        // The table already exists, so add only missing columns.
        if (! Schema::hasColumn('attendance_sessions', 'assign_class_id')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                $table->unsignedBigInteger('assign_class_id')->nullable();
            });
        }

        if (! Schema::hasColumn('attendance_sessions', 'teacher_id')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                $table->unsignedBigInteger('teacher_id')->nullable();
            });
        }

        if (! Schema::hasColumn('attendance_sessions', 'subject_id')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                $table->unsignedBigInteger('subject_id')->nullable();
            });
        }

        if (! Schema::hasColumn('attendance_sessions', 'date')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                $table->date('date')->nullable();
            });
        }

        if (! Schema::hasColumn('attendance_sessions', 'start_time')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                $table->dateTime('start_time')->nullable();
            });
        }

        if (! Schema::hasColumn('attendance_sessions', 'end_time')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                $table->dateTime('end_time')->nullable();
            });
        }

        if (! Schema::hasColumn('attendance_sessions', 'status')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                $table->enum('status', ['Open', 'Closed'])
                    ->default('Open');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
