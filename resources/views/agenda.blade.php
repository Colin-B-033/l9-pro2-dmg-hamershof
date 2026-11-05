<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agenda | De Hamershof</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/agenda.css') }}">
    <script src="{{ asset('js/agenda.js') }}" defer></script>
</head>
<body class="site-layout agenda-page">
    @include('nav')
    <main class="agenda-main">
        <header class="agenda-intro"><h1>Agenda</h1><p>Ontdek alle activiteiten en evenementen</p></header>
        <section class="agenda-content" aria-label="Evenementenkalender">
            <label class="agenda-search"><span aria-hidden="true">⌕</span><input type="search" id="event-search" aria-label="Zoek evenementen" placeholder="Zoek evenementen..."></label>
            <div class="agenda-tabs" role="group" aria-label="Weergave"><button type="button" data-view="calendar" aria-pressed="true">▦ Kalender</button><button type="button" data-view="list" aria-pressed="false">☷ Lijst</button></div>
            <div class="calendar-shell">
                <div class="month-bar"><button type="button" id="previous-month" aria-label="Vorige maand">‹</button><h2 aria-live="polite"><span id="month-name"></span> <small id="year-number"></small></h2><button type="button" id="next-month" aria-label="Volgende maand">›</button></div>
                <div id="calendar-view"><div class="weekdays" aria-hidden="true"><span>MA</span><span>DI</span><span>WO</span><span>DO</span><span>VR</span><span>ZA</span><span>ZO</span></div><div id="calendar-days" class="calendar-days" aria-label="Dagen van de maand"></div></div>
                <div id="list-view" hidden><p>Er staan nog geen evenementen in deze maand.</p></div>
            </div>
            <p id="search-status" role="status" hidden></p>
        </section>
    </main>
    @include('footer')
</body>
</html>
