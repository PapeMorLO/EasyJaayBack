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
        //
        DB::statement('ALTER TABLE `produits` MODIFY COLUMN `color` JSON NULL');
        DB::statement('ALTER TABLE `produits` MODIFY COLUMN `taille_dimension` JSON NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::statement('ALTER TABLE `produits` MODIFY COLUMN `color` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `produits` MODIFY COLUMN `taille_dimension` VARCHAR(255) NULL');
    }
};
