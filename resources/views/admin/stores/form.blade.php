{{-- Eén formulier voor zowel toevoegen als bewerken. --}}
@extends('admin.layout')

@section('title', $store->exists ? 'Winkel bewerken' : 'Winkel toevoegen')

@section('content')
    <a class="admin-back" href="{{ route('admin.stores.index') }}">&larr; Terug naar winkels</a>

    <h1>{{ $store->exists ? 'Winkel bewerken' : 'Winkel toevoegen' }}</h1>

    @if ($errors->any())
        <div class="admin-alert admin-alert--error" role="alert">
            <strong>De winkel is niet opgeslagen.</strong> Controleer de gemarkeerde velden.
            Had je een nieuwe afbeelding gekozen? Selecteer die dan opnieuw; browsers onthouden een gekozen bestand niet.
        </div>
    @endif

    {{-- enctype is nodig om een bestand mee te sturen. --}}
    <form class="admin-form" method="POST" enctype="multipart/form-data"
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

        <div class="admin-field">
            <label for="opening_hours">Openingstijden</label>
            <input type="text" id="opening_hours" name="opening_hours" value="{{ old('opening_hours', $store->opening_hours) }}" maxlength="255"
                   placeholder="Ma–vr 09:00–18:00, za 09:00–17:00"
                   @error('opening_hours') aria-invalid="true" aria-describedby="opening_hours-error" @enderror>
            <p class="admin-hint">Gewone tekst, maximaal 255 tekens. Leeg laten als je niets wilt tonen.</p>
            @error('opening_hours') <p class="admin-error" id="opening_hours-error">{{ $message }}</p> @enderror
        </div>

        <div class="admin-field">
            <label for="image">Foto of logo</label>

            @if ($store->imageUrl())
                <figure class="admin-image-preview">
                    <img src="{{ $store->imageUrl() }}" alt="Huidige afbeelding van {{ $store->name }}">
                    <figcaption>Huidige afbeelding</figcaption>
                </figure>
            @elseif ($store->image_path)
                <p class="admin-hint">Het afbeeldingsbestand van deze winkel ontbreekt. Upload een nieuwe afbeelding.</p>
            @endif

            <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                   aria-describedby="image-hint @error('image') image-error @enderror"
                   @error('image') aria-invalid="true" @enderror>
            <p class="admin-hint" id="image-hint">
                JPG, PNG of WebP, maximaal 2 MB.
                @if ($store->image_path) Een nieuw bestand vervangt de huidige afbeelding. @endif
            </p>
            @error('image') <p class="admin-error" id="image-error">{{ $message }}</p> @enderror

            @if ($store->image_path)
                <div class="admin-checkbox">
                    <input type="hidden" name="remove_image" value="0">
                    <input type="checkbox" id="remove_image" name="remove_image" value="1" @checked(old('remove_image'))>
                    <label for="remove_image">Afbeelding verwijderen</label>
                </div>
                <p class="admin-hint">Kies je ook een nieuw bestand, dan wordt het nieuwe bestand gebruikt.</p>
            @endif
        </div>

        {{-- Het verborgen veld zorgt dat een uitgevinkte checkbox als "0" wordt verstuurd. --}}
        <fieldset class="admin-fieldset">
            <legend>Weergave</legend>

            <div class="admin-checkbox">
                <input type="hidden" name="is_visible" value="0">
                <input type="checkbox" id="is_visible" name="is_visible" value="1" @checked(old('is_visible', $store->is_visible))>
                <label for="is_visible">Zichtbaar op de website</label>
            </div>
            <p class="admin-hint">Uitgevinkt: de winkel blijft in het beheer staan, maar is niet te zien op /stores en de homepage.</p>

            <div class="admin-checkbox">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $store->is_featured))>
                <label for="is_featured">Uitlichten op de homepage</label>
            </div>
            <p class="admin-hint">De homepage toont maximaal drie zichtbare, uitgelichte winkels, op alfabetische volgorde.</p>
        </fieldset>

        <div class="admin-form__actions">
            <button type="submit" class="admin-button">{{ $store->exists ? 'Wijzigingen opslaan' : 'Winkel toevoegen' }}</button>
            <a class="admin-button admin-button--secondary" href="{{ route('admin.stores.index') }}">Annuleren</a>
        </div>
    </form>
@endsection
