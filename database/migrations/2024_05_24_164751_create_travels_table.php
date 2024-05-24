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
        Schema::create('travels', function (Blueprint $table) { // the travel is irregular word (it doesn't accept the plural form), so we will modify the table name from 'travel' to 'travels' and the migration name as well, and we have to specify the table name in the Travel model.
            $table->uuid('id')->primary();
            $table->boolean('is_public')->default(false);
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description');
            $table->unsignedInteger('number_of_days');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel');
    }
};
