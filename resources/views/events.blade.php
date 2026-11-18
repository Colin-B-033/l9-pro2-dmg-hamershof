<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Evenementen | De Hamershof</title>
	@vite(['resources/css/app.css', 'resources/css/events.css'])
</head>
<body class="events-page">
	@include('nav')
	<main class="events-shell">
		<header class="events-intro">
			<h1>Evenementen</h1>
			<p>Er is altijd iets te beleven bij Hamershof. Ontdek onze<br> aankomende evenementen en activiteiten.</p>
		</header>

		<section class="events-content" aria-labelledby="events-title">
			{{-- Gewoon GET-formulier: de zoekterm komt in de adresbalk als ?zoek=... --}}
			<form class="events-search-form" method="GET" action="{{ route('events') }}" role="search">
				<label class="events-search">
					<span aria-hidden="true">⌕</span>
					<input type="search" name="zoek" value="{{ $search }}" maxlength="100" placeholder="Zoek een evenement..." aria-label="Zoek een evenement op titel">
				</label>
				<button type="submit" class="events-search-form__button">Zoeken</button>
				@if ($search !== '')
					<a class="events-search-form__clear" href="{{ route('events') }}">Zoekopdracht wissen</a>
				@endif
			</form>

			@php($count = $events->count())

			@if ($count > 0)
				<h2 id="events-title">
					{{ $count }} {{ $count === 1 ? 'evenement' : 'evenementen' }} gevonden
					@if ($search !== '') voor &ldquo;{{ $search }}&rdquo; @endif
				</h2>

				<div class="events-grid">
					@foreach ($events as $event)
						<article class="event-card">
							{{-- De bestaande beeldstijlen (uitsnede van de standaardfoto) wisselen per kaart. --}}
							<div class="event-card__visual event-card__visual--{{ ['fashion', 'family', 'food', 'music', 'market', 'wellness'][$loop->index % 6] }}">
								<img src="{{ Vite::asset('resources/assets/oudemensen.png') }}" alt="">
								{{-- Datumblokje; de volledige datum staat ook in de kaarttekst. --}}
								<time datetime="{{ $event->date->format('Y-m-d') }}" aria-hidden="true"><span>{{ $event->monthShort() }}</span>{{ $event->date->day }}</time>
							</div>
							<div class="event-card__body">
								<h3>{{ $event->title }}</h3>
								@if ($event->description)
									<p>{{ \Illuminate\Support\Str::limit($event->description, 120) }}</p>
								@endif
								<small><span aria-hidden="true">▦</span>{{ $event->dateLabel() }}</small>
								@if ($event->timeLabel())
									<small><span aria-hidden="true">◷</span>{{ $event->timeLabel() }}</small>
								@endif
								@if ($event->location)
									<small><span aria-hidden="true">⌾</span>{{ $event->location }}</small>
								@endif
							</div>
						</article>
					@endforeach
				</div>
			@elseif ($search !== '')
				<div class="events-empty">
					<h2 id="events-title">Geen evenementen gevonden</h2>
					<p>Er is geen aankomend evenement met &ldquo;{{ $search }}&rdquo; in de titel.</p>
					<a class="events-empty__button" href="{{ route('events') }}">Toon alle evenementen</a>
				</div>
			@else
				<div class="events-empty">
					<h2 id="events-title">Nog geen aankomende evenementen</h2>
					<p>Er staan op dit moment geen evenementen gepland. Kijk binnenkort nog eens, of bekijk eerdere activiteiten in de agenda.</p>
					<a class="events-empty__button" href="{{ route('agenda') }}">Naar de agenda</a>
				</div>
			@endif
		</section>
	</main>
    @include('footer')
</body>
</html>
