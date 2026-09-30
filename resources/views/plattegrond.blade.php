<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plattegrond | De Hamershof</title>
    @vite('resources/css/app.css')
</head>
<body class="map-page">
    <a class="map-skip-link" href="#plattegrond">Naar de plattegrond</a>

    <header class="map-header">
        <div class="map-header__inner">
            <details class="map-menu">
                <summary aria-label="Navigatiemenu"><span></span><span></span><span></span></summary>
                <nav aria-label="Hoofdnavigatie">
                    <a href="{{ url('/home') }}">Home</a>
                    <a href="{{ route('plattegrond') }}" aria-current="page">Plattegrond</a>
                    <a href="{{ url('/login') }}">Inloggen</a>
                </nav>
            </details>

            <a class="map-brand" href="{{ url('/home') }}" aria-label="De Hamershof — home">
                <span>de <em>Hamershof</em></span>
                <small>winkelen in het hart van Leusden</small>
            </a>
        </div>
    </header>

    <main id="plattegrond" class="map-main">
        <section class="map-intro" aria-labelledby="map-title">
            <h1 id="map-title">Plattegrond</h1>
            <p>Ontdek waar al onze winkels zich bevinden in De Hamershof</p>
        </section>

        <div class="map-columns">
            <section class="map-panel map-panel--image" aria-labelledby="map-image-title">
                <h2 id="map-image-title">Interactieve Kaart</h2>
                <div class="map-image-wrap">
                <img
                    class="map-image"
                    src="{{ asset('images/plattegrond-hamershof.jpg') }}"
                    alt="Plattegrond van winkelcentrum De Hamershof met genummerde winkelpanden, straten en parkeerplaatsen."
                    width="2000"
                    height="1427"
                >
                    <button type="button" class="map-mitra-area mitra-hover" aria-label="Winkel 1: Drankenspeciaalzaak MITRA"></button>
                    <span class="map-mitra-label" aria-hidden="true">1 · Drankenspeciaalzaak MITRA</span>
                      <button type="button" class="map-sns-area sns-hover" aria-label="Winkel 2: SNS"></button>
                     <span class="map-sns-label" aria-hidden="true">2 · SNS</span>
                     <button type="button" class="map-sns-area sns-hover" aria-label="Winkel 2: The Travel Company"></button>
                     <span class="map-sns-label" aria-hidden="true">2 · The Travel Company</span>
                </div>
                </div>
            </section>

            <aside class="map-panel map-panel--shops" aria-labelledby="map-shops-title">
                <h2 id="map-shops-title">Op de Kaart</h2>
                <div class="map-shop-scroll" tabindex="0" role="region" aria-label="Winkellijst, scroll voor meer winkels">
                    <ul class="map-shops">
                        @foreach ([
                            ['1', 'Drankenspeciaalzaak MITRA'],
                            ['2', 'SNS'],
                            ['2', 'The Travel Company'],
                            ['3', 'Dier & Beijer'],
                            ['4', 'Adam schaar'],
                            ['5', 'Wibra'],
                            ['6', 'Chérie Lingerie & Bodywear'],
                            ['7', 'Baas Optiek'],
                            ['8', 'de Broekenwinkel'],
                            ['9', 'Top1Toys Leusden'],
                            ['10', 'Marskramer Leusden'],
                            ['10a', 'B&B Kitchen'],
                            ['12', 'Kippie Leusden'],
                            ['13', 'Jumbo Wahle De Hamershof Leusden'],
                            ['14', 'Multivlaai'],
                            ['15', 'Ru-Vis Uw Visspecialist'],
                            ['19', 'Gall & Gall'],
                            ['20', 'Hizi Hair'],
                            ['21', 'van Dal Mannenmode'],
                            ['23', 'Bakkerij Niessen'],
                            ['24', 'Leusder Doner Kebab'],
                            ['25', 'Brasserie de Buurman'],
                            ['26', 'Kapsalon Own look'],
                            ['27&28', 'Primera'],
                            ['29', 'Eye Wish Groeneveld'],
                            ['31', 'Van Beest Bloemen en Planten'],
                            ['32', 'Hans Anders'],
                            ['33', 'Bike Drive'],
                            ['38', 'Brasserie Bij Vallery'],
                            ['40', 'Herenkapper Willem van Someren'],
                            ['41', 'Melody Mode'],
                            ['42', 'MS mode'],
                            ['43', 'Rosetta Boekhandel'],
                            ['44', 'Fayters'],
                            ['45', 'Goudreinet'],
                            ['47', 'Kaaszaak van Leusden'],
                            ['48', 'Wereldwinkel'],
                            ['49', 'Pour Vous Parfumerie | Conny Robbemond'],
                            ['51', 'Kruiden & Zo'],
                            ['52', 'Jola Mode'],
                            ['53', 'iSushi Shop'],
                            ['54', 'Bella Venezia 2'],
                            ['56', 'Dental Clinics Leusden'],
                            ['57', 'Leusden Natuurlijk!'],
                            ['58', 'Pets Place'],
                            ['59', 'Lekkerman'],
                            ['60', 'Albert Heijn'],
                            ['61', 'Het panterhuis'],
                            ['62', 'Shoeby'],
                            ['63', 'HEMA'],
                            ['64', 'De Bibliotheek'],
                            ['65', 'Vers Bakkerij Leusden'],
                            ['66', 'Smartphonereparaties Leusden'],
                            ['67', 'Slagerij & Partyservice Gelderblom'],
                            ['68', 'Pouls'],
                            ['69', 'Wim Teunissen Juweliers'],
                            ['70', 'Barbershop de Knipkoning'],
                            ['71', 'OKE Mobiel en Mode accessoires'],
                            ['72', 'The Cryo Lounge'],
                            ['74', 'Nova PMU & Laser Clinic'],
                            ['75', 'Happy Supermarkt'],
                            ['76', 'Fotografie Jan Verhoeff'],
                            ['77', 'Apotheek De Hamershof'],
                            ['78', 'Symphony'],
                            ['79', 'Etos'],
                            ['80', 'Baronyan Juweliers'],
                            ['81', 'Studio Jill'],
                            ['82', 'Schoonenberg Hoorcomfort'],
                            ['83', 'Zeeman'],
                            ['85', 'Kruidvat'],
                            ['87', 'Pearle'],
                            ['88', 'Bruna'],
                            ['90', 'Boni'],
                            ['94', 'Chiropractie Leusden'],
                            ['95', 'Koopjesfietsen.nl'],
                            ['96', "L'ans Fashion"],
                            ['98', 'Perfect Hair Studio'],
                            ['111', 'De Korf'],
                            ['112', 'Theater De Tuin'],
                            ['113', 'Fietsen van Greefhorst'],
                        ] as [$number, $name])
                            <li @class(['map-shop--mitra' => $number === '1'])>
                                @if ($number === '1')
                                    <button type="button" class="map-shop-button mitra-hover" aria-label="Toon winkel 1: Drankenspeciaalzaak MITRA op de kaart">
                                        <span class="map-shop-number">{{ $number }}</span><span>{{ $name }}</span>
                                    </button>
                                @else
                                    <span class="map-shop-number">{{ $number }}</span><span>{{ $name }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </main>

    @include('footer')
</body>
</html>
