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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->integer('igdb_id')->unique();
            $table->string('name');
            $table->string('cover')->nullable();
            $table->timestamp('release_date')->nullable();            
            $table->decimal('total_rating', total: 4, places: 1)->nullable();
            $table->integer('total_rating_count')->nullable();
            //la relación con desarrolldores será N:M
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
