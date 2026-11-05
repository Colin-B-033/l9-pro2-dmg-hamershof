<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Te Huur | De Hamershof</title>
	@vite('resources/css/app.css')
	@include('nav')
</head>
<body class="rent-page">
	<main class="rent-content">
		<header class="rent-hero">
			<h1>Te Huur</h1>
			<p>Start uw onderneming in winkelcentrum De Hamershof</p>
		</header>

		<section class="rent-listings" aria-labelledby="listings-title">
			<div class="rent-section-heading">
				<h2 id="listings-title">Beschikbare Panden</h2>
				<p>Ontdek onze beschikbare winkelruimtes, horecagelegenheden<br>en kantoorruimtes in het hart van Leusden.</p>
			</div>

			<div class="rent-card-grid">
				<article class="rent-card">
					<div class="rent-card__image rent-card__image--winkel">
					</div>
					<div class="rent-card__body">
						<h3>Ruime Winkelunit Centrum</h3>
						<p class="rent-card__meta">Winkelruimte · 120 m²</p>
						<p>Ruime winkelunit op een centrale locatie met veel passanten.</p>
						<a href="#contact">Bekijk aanbod</a>
					</div>
				</article>
				<article class="rent-card">
					<div class="rent-card__image rent-card__image--horeca">
					</div>
					<div class="rent-card__body">
						<h3>Horecapand met Uitstraling</h3>
						<p class="rent-card__meta">Horeca · 185 m²</p>
						<p>Sfeervolle ruimte met een uitstekende ligging in het winkelcentrum.</p>
						<a href="#contact">Bekijk aanbod</a>
					</div>
				</article>
				<article class="rent-card">
					<div class="rent-card__image rent-card__image--retail">
					</div>
					<div class="rent-card__body">
						<h3>Compacte Retail Space</h3>
						<p class="rent-card__meta">Winkelruimte · 65 m²</p>
						<p>Een compacte en veelzijdige ruimte voor jouw onderneming.</p>
						<a href="#contact">Bekijk aanbod</a>
					</div>
				</article>
				<article class="rent-card">
					<div class="rent-card__image rent-card__image--winkelplein">
					</div>
					<div class="rent-card__body">
						<h3>Premium Winkelruimte</h3>
						<p class="rent-card__meta">Winkelruimte · 210 m²</p>
						<p>Moderne winkelruimte op een zichtbare en levendige locatie.</p>
						<a href="#contact">Bekijk aanbod</a>
					</div>
				</article>
			</div>
		</section>

		<section class="rent-contact" id="contact" aria-labelledby="contact-title">
			<div class="rent-contact__intro">
				<h2 id="contact-title">Interesse in een<br>Pand?</h2>
				<p>Neem contact met ons op voor meer informatie over de beschikbare ruimtes en de mogelijkheden voor jouw onderneming.</p>
				<div class="rent-contact__details">
					<a href="mailto:verhuur@hamershof.nl"><span aria-hidden="true">@</span> verhuur@hamershof.nl</a>
					<a href="tel:0334947700"><span aria-hidden="true">●</span> 033 494 77 00</a>
				</div>
			</div>
			<form class="rent-form">
				<label for="rent-name">Naam *</label>
				<input id="rent-name" name="name" type="text" placeholder="Jouw naam">
				<label for="rent-email">E-mail *</label>
				<input id="rent-email" name="email" type="email" placeholder="jouw@email.nl">
				<label for="rent-phone">Telefoon</label>
				<input id="rent-phone" name="phone" type="tel" placeholder="Jouw telefoonnummer">
				<label for="rent-message">Bericht</label>
				<textarea id="rent-message" name="message" rows="3" placeholder="Vertel ons meer over je wensen..."></textarea>
				<button type="submit">Verstuur aanvraag</button>
			</form>
		</section>

		<section class="rent-benefits" aria-labelledby="benefits-title">
			<h2 id="benefits-title">Waarom Hamershof?</h2>
			<div class="rent-benefits__grid">
				<article>
					<span class="rent-benefit-icon" aria-hidden="true"><box-icon name='map'></box-icon></span>
					<h3>Toplocatie</h3>
					<p>Centraal gelegen in Leusden met uistekende bereikbaarheid en ruim 500 parkeerplaatsen.</p>
				</article>
				<article>
					<span class="rent-benefit-icon" aria-hidden="true"><box-icon name='line-chart'></box-icon></span>
					<h3>High Footfall</h3>
					<p>Dagelijks bezoeken duizenden klanten ons winkelcentrum voor hun dagelijkse boodschappen en winkelen.</p>
				</article>
				<article>
					<span class="rent-benefit-icon" aria-hidden="true"><box-icon type='solid' name='color'></box-icon></span>
					<h3>Diverse Mix</h3>
					<p>Een gevarieerde mix van winkels, restaurants en diensten zorgt voor een aantrekkelijk winkelklimaat.</p>
				</article>
			</div>
		</section>
	</main>
	<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
	@include('footer')
</body>
</html>