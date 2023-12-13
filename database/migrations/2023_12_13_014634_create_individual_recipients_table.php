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
        Schema::create('individual_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipient_id');
            $table->foreign('recipient_id')->references('id')->on('receipients');
            $table->unsignedBigInteger('program_id');
            $table->foreign('program_id')->references('id')->on('programs');
            $table->unsignedBigInteger('disburse_amount')->nullable();
            $table->unsignedBigInteger('frequency_id')->nullable();
            $table->foreign('frequency_id')->references('id')->on('frequencies')->nullable();
            $table->string('payment_date')->nullable(); 
            $table->integer('total_month')->nullable();
            $table->integer('total_year')->nullable();
            $table->string('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individual_recipients');
    }
};
