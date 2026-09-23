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

		<section class="home-categories" aria-labelledby="categories-title">
			<div class="home-categories__header">
				<h2 id="categories-title">Onze Winkels</h2>
				<p>van mode tot horeca, van dagelijkse boodschappen tot bijzondere cadeaus.<br>Ontdek ons uitgebreide aanbod.</p>
			</div>

			<div class="home-categories__grid">
				<article class="home-category">
					<div class="home-category__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M20 22.5V18C20 15.7909 21.7909 14 24 14H40C42.2091 14 44 15.7909 44 18V22.5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M18 22.5H46L42 50H22L18 22.5Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
							<path d="M30 28V42" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
							<path d="M34 28V42" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
						</svg>
					</div>
					<div class="home-category__meta">
						<span>Mode &amp; Accessoires</span>
						<small>18 winkels</small>
					</div>
				</article>

				<article class="home-category">
					<div class="home-category__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M18 25L32 16L46 25V44C46 46.2091 44.2091 48 42 48H22C19.7909 48 18 46.2091 18 44V25Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
							<path d="M24 25V20C24 18.3431 25.3431 17 27 17H37C38.6569 17 40 18.3431 40 20V25" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
							<path d="M25 32H39" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
						</svg>
					</div>
					<div class="home-category__meta">
						<span>Supermarkten</span>
						<small>3 winkels</small>
					</div>
				</article>

				<article class="home-category">
					<div class="home-category__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M16 20H24L28 14H36L40 20H48V30C48 40.4934 39.9411 48 29.5 48C19.0589 48 11 40.4934 11 30V20H16Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
							<path d="M20 20H44" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
							<path d="M21 31H38" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
							<path d="M25 36H34" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
						</svg>
					</div>
					<div class="home-category__meta">
						<span>Eten &amp; Drinken</span>
						<small>12 winkels</small>
					</div>
				</article>

				<article class="home-category">
					<div class="home-category__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M19 20C19 15.5817 22.5817 12 27 12H37C41.4183 12 45 15.5817 45 20V22H19V20Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
							<path d="M16 22H48V42C48 46.4183 44.4183 50 40 50H24C19.5817 50 16 46.4183 16 42V22Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
							<path d="M24 29C24 25.6863 26.6863 23 30 23H34C37.3137 23 40 25.6863 40 29V31H24V29Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
							<path d="M24 38H40" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
						</svg>
					</div>
					<div class="home-category__meta">
						<span>Koffie &amp; Snacks</span>
						<small>6 winkels</small>
					</div>
				</article>

				<article class="home-category">
					<div class="home-category__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M18 26L32 14L46 26V46C46 48.2091 44.2091 50 42 50H22C19.7909 50 18 48.2091 18 46V26Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
							<path d="M25 26V20H39V26" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
							<path d="M28 32H36" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
						</svg>
					</div>
					<div class="home-category__meta">
						<span>Wonen &amp; Lifestyle</span>
						<small>8 winkels</small>
					</div>
				</article>

				<article class="home-category">
					<div class="home-category__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M32 14C27 18.5 20 24 20 33C20 40.732 25.268 46 32 46C38.732 46 44 40.732 44 33C44 24 37 18.5 32 14Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
							<path d="M32 17L36 23H28L32 17Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
						</svg>
					</div>
					<div class="home-category__meta">
						<span>Gezondheidszorg</span>
						<small>5 winkels</small>
					</div>
				</article>
			</div>
				<section class="home-featured" aria-labelledby="featured-title">
					<h2 id="featured-title">Uitgelichte Winkels</h2>
				</section>
		</section>

	
	</main>

	@include('footer')
</body>
</html>
