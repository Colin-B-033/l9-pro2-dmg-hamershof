<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Evenementen die via het CMS worden beheerd en op /agenda verschijnen.
     * Losjes gebaseerd op de tabel `events` uit db.sql, maar eenvoudiger:
     * één evenement valt altijd binnen één dag.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->date('date');                       // alleen de dag, zonder tijd
            $table->time('start_time')->nullable();     // optioneel, bijv. 14:00
            $table->time('end_time')->nullable();       // optioneel, alleen met een begintijd
            $table->string('location', 150)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            // De agenda zoekt en sorteert vaak op datum.
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
