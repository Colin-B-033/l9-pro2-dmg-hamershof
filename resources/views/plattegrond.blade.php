<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plattegrond · De Hamershof</title>
    <link rel="stylesheet" href="{{ asset('css/plattegrond.css') }}">
    <script src="{{ asset('js/plattegrond.js') }}" defer></script>
</head>
<body class="design-page">
    <a class="skip" href="#main">Naar de winkels</a>
    <header class="design-header">
        <a class="brand" href="/home">de <em>Hamershof</em><small>winkelen in het hart van Leusden</small></a>
        <a href="/home">Terug naar home ↗</a>
    </header>
<main id="main" class="design-main">
        <section class="hero">
            <div><p class="eyebrow">ONTDEK DE HAMERSHOF · LEUSDEN</p><h1>Vind jouw winkel.</h1>
            <p class="intro">Van je dagelijkse boodschappen tot iets bijzonders. Zoek je winkel en ontdek waar je moet zijn.</p></div>
            <div class="search-box">
                <label for="shop-search">Welke winkel zoek je?</label>
                <div class="search-input"><span aria-hidden="true">⌕</span><input id="shop-search" type="search" placeholder="Bijvoorbeeld HEMA, Mitra of 63" autocomplete="off"><button type="button" id="clear-search" aria-label="Zoekopdracht wissen" hidden>×</button></div>
                <p>Typ een naam of winkelnummer. De resultaten verschijnen direct.</p>
            </div>
        </section>
        <div class="workspace">
            <section class="map-card" aria-labelledby="map-heading">
                <div class="section-heading"><div><p class="eyebrow">ZO VIND JE DE WEG</p><h2 id="map-heading">De plattegrond</h2></div><button class="outline-button" id="enlarge" type="button">Vergroot kaart ↗</button></div>
                <div class="map-stage">@include('plattegrond-map')</div>
                <div class="map-caption"><span class="dot"></span><span>Scroll gerust verder. Vergroot de kaart om de winkels beter te bekijken.</span></div>
                <div class="selection" aria-live="polite"><span class="selection-number">↗</span><div><strong id="selected-name">Kies een winkel</strong><p id="selected-note">Tik op een winkel in de lijst of op de kaart.</p></div><button type="button" id="clear-selection" aria-label="Selectie wissen" hidden>×</button></div>
            </section>
            <section class="directory" aria-labelledby="directory-heading">
                <div class="section-heading"><div><p class="eyebrow">ONTDEK HET AANBOD</p><h2 id="directory-heading">Onze winkels</h2></div><span id="result-count" role="status"></span></div>
                <ul class="shop-list">
                    @foreach ($shops as [$number, $name])
                    <li><button type="button" class="shop-item" data-number="{{ $number }}" data-name="{{ $name }}"><span class="shop-number">{{ $number }}</span><span class="shop-name">{{ $name }}</span><span class="shop-arrow" aria-hidden="true">↗</span></button></li>
                    @endforeach
                </ul>
                <div class="no-results" hidden><h3>Geen winkel gevonden</h3><p>Probeer een andere naam of een winkelnummer.</p><button type="button" id="reset-search">Toon alle winkels</button></div>
            </section>
        </div>
        <section class="visit-note"><p class="eyebrow">MAAK ER EEN FIJN BEZOEK VAN</p><h2>Even de tijd voor Leusden.</h2><p>Je favoriete winkels, een gezellige tussenstop en alles voor elke dag. Welkom in De Hamershof.</p><a href="/home">Terug naar home →</a></section>
    </main>
    @include('footer')
    <dialog id="map-dialog" aria-labelledby="dialog-title"><div class="dialog-bar"><div><h2 id="dialog-title">De Hamershof</h2><p id="dialog-selected">Kies een winkel op de kaart.</p></div><button id="close-map" type="button">Sluiten ×</button></div><div class="dialog-map"></div><div class="zoom-controls"><button type="button" data-zoom="in" aria-label="Inzoomen">+</button><button type="button" data-zoom="out" aria-label="Uitzoomen">−</button><button type="button" data-zoom="reset">Hele kaart</button></div><p class="dialog-hint">Gebruik + en − om te vergroten. Scroll om de vergrote kaart te bekijken.</p></dialog>
</body>
</html>
