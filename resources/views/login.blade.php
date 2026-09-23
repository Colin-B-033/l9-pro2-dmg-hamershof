<main class="login-card">
		<h1>Inloggen</h1>

		<form method="POST" action="{{ url('/login') }}">
			<label for="email">E-mailadres</label>
			<input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
			<label for="password">Wachtwoord</label>
			<input type="password" id="password" name="password" required>

			<button type="submit">Inloggen</button>
		</form>
	</main>
