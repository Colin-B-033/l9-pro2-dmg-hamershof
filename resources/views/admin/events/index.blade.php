@extends('admin.layout')

@section('title', 'Agenda')

@section('content')
    <div class="admin-page-heading">
        <div>
            <h1>Agenda</h1>
            <p>Zichtbare evenementen staan in de openbare <a href="{{ route('agenda') }}" target="_blank" rel="noopener">agenda</a>.</p>
        </div>
        <a class="admin-button" href="{{ route('admin.events.create') }}">Evenement toevoegen</a>
    </div>

    @if ($events->isEmpty())
        <div class="admin-empty">
            <h2>Nog geen evenementen</h2>
            <p>Voeg het eerste evenement toe. Het verschijnt daarna direct in de agenda op de website.</p>
            <a class="admin-button" href="{{ route('admin.events.create') }}">Eerste evenement toevoegen</a>
        </div>
    @else
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">Titel</th>
                        <th scope="col">Datum</th>
                        <th scope="col">Tijd</th>
                        <th scope="col">In de agenda</th>
                        <th scope="col"><span class="sr-only">Acties</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $event)
                        <tr @class(['is-hidden' => ! $event->is_visible])>
                            <td>
                                <strong>{{ $event->title }}</strong>
                                @if ($event->location)
                                    <br><span class="admin-muted">{{ $event->location }}</span>
                                @endif
                            </td>
                            <td>{{ $event->date->format('d-m-Y') }}</td>
                            <td>{{ $event->timeLabel() ?? '—' }}</td>
                            <td>
                                @if ($event->is_visible)
                                    <span class="admin-status">Zichtbaar</span>
                                @else
                                    <span class="admin-status admin-status--hidden">Verborgen</span>
                                @endif
                            </td>
                            <td class="admin-table__actions">
                                <a class="admin-button admin-button--small admin-button--secondary" href="{{ route('admin.events.edit', $event) }}">
                                    Bewerken<span class="sr-only"> {{ $event->title }}</span>
                                </a>
                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                      onsubmit="return confirm('Weet je zeker dat je dit evenement wilt verwijderen? Dit kan niet ongedaan worden gemaakt.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-button admin-button--small admin-button--danger">
                                        Verwijderen<span class="sr-only"> {{ $event->title }}</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="admin-muted">{{ $events->count() }} {{ $events->count() === 1 ? 'evenement' : 'evenementen' }}, gesorteerd op datum en begintijd.</p>
    @endif
@endsection
