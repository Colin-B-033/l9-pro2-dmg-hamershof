(() => {
    const parts = new Intl.DateTimeFormat('en-CA', {timeZone:'Europe/Amsterdam', year:'numeric', month:'numeric'}).formatToParts(new Date());
    let year = Number(parts.find(p => p.type === 'year').value);
    let month = Number(parts.find(p => p.type === 'month').value) - 1;
    const months = ['Januari','Februari','Maart','April','Mei','Juni','Juli','Augustus','September','Oktober','November','December'];
    function render() {
        document.querySelector('#month-name').textContent = months[month];
        document.querySelector('#year-number').textContent = year;
        const grid = document.querySelector('#calendar-days');
        grid.replaceChildren();
        const offset = (new Date(year, month, 1).getDay() + 6) % 7;
        const total = new Date(year, month + 1, 0).getDate();
        const cells = Math.ceil((offset + total) / 7) * 7;
        for (let i = 0; i < cells; i++) {
            const cell = document.createElement('div');
            cell.className = 'calendar-day';
            const day = i - offset + 1;
            if (day < 1 || day > total) { cell.classList.add('outside'); cell.setAttribute('aria-hidden','true'); }
            else { const time = document.createElement('time'); time.dateTime = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`; time.textContent = day; time.setAttribute('aria-label',`${day} ${months[month]} ${year}`); cell.append(time); }
            grid.append(cell);
        }
    }
    function shift(direction) { const next = new Date(year, month + direction, 1); year = next.getFullYear(); month = next.getMonth(); render(); }
    document.querySelector('#previous-month').addEventListener('click', () => shift(-1));
    document.querySelector('#next-month').addEventListener('click', () => shift(1));
    document.querySelectorAll('[data-view]').forEach(button => button.addEventListener('click', () => {
        document.querySelectorAll('[data-view]').forEach(b => b.setAttribute('aria-pressed', String(b === button)));
        document.querySelector('#calendar-view').hidden = button.dataset.view !== 'calendar';
        document.querySelector('#list-view').hidden = button.dataset.view !== 'list';
    }));
    document.querySelector('#event-search').addEventListener('input', e => { const status = document.querySelector('#search-status'); status.hidden = !e.target.value.trim(); status.textContent = 'Geen evenementen gevonden. De agenda is nog leeg.'; });
    render();
})();
