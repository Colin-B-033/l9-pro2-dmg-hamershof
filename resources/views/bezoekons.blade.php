<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Bezoek Ons | De Hamershof</title>
	@vite('resources/css/app.css')
    @include('nav')
</head>
<body class="visit-page">
	<main>
		<header class="visit-hero">
			<h1>Bezoek Ons</h1>
			<p>Kom langs en ontdek alles wat we te bieden hebben!</p>
		</header>

		<section class="visit-location" aria-label="Locatie">
			<div class="visit-icon visit-icon--pin" aria-hidden="true">&#9906;</div>
			<p>Winkelcentrum Hamershof<br>Hamershof 1, 3831 AA Leusden<br>Nederland</p>
		</section>

		<section class="visit-contact-grid" aria-label="Contactgegevens">
			<article>
				<span class="visit-icon" aria-hidden="true">&#9742;</span>
				<h2>Adres</h2>
				<p>Hamershof 1<br>3831 AA Leusden</p>
			</article>
			<article>
				<span class="visit-icon" aria-hidden="true">&#9993;</span>
				<h2>Contact</h2>
				<p>info@hamershof.nl<br>033 - 494 12 00</p>
			</article>
			<article>
				<span class="visit-icon" aria-hidden="true">&#9678;</span>
				<h2>Openingstijden</h2>
				<p>Ma - Za: 09:00 - 18:00<br>Zo: 12:00 - 17:00</p>
			</article>
		</section>

		<section class="visit-hours" aria-labelledby="hours-title">
			<div class="visit-section-heading">
				<h2 id="hours-title">Volledige Openingstijden</h2>
				<span>Winkelcentrum</span>
			</div>
			<dl>
				<div><dt>Maandag</dt><dd>09:00 - 18:00</dd></div>
				<div><dt>Dinsdag</dt><dd>09:00 - 18:00</dd></div>
				<div><dt>Woensdag</dt><dd>09:00 - 18:00</dd></div>
				<div><dt>Donderdag</dt><dd>09:00 - 18:00</dd></div>
				<div><dt>Vrijdag</dt><dd>09:00 - 21:00</dd></div>
				<div><dt>Zaterdag</dt><dd>09:00 - 17:00</dd></div>
				<div><dt>Zondag</dt><dd>12:00 - 17:00</dd></div>
			</dl>
			<p class="visit-note">Let op: individuele winkels kunnen afwijkende openingstijden hebben. Controleer dit bij de betreffende winkel.</p>
		</section>

		<section class="visit-service" aria-labelledby="service-title">
			<h2 id="service-title">Bereikbaarheid</h2>
			<div class="visit-service-grid">
				<article><box-icon type='solid' name='car'></box-icon><h3>Met de auto</h3><p>Ruime parkeergelegenheid<br>in de directe omgeving.<br>Eerste 2 uur gratis parkeren.</p></article>
				<article><box-icon type='solid' name='bus-school'></box-icon><h3>Openbaar vervoer</h3><p>Goed bereikbaar met de bus.<br>Halte op 5 minuten lopen<br>van het winkelcentrum.</p></article>
				<article><box-icon name='check'></box-icon><h3>Toegankelijk</h3><p>Het winkelcentrum is<br>volledig toegankelijk voor<br>rolstoelgebruikers.</p><a href="#contact">Plan je bezoek</a></article>
			</div>
		</section>

		<section class="visit-form-section" id="contact" aria-labelledby="contact-title">
			<h2 id="contact-title">Stel een vraag</h2>
			<form class="visit-form">
				<label for="name">Naam</label>
				<input id="name" name="name" type="text">
				<label for="email">Email</label>
				<input id="email" name="email" type="email">
				<label for="subject">Onderwerp</label>
				<input id="subject" name="subject" type="text">
				<label for="message">Bericht</label>
				<textarea id="message" name="message" rows="4"></textarea>
				<button type="submit">Verstuur bericht</button>
			</form>
		</section>
        <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
	</main>
    @include('footer')
</body>
</html>
