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
        Schema::create('tours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('travel_id'); // Define the UUID for travel_id
            $table->string('name');
            $table->date('starting_date');
            $table->date('ending_date');
            $table->integer('price');

            // Add the foreign key constraint
            $table->foreign('travel_id')
                  ->references('id')
                  ->on('travels');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
