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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users')->nullable();
            $table->unsignedBigInteger('recommend_by_id')->nullable();
            $table->foreign('recommend_by_id')->references('id')->on('users')->default(0)->nullable();
            $table->dateTime('recommend_date')->nullable();
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->foreign('approved_by_id')->references('id')->on('users')->default(0)->nullable();
            $table->dateTime('approved_date')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->foreign('type_id')->references('id')->on('program_types')->nullable();
            $table->unsignedBigInteger('disburse_amount')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->foreign('status_id')->references('id')->on('statuses')->nullable();
            $table->date('period')->nullable();
            $table->unsignedBigInteger('bank_panel')->nullable();
            $table->foreign('bank_panel')->references('id')->on('bank_panels')->nullable();
            $table->unsignedBigInteger('frequency_id')->nullable();
            $table->foreign('frequency_id')->references('id')->on('frequencies')->nullable();
            $table->string('payment_date')->nullable();
            $table->integer('total_month')->nullable();
            $table->integer('total_year')->nullable();
            $table->string('end_date')->nullable();
            $table->string('reason_to_reject')->nullable();
            $table->unsignedBigInteger('rejected_by_id')->nullable();
            $table->foreign('rejected_by_id')->references('id')->on('users')->default(0)->nullable();
            $table->dateTime('rejected_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
