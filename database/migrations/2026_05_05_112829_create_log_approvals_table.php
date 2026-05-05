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
        Schema::create('log_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('log_id'); // O log original
            $table->unsignedBigInteger('user_id'); // Quem pediu a alteração
            $table->json('dados_novos'); // As horas novas que ele quer
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            // Apaga o pedido se o log original ou o user forem apagados
            $table->foreign('log_id')->references('id')->on('logs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_approvals');
    }
};
