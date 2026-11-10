<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('igdb_id');
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        DB::table('platforms')->insert([
            ['name' => 'Steam', 'igdb_id' => '6', 'icon' => 'si-steam'],
            ['name' => 'Epic', 'igdb_id' => '6', 'icon' => 'si-epicgames'],
            ['name' => 'GOG', 'igdb_id' => '6', 'icon' => 'si-gogdotcom'],
            ['name' => 'Xbox Game Pass', 'igdb_id' => '6', 'icon' => 'ico-10-gamepass'],
            ['name' => 'CD / DVD', 'igdb_id' => '6', 'icon' => 'mdi-microsoft-windows'],
            ['name' => 'Battle.net', 'igdb_id' => '6', 'icon' => 'si-battledotnet'],
            ['name' => 'GeForce Now', 'igdb_id' => '6', 'icon' => 'si-nvidia'],
            ['name' => 'EA App', 'igdb_id' => '6', 'icon' => 'si-ea'],
            ['name' => 'Ubisoft Connect', 'igdb_id' => '6', 'icon' => 'si-ubisoft'],
            ['name' => 'Rockstar Launcher', 'igdb_id' => '6', 'icon' => 'si-rockstargames'],
            ['name' => 'Itch.io', 'igdb_id' => '6', 'icon' => 'si-itchdotio'],
            ['name' => 'Microsoft Store', 'igdb_id' => '6', 'icon' => 'mdi-microsoft'],
            ['name' => 'Amazon Luna', 'igdb_id' => '6', 'icon' => ''],
            ['name' => 'Emulador', 'igdb_id' => '6', 'icon' => 'si-retropie'],

            ['name' => 'PlayStation', 'igdb_id' => '7', 'icon' => 'si-playstation'],
            ['name' => 'PlayStation 2', 'igdb_id' => '8', 'icon' => 'si-playstation2'],
            ['name' => 'PlayStation 3', 'igdb_id' => '9', 'icon' => 'si-playstation3'],
            ['name' => 'PlayStation 4', 'igdb_id' => '48', 'icon' => 'si-playstation4'],
            ['name' => 'PlayStation 5', 'igdb_id' => '167', 'icon' => 'si-playstation5'],
            ['name' => 'PSP', 'igdb_id' => '38', 'icon' => 'si-playstationportable'],
            ['name' => 'PS Vita', 'igdb_id' => '46', 'icon' => 'si-playstationvita'],

            ['name' => 'Switch', 'igdb_id' => '130', 'icon' => 'mdi-nintendo-switch'],
            ['name' => 'Switch 2', 'igdb_id' => '508', 'icon' => 'ico-05-switch2'],
            ['name' => 'Wii', 'igdb_id' => '5', 'icon' => 'mdi-nintendo-wii'],
            ['name' => 'Wii U', 'igdb_id' => '41', 'icon' => 'nintendo-wiiu'],
            ['name' => '3DS', 'igdb_id' => '37', 'icon' => ''],

            ['name' => 'Xbox', 'igdb_id' => '11', 'icon' => 'mdi-microsoft-xbox'],
            ['name' => 'Xbox 360', 'igdb_id' => '12', 'icon' => 'mdi-microsoft-xbox'],
            ['name' => 'Xbox One', 'igdb_id' => '49', 'icon' => 'mdi-microsoft-xbox'],
            ['name' => 'Xbox Series X|S', 'igdb_id' => '169', 'icon' => 'mdi-microsoft-xbox'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platforms');
    }
};
