<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_replacements', function (Blueprint $table) {

            // Remove old fields
            $table->dropForeign(['original_teacher_id']);
            $table->dropColumn([
                'original_teacher_id',
                'reason',
            ]);

            // assign_class_id is optional because
            // an unassigned subject may not have an assign_class record.
            $table->foreignId('assign_class_id')
                ->nullable()
                ->change();

            // Store the actual subject being replaced.
            $table->foreignId('subject_id')
                ->after('assign_class_id')
                ->constrained('subjects')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('class_replacements', function (Blueprint $table) {

            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');

            $table->foreignId('original_teacher_id')
                ->nullable()
                ->after('assign_class_id');

            $table->string('reason')->nullable();

            $table->foreign('original_teacher_id')
                ->references('id')
                ->on('teachers')
                ->cascadeOnDelete();
        });
    }
};
