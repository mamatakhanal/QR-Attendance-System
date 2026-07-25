<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->foreignId('assign_class_id')
                ->after('teacher_id')
                ->constrained('assign_class')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('attendance', function (Blueprint $table) {
        $table->dropForeign(['assign_class_id']);
        $table->dropColumn('assign_class_id');
    });
    }
};
