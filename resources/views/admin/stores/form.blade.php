{{-- Eén formulier voor zowel toevoegen als bewerken. --}}
@extends('admin.layout')

@section('title', $store->exists ? 'Winkel bewerken' : 'Winkel toevoegen')

@section('content')
    <a class="admin-back" href="{{ route('admin.stores.index') }}">&larr; Terug naar winkels</a>

    <h1>{{ $store->exists ? 'Winkel bewerken' : 'Winkel toevoegen' }}</h1>

    @if ($errors->any())
        <div class="admin-alert admin-alert--error" role="alert">
            <strong>De winkel is niet opgeslagen.</strong> Controleer de gemarkeerde velden.
        </div>
    @endif

    <form class="admin-form" method="POST"
          action="{{ $store->exists ? route('admin.stores.update', $store) : route('admin.stores.store') }}">
        @csrf
        @if ($store->exists)
            @method('PUT')
        @endif

        <div class="admin-field">
            <label for="name">Naam <span class="admin-required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $store->name) }}" maxlength="100" required
                   @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
            @error('name') <p class="admin-error" id="name-error">{{ $message }}</p> @enderror
        </div>

        <div class="admin-field">
            <label for="category">Categorie <span class="admin-required">*</span></label>
            <select id="category" name="category" required
                    @error('category') aria-invalid="true" aria-describedby="category-error" @enderror>
                <option value="">Kies een categorie</option>
                @foreach (\App\Models\Store::CATEGORIES as $value => $label)
                    <option value="{{ $value }}" @selected(old('category', $store->category) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('category') <p class="admin-error" id="category-error">{{ $message }}</p> @enderror
        </div>

        <div class="admin-field">
            <label for="description">Omschrijving</label>
            <textarea id="description" name="description" rows="4" maxlength="1000"
                      @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>{{ old('description', $store->description) }}</textarea>
            <p class="admin-hint">Korte tekst voor op de winkelkaart, maximaal 1000 tekens.</p>
            @error('description') <p class="admin-error" id="description-error">{{ $message }}</p> @enderror
        </div>

        <div class="admin-field">
            <label for="location">Locatie in de Hamershof</label>
            <input type="text" id="location" name="location" value="{{ old('location', $store->location) }}" maxlength="150"
                   placeholder="Bijvoorbeeld: begane grond, naast de hoofdingang"
                   @error('location') aria-invalid="true" aria-describedby="location-error" @enderror>
            @error('location') <p class="admin-error" id="location-error">{{ $message }}</p> @enderror
        </div>

        <div class="admin-field-row">
            <div class="admin-field">
                <label for="website">Website</label>
                <input type="url" id="website" name="website" value="{{ old('website', $store->website) }}"
                       placeholder="https://www.voorbeeld.nl"
                       @error('website') aria-invalid="true" aria-describedby="website-error" @enderror>
                @error('website') <p class="admin-error" id="website-error">{{ $message }}</p> @enderror
            </div>

            <div class="admin-field">
                <label for="phone">Telefoon</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $store->phone) }}" maxlength="30"
                       placeholder="033 123 45 67"
                       @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
                @error('phone') <p class="admin-error" id="phone-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="admin-button">{{ $store->exists ? 'Wijzigingen opslaan' : 'Winkel toevoegen' }}</button>
            <a class="admin-button admin-button--secondary" href="{{ route('admin.stores.index') }}">Annuleren</a>
        </div>
    </form>
@endsection
