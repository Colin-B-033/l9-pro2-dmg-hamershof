@extends('admin.layout')

@section('title', 'Winkels')

@section('content')
    <div class="admin-page-heading">
        <div>
            <h1>Winkels</h1>
            <p>Zichtbare winkels staan op de openbare pagina <a href="{{ route('stores') }}" target="_blank" rel="noopener">/winkels</a>.</p>
        </div>
        <a class="admin-button" href="{{ route('admin.stores.create') }}">Winkel toevoegen</a>
    </div>

    {{-- Aantallen over alle winkels; die veranderen niet door een zoekopdracht. --}}
    <dl class="admin-counts" aria-label="Aantallen winkels">
        <div><dt>Totaal</dt><dd>{{ $totalCount }}</dd></div>
        <div><dt>Zichtbaar</dt><dd>{{ $visibleCount }}</dd></div>
        <div><dt>Verborgen</dt><dd>{{ $hiddenCount }}</dd></div>
    </dl>

    {{-- Gewoon GET-formulier: de zoekterm komt in de adresbalk als ?zoek=... --}}
    <form class="admin-search" method="GET" action="{{ route('admin.stores.index') }}" role="search">
        <label for="zoek">Zoek op winkelnaam</label>
        <div class="admin-search__row">
            <input type="search" id="zoek" name="zoek" value="{{ $search }}" maxlength="100" placeholder="Bijvoorbeeld: bakker">
            <button type="submit" class="admin-button">Zoeken</button>
            @if ($search !== '')
                <a class="admin-button admin-button--secondary" href="{{ route('admin.stores.index') }}">Zoekopdracht wissen</a>
            @endif
        </div>
    </form>

    @if ($stores->isEmpty() && $search !== '')
        <div class="admin-empty">
            <h2>Geen winkels gevonden</h2>
            <p>Er is geen winkel met &ldquo;{{ $search }}&rdquo; in de naam.</p>
            <a class="admin-button admin-button--secondary" href="{{ route('admin.stores.index') }}">Toon alle winkels</a>
        </div>
    @elseif ($stores->isEmpty())
        <div class="admin-empty">
            <h2>Nog geen winkels</h2>
            <p>Voeg de eerste winkel toe. Hij verschijnt daarna direct op de website.</p>
            <a class="admin-button" href="{{ route('admin.stores.create') }}">Eerste winkel toevoegen</a>
        </div>
    @else
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">Naam</th>
                        <th scope="col">Categorie</th>
                        <th scope="col">Locatie</th>
                        <th scope="col">Op de website</th>
                        <th scope="col">Laatst gewijzigd</th>
                        <th scope="col"><span class="sr-only">Acties</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stores as $store)
                        <tr @class(['is-hidden' => ! $store->is_visible])>
                            <td><strong>{{ $store->name }}</strong></td>
                            <td>{{ $store->categoryLabel() }}</td>
                            <td>{{ $store->location ?: '—' }}</td>
                            <td>
                                @if ($store->is_visible)
                                    <span class="admin-status">Zichtbaar</span>
                                @else
                                    <span class="admin-status admin-status--hidden">Verborgen</span>
                                @endif
                                @if ($store->is_featured)
                                    <span class="admin-status admin-status--featured">Uitgelicht</span>
                                @endif
                            </td>
                            <td>{{ $store->updated_at->format('d-m-Y H:i') }}</td>
                            <td class="admin-table__actions">
                                <a class="admin-button admin-button--small admin-button--secondary" href="{{ route('admin.stores.edit', $store) }}">Bewerken</a>
                                <form method="POST" action="{{ route('admin.stores.destroy', $store) }}"
                                      onsubmit="return confirm('Weet je zeker dat je deze winkel wilt verwijderen? Dit kan niet ongedaan worden gemaakt.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-button admin-button--small admin-button--danger">
                                        Verwijderen<span class="sr-only"> {{ $store->name }}</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="admin-muted">
            @if ($search !== '')
                {{ $stores->count() }} {{ $stores->count() === 1 ? 'winkel' : 'winkels' }} gevonden voor &ldquo;{{ $search }}&rdquo;
            @else
                {{ $stores->count() }} {{ $stores->count() === 1 ? 'winkel' : 'winkels' }}
            @endif
        </p>
    @endif
@endsection
