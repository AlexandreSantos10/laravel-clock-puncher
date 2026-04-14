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
        Schema::table('logs', function (Blueprint $table) {
                $table->integer('user_id');
                $table->date('data');
                $table->datetime('entrada');
                $table->datetime('final_almoço');
                $table->datetime('saida');
                $table->integer('total_horas');
                $table->string('obs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            
        });
    }
};
