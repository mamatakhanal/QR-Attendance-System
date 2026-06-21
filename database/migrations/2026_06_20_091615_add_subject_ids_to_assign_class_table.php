<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assign_class', function (Blueprint $table) {
            $table->json('subject_ids')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('assign_class', function (Blueprint $table) {
            //
        });
    }
};
