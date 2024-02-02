<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('associacao_secretario', function (Blueprint $table) {
            $table->id();

            $table->foreignId('associacao_id')->constrained('associacoes')->cascadeOnDelete();
            $table->foreignId('secretario_id')->constrained('users')->restrictOnDelete();
            $table->unique(['associacao_id', 'secretario_id']);

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('associacao_secretario');
    }
};
