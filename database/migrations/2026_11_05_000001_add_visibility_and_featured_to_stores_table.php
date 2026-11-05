<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * is_visible: staat de winkel op de website? Bestaande winkels blijven zichtbaar (default true).
     * is_featured: uitgelicht op de homepage? Standaard niet.
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->boolean('is_visible')->default(true)->after('phone');
            $table->boolean('is_featured')->default(false)->after('is_visible');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['is_visible', 'is_featured']);
        });
    }
};
