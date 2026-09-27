<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add the column if it does not already exist.
        if (!Schema::hasColumn('attendance', 'assign_class_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->unsignedBigInteger('assign_class_id')->nullable()->after('subject_id');
            });
        }

        // Add the foreign key.
        Schema::table('attendance', function (Blueprint $table) {
            $table->foreign('assign_class_id')
                ->references('id')
                ->on('assign_class')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['assign_class_id']);
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn('assign_class_id');
        });
    }
};