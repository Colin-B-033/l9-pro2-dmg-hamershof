/*
 * Openbare agenda (/agenda).
 *
 * De evenementen komen uit het CMS: Laravel zet de zichtbare evenementen als JSON
 * in <script id="agenda-events">. Dit script toont ze in de kalender en in de lijst.
 *
 * Veiligheid: tekst van beheerders (titel, locatie, omschrijving) zetten we altijd
 * met textContent in de pagina, nooit met innerHTML. Zo wordt HTML nooit uitgevoerd.
 *
 * Datums: een datum is gewone tekst, bijvoorbeeld "2026-11-18". We gebruiken bewust
 * géén new Date("2026-11-18"): dat leest de datum als UTC-tijd, waardoor hij in
 * sommige tijdzones op de vorige dag uitkomt. We vergelijken daarom gewoon tekst.
 */
(() => {
    const months = ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'];
    const weekdays = ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'];

    const elements = {
        monthName: document.querySelector('#month-name'),
        yearNumber: document.querySelector('#year-number'),
        days: document.querySelector('#calendar-days'),
        calendarView: document.querySelector('#calendar-view'),
        listView: document.querySelector('#list-view'),
        search: document.querySelector('#event-search'),
        status: document.querySelector('#search-status'),
        clearSearch: document.querySelector('#clear-search'),
        viewButtons: document.querySelectorAll('[data-view]'),
    };

    // Evenementen uit het CMS inlezen. Lukt dat niet, dan tonen we een lege agenda.
    let events = [];
    try {
        events = JSON.parse(document.querySelector('#agenda-events').textContent) || [];
    } catch (error) {
        events = [];
    }

    // Vandaag volgens Nederlandse tijd, als "2026-11-18".
    const parts = new Intl.DateTimeFormat('en-CA', { timeZone: 'Europe/Amsterdam', year: 'numeric', month: '2-digit', day: '2-digit' }).formatToParts(new Date());
    const part = (type) => parts.find((p) => p.type === type).value;
    const today = `${part('year')}-${part('month')}-${part('day')}`;

    // De gekozen maand en zoekterm. Kalender en lijst gebruiken allebei deze waarden.
    let year = Number(part('year'));
    let month = Number(part('month')) - 1; // 0 = januari, 11 = december
    let searchTerm = '';
    let view = 'calendar';

    const pad = (number) => String(number).padStart(2, '0');

    // Bijvoorbeeld isoDate(2026, 10, 18) => "2026-11-18" (let op: maand 10 = november).
    const isoDate = (y, m, d) => `${y}-${pad(m + 1)}-${pad(d)}`;

    const monthLabel = () => `${months[month].toLowerCase()} ${year}`;

    // "2026-11-18" => "woensdag 18 november 2026"
    function formatDate(dateText) {
        const [y, m, d] = dateText.split('-').map(Number);
        // new Date(jaar, maand, dag) gebruikt de lokale tijd, dus hier verschuift de dag niet.
        const weekday = weekdays[new Date(y, m - 1, d).getDay()];
        return `${weekday} ${d} ${months[m - 1].toLowerCase()} ${y}`;
    }

    // "14:00 – 16:00", "14:00" of "" (geen tijd)
    function formatTime(event) {
        if (!event.start_time) {
            return '';
        }
        return event.end_time ? `${event.start_time} – ${event.end_time}` : event.start_time;
    }

    // Maakt een element met veilige tekst.
    function createElement(tag, className, text) {
        const element = document.createElement(tag);
        if (className) {
            element.className = className;
        }
        if (text !== undefined) {
            element.textContent = text;
        }
        return element;
    }

    // Evenementen in de gekozen maand waarvan de titel de zoekterm bevat (hoofdletters maken niet uit).
    // De volgorde (datum, begintijd) komt al goed uit Laravel.
    function eventsInSelectedMonth() {
        const prefix = `${year}-${pad(month + 1)}-`;
        const term = searchTerm.toLocaleLowerCase('nl');

        return events.filter((event) => event.date.startsWith(prefix)
            && event.title.toLocaleLowerCase('nl').includes(term));
    }

    function emptyMessage() {
        return searchTerm
            ? `Geen evenementen gevonden voor “${searchTerm}” in ${monthLabel()}.`
            : `Er staan geen evenementen in ${monthLabel()}.`;
    }

    function renderCalendar(monthEvents) {
        elements.days.replaceChildren();

        const offset = (new Date(year, month, 1).getDay() + 6) % 7; // maandag = eerste kolom
        const total = new Date(year, month + 1, 0).getDate(); // aantal dagen in de maand
        const cells = Math.ceil((offset + total) / 7) * 7;

        for (let i = 0; i < cells; i++) {
            const cell = createElement('div', 'calendar-day');
            const day = i - offset + 1;

            if (day < 1 || day > total) {
                cell.classList.add('outside');
                cell.setAttribute('aria-hidden', 'true');
                elements.days.append(cell);
                continue;
            }

            const date = isoDate(year, month, day);
            const time = createElement('time', '', String(day));
            time.dateTime = date;
            time.setAttribute('aria-label', `${day} ${months[month]} ${year}`);
            if (date === today) {
                cell.classList.add('today');
                time.setAttribute('aria-current', 'date');
            }
            cell.append(time);

            // Alle evenementen van deze dag (dat kunnen er meerdere zijn).
            const dayEvents = monthEvents.filter((event) => event.date === date);
            if (dayEvents.length > 0) {
                const list = createElement('ul', 'calendar-events');
                dayEvents.forEach((event) => {
                    const item = createElement('li', 'calendar-event');
                    if (event.start_time) {
                        item.append(createElement('span', 'calendar-event__time', event.start_time), ' ');
                    }
                    item.append(createElement('span', 'calendar-event__title', event.title));
                    item.title = event.title; // volledige titel bij aanwijzen, als hij is afgekort
                    list.append(item);
                });
                cell.append(list);
                cell.classList.add('has-events');
            }

            elements.days.append(cell);
        }
    }

    function renderList(monthEvents) {
        elements.listView.replaceChildren();

        if (monthEvents.length === 0) {
            elements.listView.append(createElement('p', 'agenda-empty', emptyMessage()));
            return;
        }

        const list = createElement('ol', 'agenda-list');
        monthEvents.forEach((event) => {
            const item = createElement('li', 'agenda-item');
            item.append(createElement('h3', '', event.title));

            const when = createElement('p', 'agenda-item__when');
            const date = createElement('time', '', formatDate(event.date));
            date.dateTime = event.date;
            when.append(date);
            if (event.start_time) {
                when.append(createElement('span', '', `, ${formatTime(event)}`));
            }
            item.append(when);

            if (event.location) {
                item.append(createElement('p', 'agenda-item__location', `Locatie: ${event.location}`));
            }
            if (event.description) {
                item.append(createElement('p', 'agenda-item__description', event.description));
            }

            list.append(item);
        });
        elements.listView.append(list);
    }

    // Melding boven de kalender: aantal zoekresultaten, of dat er niets is.
    // In de lijstweergave staat de "leeg"-melding al in de lijst zelf.
    function renderStatus(monthEvents) {
        let message = '';
        if (monthEvents.length === 0 && view === 'calendar') {
            message = emptyMessage();
        } else if (monthEvents.length > 0 && searchTerm) {
            const count = monthEvents.length;
            message = `${count} ${count === 1 ? 'evenement' : 'evenementen'} gevonden voor “${searchTerm}” in ${monthLabel()}.`;
        }

        elements.status.textContent = message;
        elements.clearSearch.hidden = searchTerm === '';
    }

    function render() {
        elements.monthName.textContent = months[month];
        elements.yearNumber.textContent = year;

        const monthEvents = eventsInSelectedMonth();
        renderCalendar(monthEvents);
        renderList(monthEvents);
        renderStatus(monthEvents);
    }

    // Vorige/volgende maand; new Date regelt de jaargrens (december -> januari).
    function shift(direction) {
        const next = new Date(year, month + direction, 1);
        year = next.getFullYear();
        month = next.getMonth();
        render();
    }

    function setView(newView) {
        view = newView;
        elements.viewButtons.forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.view === view)));
        elements.calendarView.hidden = view !== 'calendar';
        elements.listView.hidden = view !== 'list';
        render();
    }

    function clearSearch() {
        elements.search.value = '';
        searchTerm = '';
        render();
        elements.search.focus();
    }

    document.querySelector('#previous-month').addEventListener('click', () => shift(-1));
    document.querySelector('#next-month').addEventListener('click', () => shift(1));
    elements.viewButtons.forEach((button) => button.addEventListener('click', () => setView(button.dataset.view)));

    elements.search.addEventListener('input', () => {
        searchTerm = elements.search.value.trim();
        render();
    });
    elements.search.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            clearSearch();
        }
    });
    elements.clearSearch.addEventListener('click', clearSearch);

    render();
})();
