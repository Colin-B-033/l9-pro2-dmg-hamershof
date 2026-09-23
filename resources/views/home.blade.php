<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>De Hamershof | Winkelen in Leusden</title>
	@vite('resources/css/app.css')
</head>
<body class="home-page site-layout">
	<main>
		<section class="home-hero" aria-labelledby="home-title">
			<video class="home-hero__video" autoplay muted loop playsinline aria-hidden="true">
				<source src="{{ Vite::asset('resources/assets/Home.mp4') }}" type="video/mp4">
			</video>
			<div class="home-hero__shade"></div>
			<div class="home-hero__content">
				<p class="home-hero__eyebrow">De Hamershof</p>
				<h1 id="home-title">Alles op één plek</h1>
				<p class="home-hero__intro">Het winkelcentrum van Hamershof waar winkelen, wonen, werken, boodschappen doen, recreëren, cultuur, educatie en leisure samenkomen.</p>
			</div>
		</section>

		<section class="home-welcome" aria-labelledby="welcome-title">
			<div class="home-welcome__copy">
				<p class="home-kicker">Welkom in Leusden</p>
				<h2 id="welcome-title">Welkom bij Hamershof</h2>
				<p>Hamershof is het bruisende winkelcentrum waar iedereen zich welkom voelt. Met meer dan 50 winkels, restaurants en diensten bieden wij alles wat u nodig heeft, op één centrale locatie.</p>
				<p>Of u nu op zoek bent naar de nieuwste mode, verse boodschappen doet, of gewoon even wilt ontspannen in een van onze gezellige cafés, bij Hamershof vindt u het allemaal.</p>
				<div class="home-stats" aria-label="Hamershof in cijfers">
					<div class="home-stat"><strong>50+</strong><span>Winkels</span></div>
					<div class="home-stat"><strong>500+</strong><span>Parkeerplaatsen</span></div>
					<div class="home-stat"><strong>7</strong><span>Dagen open</span></div>
				</div>
			</div>
			<div class="home-welcome__media">
				<img src="" alt="">
			</div>
		</section>
	</main>

	@include('footer')
</body>
</html>
