{{-- Eén formulier voor zowel toevoegen als bewerken. --}}
@extends('admin.layout')

@section('title', $event->exists ? 'Evenement bewerken' : 'Evenement toevoegen')

@section('content')
    <a class="admin-back" href="{{ route('admin.events.index') }}">&larr; Terug naar de agenda</a>

    <h1>{{ $event->exists ? 'Evenement bewerken' : 'Evenement toevoegen' }}</h1>

    @if ($errors->any())
        <div class="admin-alert admin-alert--error" role="alert">
            <strong>Het evenement is niet opgeslagen.</strong> Controleer de gemarkeerde velden.
        </div>
    @endif

    <form class="admin-form" method="POST"
          action="{{ $event->exists ? route('admin.events.update', $event) : route('admin.events.store') }}">
        @csrf
        @if ($event->exists)
            @method('PUT')
        @endif

        <div class="admin-field">
            <label for="title">Titel <span class="admin-required">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title', $event->title) }}" maxlength="100" required
                   @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>
            @error('title') <p class="admin-error" id="title-error">{{ $message }}</p> @enderror
        </div>

        <div class="admin-field">
            <label for="date">Datum <span class="admin-required">*</span></label>
            {{-- Een datumveld stuurt de datum altijd als 2026-11-18, ongeacht hoe de browser hem toont. --}}
            <input type="date" id="date" name="date" value="{{ old('date', $event->date?->format('Y-m-d')) }}" required
                   @error('date') aria-invalid="true" aria-describedby="date-error" @enderror>
            @error('date') <p class="admin-error" id="date-error">{{ $message }}</p> @enderror
        </div>

        <div class="admin-field-row">
            <div class="admin-field">
                <label for="start_time">Begintijd</label>
                <input type="time" id="start_time" name="start_time" value="{{ old('start_time', $event->start_time) }}"
                       aria-describedby="start_time-hint @error('start_time') start_time-error @enderror"
                       @error('start_time') aria-invalid="true" @enderror>
                <p class="admin-hint" id="start_time-hint">Leeg laten als er geen vaste tijd is.</p>
                @error('start_time') <p class="admin-error" id="start_time-error">{{ $message }}</p> @enderror
            </div>

            <div class="admin-field">
                <label for="end_time">Eindtijd</label>
                <input type="time" id="end_time" name="end_time" value="{{ old('end_time', $event->end_time) }}"
                       aria-describedby="end_time-hint @error('end_time') end_time-error @enderror"
                       @error('end_time') aria-invalid="true" @enderror>
                <p class="admin-hint" id="end_time-hint">Alleen met een begintijd, en later op dezelfde dag.</p>
                @error('end_time') <p class="admin-error" id="end_time-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="admin-field">
            <label for="location">Locatie</label>
            <input type="text" id="location" name="location" value="{{ old('location', $event->location) }}" maxlength="150"
                   placeholder="Bijvoorbeeld: Centrale plaza"
                   @error('location') aria-invalid="true" aria-describedby="location-error" @enderror>
            @error('location') <p class="admin-error" id="location-error">{{ $message }}</p> @enderror
        </div>

        <div class="admin-field">
            <label for="description">Omschrijving</label>
            <textarea id="description" name="description" rows="4" maxlength="1000"
                      aria-describedby="description-hint @error('description') description-error @enderror"
                      @error('description') aria-invalid="true" @enderror>{{ old('description', $event->description) }}</textarea>
            <p class="admin-hint" id="description-hint">Korte tekst voor in de lijstweergave van de agenda, maximaal 1000 tekens.</p>
            @error('description') <p class="admin-error" id="description-error">{{ $message }}</p> @enderror
        </div>

        {{-- Het verborgen veld zorgt dat een uitgevinkte checkbox als "0" wordt verstuurd. --}}
        <fieldset class="admin-fieldset">
            <legend>Weergave</legend>

            <div class="admin-checkbox">
                <input type="hidden" name="is_visible" value="0">
                <input type="checkbox" id="is_visible" name="is_visible" value="1" @checked(old('is_visible', $event->is_visible))>
                <label for="is_visible">Zichtbaar in de agenda</label>
            </div>
            <p class="admin-hint">Uitgevinkt: het evenement blijft in het beheer staan, maar is niet te zien op de website.</p>
        </fieldset>

        <div class="admin-form__actions">
            <button type="submit" class="admin-button">{{ $event->exists ? 'Wijzigingen opslaan' : 'Evenement toevoegen' }}</button>
            <a class="admin-button admin-button--secondary" href="{{ route('admin.events.index') }}">Annuleren</a>
        </div>
    </form>
@endsection
