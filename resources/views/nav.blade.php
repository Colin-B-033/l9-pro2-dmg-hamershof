<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;1,6..96,400&family=Playfair+Display&display=swap">

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
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 2l20 20M22 2L2 22" /></svg>
			<span class="sr-only">Menu sluiten</span>
		</button>

		<a class="site-navigation__logo site-logo" href="/" aria-label="De Hamershof home">
			<span>de <em>Hamers</em>hof</span>
			<small>/ winkelen in het hart van Leusden</small>
		</a>

		<nav aria-label="Hoofdnavigatie">
			<a href="/home">Home</a>
			<a href="/tehuur">Te Huur</a>
			<a href="/plattegrond">Plattegrond</a>
			<a href="/events">Events</a>
			<a href="/agenda">Agenda</a>
			<a href="/stores">Winkels</a>
			<a href="/bezoekons">Bezoek ons</a>
		</nav>

		{{-- Ingelogd? Dan naar het beheer (beheerder) of het eigen account, anders naar de login. --}}
		@if (auth()->user()?->is_admin)
			<a class="site-navigation__login" href="/admin">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 7V4a1 1 0 0 1 1-1h9a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-9a1 1 0 0 1-1-1v-3M3 12h12M11 8l4 4-4 4" /></svg>
				Naar beheer
			</a>
		@elseif (auth()->check())
			<a class="site-navigation__login" href="/dashboard">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 7V4a1 1 0 0 1 1-1h9a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-9a1 1 0 0 1-1-1v-3M3 12h12M11 8l4 4-4 4" /></svg>
				Mijn account
			</a>
		@else
			<a class="site-navigation__login" href="/login">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 7V4a1 1 0 0 1 1-1h9a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-9a1 1 0 0 1-1-1v-3M3 12h12M11 8l4 4-4 4" /></svg>
				Winkelier Inloggen
			</a>
		@endif

		<div class="site-navigation__footer">
			<div class="site-navigation__social">
				<span class="site-navigation__label">Follow us</span>
				<div class="site-navigation__icons">
					<a href="https://www.facebook.com/" aria-label="Facebook">
						<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="4" /><path d="M16 8h-1.500a2 2 0 0 0-2 2v11M9.500 13H15" /></svg>
					</a>
					<a href="https://www.instagram.com/" aria-label="Instagram">
						<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" /><circle cx="12" cy="12" r="4" /><path d="M17 7h.01" /></svg>
					</a>
				</div>
			</div>
			<div class="site-navigation__contact">
				<span class="site-navigation__label">Contact us</span>
				<div class="site-navigation__details">
					<a href="mailto:info@hamershof.nl">info@hamershof.nl</a>
					<a href="tel:0613558468">0613558468</a>
				</div>
			</div>
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
