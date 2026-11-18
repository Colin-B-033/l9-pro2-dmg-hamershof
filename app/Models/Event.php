<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Een evenement in de agenda (/agenda), beheerd via het CMS (/admin/evenementen).
 * Let op: importeer altijd App\Models\Event; zonder import bedoelt Laravel de Event-facade.
 */
class Event extends Model
{
    protected $fillable = [
        'title',
        'date',
        'start_time',
        'end_time',
        'location',
        'description',
        'is_visible',
    ];

    /**
     * Standaardwaarden voor een nieuw evenement (bijvoorbeeld in het lege formulier).
     */
    protected $attributes = [
        'is_visible' => true,
    ];

    protected function casts(): array
    {
        return [
            // Alleen een datum, als Y-m-d. Er zit geen tijd of tijdzone in, dus de dag kan niet verschuiven.
            'date' => 'date:Y-m-d',
            'is_visible' => 'boolean',
        ];
    }

    /**
     * Tijden altijd als "14:00" teruggeven. Sommige databases geven "14:00:00" terug.
     */
    protected function startTime(): Attribute
    {
        return Attribute::get(fn (?string $value) => $value ? substr($value, 0, 5) : null);
    }

    protected function endTime(): Attribute
    {
        return Attribute::get(fn (?string $value) => $value ? substr($value, 0, 5) : null);
    }

    /**
     * Alleen evenementen die in de openbare agenda getoond mogen worden.
     * Gebruik: Event::visible()->get()
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Sorteert op datum en daarna op begintijd (evenementen zonder tijd eerst).
     */
    public function scopeInAgendaOrder($query)
    {
        return $query->orderBy('date')->orderBy('start_time')->orderBy('title');
    }

    /**
     * Leesbare tijd, bijvoorbeeld "14:00 – 16:00" of "14:00". Zonder begintijd: null.
     */
    public function timeLabel(): ?string
    {
        if (! $this->start_time) {
            return null;
        }

        return $this->end_time ? "{$this->start_time} – {$this->end_time}" : $this->start_time;
    }

    /**
     * Alleen de gegevens die de openbare agenda nodig heeft (geen tijdstempels of interne velden).
     * De datum is een gewone tekst "2026-11-18", zodat JavaScript hem niet als UTC-tijd leest.
     */
    public function toAgendaArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'date' => $this->date->format('Y-m-d'),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'location' => $this->location,
            'description' => $this->description,
        ];
    }
}
