<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Evenementen | De Hamershof</title>
	@vite('resources/css/events.css')
    @vite('resources/css/app.css')  
</head>
@include('nav')
<body class="events-page">
	<main class="events-shell">
		<header class="events-intro">
			<h1>Evenementen</h1>
			<p>Er is altijd iets te beleven bij Hamershof. Ontdek onze<br> aankomende evenementen en activiteiten.</p>
		</header>

		<section class="events-content" aria-labelledby="events-title">
			<label class="events-search">
				<span aria-hidden="true">⌕</span>
				<input type="search" placeholder="Zoek een evenement..." aria-label="Zoek een evenement">
			</label>

			<nav class="events-filters" aria-label="Filter evenementen">
				<a class="is-active" href="#alle">Alle evenementen</a>
				<a href="#mode">Mode</a>
				<a href="#familie">Familie</a>
				<a href="#cultuur">Cultuur</a>
				<a href="#muziek">Muziek</a>
				<a href="#markt">Markt</a>
				<a href="#wellness">Wellness</a>
			</nav>

			<h2 id="events-title">6 evenementen gevonden</h2>

			<div class="events-grid">
				@foreach ([
					['12', 'Lente Fashion Show', 'Bekijk de nieuwste lentetrends tijdens onze exclusieve fashion show met lokale modellen.', '14:00 - 17:00', 'Centrale Plaza', 'fashion'],
					['18', 'Kinderactiviteiten Dag', 'Een dag vol pret voor de hele familie met workshops, spelletjes en entertainment.', '10:00 - 16:00', 'Bij de fontein', 'family'],
					['25', 'Food Festival', 'Proef heerlijke gerechten van onze restaurants en ontdek nieuwe smaken.', '12:00 - 20:00', 'Food Court', 'food'],
					['5', 'Live Muziek-Avond', 'Geniet van live optredens van lokale bands en artiesten.', '18:30 - 22:00', 'Centrale Plaza', 'music'],
					['10', 'Popsmarkt', 'Speciale aanbiedingen met lokale ambachtslieden en producten.', '10:00 - 17:00', 'Hoofdingang', 'market'],
					['20', 'Beauty & Wellness Day', 'Een dag gewijd aan schoonheid en welzijn met workshops en behandelingen.', '10:00 - 18:00', 'Eerste verdieping', 'wellness']
				] as $event)
					<article class="event-card">
						<div class="event-card__visual event-card__visual--{{ $event[5] }}">
							<video autoplay muted loop playsinline aria-hidden="true">
								<source src="{{ Vite::asset('resources/assets/Home.mp4') }}" type="video/mp4">
							</video>
							<time datetime="2026-03-{{ $event[0] }}"><span>MAART</span>{{ $event[0] }}</time>
							<b>GRATIS</b>
						</div>
						<div class="event-card__body">
							<h3>{{ $event[1] }}</h3>
							<p>{{ $event[2] }}</p>
							<small><span aria-hidden="true">◷</span>{{ $event[3] }}</small>
							<small><span aria-hidden="true">⌾</span>{{ $event[4] }}</small>
						</div>
					</article>
				@endforeach
			</div>
		</section>
	</main>
    @include('footer')
</body>
</html>
