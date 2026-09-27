<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If the table does not exist, create it normally.
        if (!Schema::hasTable('assign_class')) {
            Schema::create('assign_class', function (Blueprint $table) {
                $table->id();

                $table->foreignId('teacher_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->unsignedInteger('semester');

                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();

                $table->timestamps();
            });

            return;
        }

        // If the table already exists, add only the missing columns.
        Schema::table('assign_class', function (Blueprint $table) {
            if (!Schema::hasColumn('assign_class', 'start_time')) {
                $table->time('start_time')->nullable()->after('semester');
            }

            if (!Schema::hasColumn('assign_class', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assign_class');
    }
};