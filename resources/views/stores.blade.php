<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle winkels | De Hamershof</title>
    @vite('resources/css/app.css')
</head>
@include('nav')
<body class="home-page site-layout stores-page">
    <main>
        <section class="stores-hero" aria-labelledby="stores-title">
            <h1 id="stores-title">Onze Winkels</h1>
            <p>Ontdek meer dan 50 winkels, horeca en diensten in winkelcentrum Hamershof.</p>
            <p>Bekijk het volledige aanbod en vind jouw favoriete winkel.</p>
        </section>

        <nav class="stores-filter" aria-label="Winkelcategorieën">
            <a href="#alle-winkels">Alle winkels</a>
            <a href="#mode">Mode &amp; accessoires</a>
            <a href="#supermarkten">Supermarkten</a>
            <a href="#eten">Eten &amp; drinken</a>
            <a href="#diensten">Diensten</a>
        </nav>

        <section class="stores-list" id="alle-winkels" aria-labelledby="stores-list-title">
            <div class="stores-list__heading">
                <h2 id="stores-list-title">Vind jouw winkel</h2>
                <span>50+ winkels en diensten</span>
            </div>

            <div class="stores-grid">
                @foreach ([
                    ['mode', 'mode', 'Fashion District', 'Mode & accessoires', 'Vandaag open tot 18:00'],
                    ['mode', 'mode', 'Shoeby', 'Mode & accessoires', 'Vandaag open tot 18:00'],
                    ['mode', 'mode', 'Bristol', 'Mode & accessoires', 'Vandaag open tot 18:00'],
                    ['supermarkten', 'supermarkten', 'Albert Heijn', 'Supermarkt', 'Vandaag open tot 20:00'],
                    ['supermarkten', 'supermarkten', 'Jumbo', 'Supermarkt', 'Vandaag open tot 20:00'],
                    ['supermarkten', 'supermarkten', 'Kruidvat', 'Drogisterij', 'Vandaag open tot 18:00'],
                    ['eten', 'eten', 'Café Central', 'Horeca', 'Vandaag open tot 18:00'],
                    ['eten', 'eten', 'Restaurant Plaza', 'Restaurant', 'Vandaag open tot 24:00'],
                    ['eten', 'eten', 'Bakker Bart', 'Eten & drinken', 'Vandaag open tot 17:00'],
                    ['diensten', 'diensten', 'Haarstudio Leusden', 'Persoonlijke verzorging', 'Vandaag open tot 18:00'],
                    ['diensten', 'diensten', 'Etos Apotheek', 'Gezondheid', 'Vandaag open tot 18:00'],
                    ['diensten', 'diensten', 'PostNL Punt', 'Dienstverlening', 'Vandaag open tot 18:00']
                ] as $store)
                    <article class="store-card" id="{{ $store[0] }}">
                        <div class="store-card__image store-card__image--{{ $store[1] }}">
                            <video autoplay muted loop playsinline aria-hidden="true"><source src="{{ Vite::asset('resources/assets/Home.mp4') }}" type="video/mp4"></video>
                        </div>
                        <div class="store-card__body">
                            <span>{{ $store[3] }}</span>
                            <h3>{{ $store[2] }}</h3>
                            <p>{{ $store[4] }}</p>
                            <a href="#contact">Bekijk winkel <span aria-hidden="true">&rarr;</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
        @include('footer')
    </main>

</body>
</html>
