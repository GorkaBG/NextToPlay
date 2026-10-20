<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\GameUser;
use App\Models\Platform;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_user_platform', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(GameUser::class)->constrained('game_users')->cascadeOnDelete();
            $table->foreignIdFor(Platform::class)->constrained('platforms')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_user_platform');
    }
};
