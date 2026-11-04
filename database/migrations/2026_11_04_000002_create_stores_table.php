<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Winkels die via het CMS worden beheerd en op /winkels verschijnen.
     * Gebaseerd op de tabel `winkels` uit db.sql, maar met Laravel-naamgeving.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('category', 30); // sleutel uit Store::CATEGORIES
            $table->text('description')->nullable();
            $table->string('location', 150)->nullable();
            $table->string('website')->nullable();
            $table->string('phone', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
