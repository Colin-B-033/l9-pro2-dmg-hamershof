<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Inloggen | De Hamershof</title>
	@vite('resources/css/app.css')
</head>
<body class="login-page">
	@include('nav')
	<main class="login-card">
		<h1>Welkom terug</h1>
		<p class="login-card__intro">Log in om toegang te krijgen tot uw account</p>

		<form class="login-form" method="POST" action="{{ url('/login') }}">
			@csrf
			<div class="login-form__field">
				<label for="email">E-mailadres</label>
				<input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="voorbeeld@email.nl" required autofocus>
			</div>

			<div class="login-form__field">
				<div class="login-form__label-row">
					<label for="password">Wachtwoord</label>
					@if (Route::has('password.request'))
						<a href="{{ route('password.request') }}">Wachtwoord vergeten?</a>
					@endif
				</div>
				<input type="password" id="password" name="password" placeholder="••••••••" required>
			</div>

			<label class="login-form__remember" for="remember">
				<input type="checkbox" id="remember" name="remember">
				<span>Onthoud mij</span>
			</label>

			@if ($errors->any())
				<p class="login-form__error">{{ $errors->first() }}</p>
			@endif

			<button type="submit">Inloggen</button>
		</form>
	</main>

	<footer class="login-footer">
		<p>© {{ date('Y') }} Belangenvereniging de Hamershof</p>
		<p>Powered By Dragon Media Group.</p>
	</footer>
</body>
</html>
