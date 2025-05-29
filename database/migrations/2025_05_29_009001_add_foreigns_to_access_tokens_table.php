<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('access_tokens', function (Blueprint $table) {
            $table
                ->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('access_tokens', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
        });
    }
};
