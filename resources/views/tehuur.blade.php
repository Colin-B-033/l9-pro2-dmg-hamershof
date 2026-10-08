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
						<span>Beschikbaar</span>
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
						<span>Beschikbaar</span>
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
						<span>Beschikbaar</span>
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
						<span>Beschikbaar</span>
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
					<span class="rent-benefit-icon" aria-hidden="true">◉</span>
					<h3>Toplocatie</h3>
					<p>Centrale ligging met veel bezoekers en een uitstekende bereikbaarheid.</p>
				</article>
				<article>
					<span class="rent-benefit-icon" aria-hidden="true">⌂</span>
					<h3>Royale Voetbal</h3>
					<p>Profiteer van de levendige sfeer en de vele bezoekers van het winkelcentrum.</p>
				</article>
				<article>
					<span class="rent-benefit-icon" aria-hidden="true">♧</span>
					<h3>Flexibele Mix</h3>
					<p>Een gevarieerd aanbod van winkels, horeca en dienstverlening om je heen.</p>
				</article>
			</div>
		</section>
	</main>
	@include('footer')
</body>
</html>