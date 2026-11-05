<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * image_path: pad van de geüploade afbeelding op de 'public'-disk, bijv. "stores/abc123.jpg".
     * opening_hours: gewone tekst, bijv. "Ma–vr 09:00–18:00, za 09:00–17:00".
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('phone');
            $table->string('opening_hours', 255)->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'opening_hours']);
        });
    }
};
