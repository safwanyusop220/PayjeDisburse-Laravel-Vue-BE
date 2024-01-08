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
        Schema::create('recipient_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->foreign('recipient_id')->references('id')->on('receipients');
            $table->unsignedBigInteger('program_id')->nullable();
            $table->foreign('program_id')->references('id')->on('programs');
            $table->unsignedBigInteger('status_id')->default(1);
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->string('reason_to_reject')->default('-');
            $table->unsignedBigInteger('created_by_id');
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->unsignedBigInteger('recommend_by_id')->nullable();
            $table->foreign('recommend_by_id')->references('id')->on('users')->default(0)->nullable();
            $table->dateTime('recommend_date')->nullable();
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->foreign('approved_by_id')->references('id')->on('users')->default(0)->nullable();
            $table->dateTime('approved_date')->nullable();
            $table->unsignedBigInteger('rejected_by_id')->nullable();
            $table->foreign('rejected_by_id')->references('id')->on('users')->default(0)->nullable();
            $table->dateTime('rejected_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipient_programs');
    }
};
