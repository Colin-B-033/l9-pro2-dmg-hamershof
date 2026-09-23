<header class="site-header" data-site-header>
	<a class="site-header__menu-button" href="#site-navigation" aria-controls="site-navigation" aria-expanded="false" data-menu-toggle>
		<span class="site-header__menu-icon" aria-hidden="true"><span></span><span></span><span></span></span>
		<span class="sr-only">Menu openen</span>
	</a>

	<a class="site-logo" href="/" aria-label="De Hamershof home">
		<span>de <em>Hamershof</em></span>
		<small>winkelen in het hart van Leusden</small>
	</a>

	<div class="site-navigation" id="site-navigation" data-menu-panel>
		<button class="site-navigation__close" type="button" aria-controls="site-navigation" aria-expanded="true" data-menu-close>
			<span aria-hidden="true">&times;</span>
			<span class="sr-only">Menu sluiten</span>
		</button>

		<a class="site-navigation__logo site-logo" href="/" aria-label="De Hamershof home">
			<span>de <em>Hamershof</em></span>
			<small>winkelen in het hart van Leusden</small>
		</a>

		<nav aria-label="Hoofdnavigatie">
			<a href="/">Home</a>
			<a href="/te-huur">Te Huur</a>
			<a href="/plattegrond">Plattegrond</a>
			<a href="/nieuws">Nieuws</a>
			<a href="/agenda">Agenda</a>
			<a href="/markten">Markten</a>
			<a href="/hamershofbon">Hamershofbon</a>
			<a href="/contact">Contact</a>
		</nav>

		<a class="site-navigation__login" href="/login">
			<span aria-hidden="true">↪</span> Winkelier inloggen
		</a>

		<div class="site-navigation__footer">
			<span>Volg ons</span>
			<a href="https://www.facebook.com/" aria-label="Facebook">f</a>
			<a href="https://www.instagram.com/" aria-label="Instagram">◎</a>
			<span class="site-navigation__contact">Contact us<br><a href="mailto:info@hamershof.nl">info@hamershof.nl</a></span>
			<span class="site-navigation__phone">| &nbsp; 0613558468</span>
		</div>
	</div>
</header>

<script>
	(() => {
		const header = document.querySelector('[data-site-header]');
		if (!header) return;

		const toggle = header.querySelector('[data-menu-toggle]');
		const close = header.querySelector('[data-menu-close]');
		const setOpen = (open) => {
			header.classList.toggle('is-open', open);
			toggle.setAttribute('aria-expanded', String(open));
			close.setAttribute('aria-expanded', String(open));
			document.body.classList.toggle('menu-is-open', open);
		};

		toggle.addEventListener('click', (event) => {
			event.preventDefault();
			setOpen(!header.classList.contains('is-open'));
		});
		close.addEventListener('click', () => setOpen(false));
		header.querySelectorAll('.site-navigation nav a, .site-navigation__logo').forEach((link) => {
			link.addEventListener('click', () => setOpen(false));
		});
	})();
</script>
