<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimClaimCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claim_claim_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained()->onDelete('cascade');
            $table->foreignId('claim_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Índice único para evitar duplicados
            $table->unique(['claim_id', 'claim_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claim_claim_category');
    }
}