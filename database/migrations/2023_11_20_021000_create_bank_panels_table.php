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
        Schema::create('bank_panels', function (Blueprint $table) {
            $table->id();
            $table->string('holder_name');
            $table->unsignedBigInteger('bank_id');
            $table->foreign('bank_id')->references('id')->on('ref_banks');
            $table->unsignedBigInteger('account_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_panels');
    }
};
