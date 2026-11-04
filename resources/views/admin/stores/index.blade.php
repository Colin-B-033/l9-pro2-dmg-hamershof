@extends('admin.layout')

@section('title', 'Winkels')

@section('content')
    <div class="admin-page-heading">
        <div>
            <h1>Winkels</h1>
            <p>Deze winkels staan op de openbare pagina <a href="{{ route('stores') }}" target="_blank" rel="noopener">/winkels</a>.</p>
        </div>
        <a class="admin-button" href="{{ route('admin.stores.create') }}">Winkel toevoegen</a>
    </div>

    @if ($stores->isEmpty())
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
                        <th scope="col">Laatst gewijzigd</th>
                        <th scope="col"><span class="sr-only">Acties</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stores as $store)
                        <tr>
                            <td><strong>{{ $store->name }}</strong></td>
                            <td>{{ $store->categoryLabel() }}</td>
                            <td>{{ $store->location ?: '—' }}</td>
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
        <p class="admin-muted">{{ $stores->count() }} {{ $stores->count() === 1 ? 'winkel' : 'winkels' }}</p>
    @endif
@endsection
