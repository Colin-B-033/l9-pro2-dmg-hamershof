<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle winkels | De Hamershof</title>
    @vite('resources/css/app.css')
</head>
<body class="home-page site-layout stores-page">
    @include('nav')
    <main>
        <section class="stores-hero" aria-labelledby="stores-title">
            <h1 id="stores-title">Onze Winkels</h1>
            <p>Ontdek meer dan 50 winkels, horeca en diensten in winkelcentrum Hamershof.</p>
            <p>Bekijk het volledige aanbod en vind jouw favoriete winkel.</p>
        </section>

        {{-- Categorieën komen uit App\Models\Store::CATEGORIES --}}
        <nav class="stores-filter" aria-label="Winkelcategorieën">
            <a href="{{ route('stores') }}#alle-winkels" @class(['is-active' => ! $activeCategory])>Alle winkels</a>
            @foreach (\App\Models\Store::CATEGORIES as $value => $label)
                <a href="{{ route('stores', ['categorie' => $value]) }}#alle-winkels" @class(['is-active' => $activeCategory === $value])>{{ $label }}</a>
            @endforeach
        </nav>

        <section class="stores-list" id="alle-winkels" aria-labelledby="stores-list-title">
            <div class="stores-list__heading">
                <h2 id="stores-list-title">Vind jouw winkel</h2>
                <span>{{ $stores->count() }} {{ $stores->count() === 1 ? 'winkel' : 'winkels en diensten' }}</span>
            </div>

            @if ($stores->isEmpty())
                <div class="stores-empty">
                    @if ($activeCategory)
                        <p>Er zijn nog geen winkels in deze categorie.</p>
                        <a href="{{ route('stores') }}#alle-winkels">Bekijk alle winkels</a>
                    @else
                        <p>Er zijn op dit moment nog geen winkels toegevoegd. Kom snel terug!</p>
                    @endif
                </div>
            @else
                <div class="stores-grid">
                    @foreach ($stores as $store)
                        <article class="store-card">
                            {{-- Eigen afbeelding als die er is; anders de standaardfoto van de website. --}}
                            @if ($store->imageUrl())
                                <div class="store-card__image store-card__image--photo">
                                    <img src="{{ $store->imageUrl() }}" alt="" loading="lazy">
                                </div>
                            @else
                                <div class="store-card__image store-card__image--fallback store-card__image--{{ $store->category }}">
                                    <img src="{{ Vite::asset('resources/assets/steen.png') }}" alt="">
                                </div>
                            @endif
                            <div class="store-card__body">
                                <span>{{ $store->categoryLabel() }}</span>
                                <h3>{{ $store->name }}</h3>
                                @if ($store->description)
                                    <p>{{ \Illuminate\Support\Str::limit($store->description, 140) }}</p>
                                @endif
                                @if ($store->location)
                                    <p class="store-card__meta">{{ $store->location }}</p>
                                @endif
                                @if ($store->opening_hours)
                                    <p class="store-card__meta"><span class="store-card__label">Openingstijden:</span> {{ $store->opening_hours }}</p>
                                @endif
                                @if ($store->phone)
                                    <p class="store-card__meta">Tel. {{ $store->phone }}</p>
                                @endif
                                @if ($store->website)
                                    <a href="{{ $store->website }}" target="_blank" rel="noopener noreferrer">Bezoek website <span aria-hidden="true">&rarr;</span></a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
        @include('footer')
    </main>

</body>
</html>
