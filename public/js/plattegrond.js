(() => {
    const search = document.querySelector('#shop-search');
    const clearSearch = document.querySelector('#clear-search');
    const items = [...document.querySelectorAll('.shop-item')];
    const svg = document.querySelector('.map-stage svg');
    const shapes = [...svg.querySelectorAll('.store')];
    const dialog = document.querySelector('#map-dialog');
    let selected = null;
    let scale = 1;
    const normalize = text => text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('nl').trim();
    const matchingIds = (number, name = '') => number === '2' ? (name === 'SNS' ? ['2A'] : name === 'The Travel Company' ? ['2B'] : ['2A', '2B']) : number === '87' ? ['87a', '87b'] : number === '90' ? ['90', '90-2'] : number === '27&28' ? ['27', '28'] : [number];
    function updateShapes() {
        document.querySelectorAll('.map-stage .store, .dialog-map .store').forEach(shape => {
            const on = selected && matchingIds(selected.number, selected.name).includes(shape.dataset.id);
            shape.classList.toggle('is-selected', Boolean(on));
            shape.setAttribute('aria-pressed', String(Boolean(on)));
        });
    }
    function selectShop(number, name) {
        selected = { number, name };
        const found = shapes.some(shape => matchingIds(number, name).includes(shape.dataset.id));
        document.querySelector('#selected-name').textContent = name;
        document.querySelector('.selection-number').textContent = number;
        document.querySelector('#selected-note').textContent = !found ? 'Dit winkelnummer is nog niet aan een vlak gekoppeld.' : `Winkel ${number} is gemarkeerd op de kaart. Vergroot de kaart voor meer detail.`;
        document.querySelector('#dialog-selected').textContent = `${number} · ${name}`;
        document.querySelector('#clear-selection').hidden = false;
        items.forEach(item => item.setAttribute('aria-pressed', String(item.dataset.name === name)));
        updateShapes();
    }
    function filter() {
        const q = normalize(search.value);
        let count = 0;
        items.forEach(item => {
            const visible = normalize(`${item.dataset.number} ${item.dataset.name}`).includes(q);
            item.parentElement.hidden = !visible;
            if (visible) count++;
        });
        document.querySelector('#result-count').textContent = `${count} ${count === 1 ? 'winkel' : 'winkels'}`;
        document.querySelector('.no-results').hidden = count !== 0;
        clearSearch.hidden = !search.value;
    }
    function resetSearch() { search.value = ''; filter(); search.focus(); }
    search.addEventListener('input', filter);
    clearSearch.addEventListener('click', resetSearch);
    document.querySelector('#reset-search').addEventListener('click', resetSearch);
    items.forEach(item => {
        item.setAttribute('aria-pressed', 'false');
        item.addEventListener('click', () => {
            selectShop(item.dataset.number, item.dataset.name);
            if (matchMedia('(max-width:800px)').matches) document.querySelector('.map-card').scrollIntoView({behavior: matchMedia('(prefers-reduced-motion:reduce)').matches ? 'instant' : 'smooth', block:'start'});
        });
    });
    function bindMap(map) {
        map.setAttribute('role', 'group');
        map.setAttribute('aria-label', 'Plattegrond met winkelvakken');
        map.querySelectorAll('.store').forEach(shape => {
            const matches = items.filter(item => matchingIds(item.dataset.number, item.dataset.name).includes(shape.dataset.id));
            const name = matches.length ? matches.map(item => item.dataset.name).join(' / ') : `Winkelvak ${shape.dataset.id}`;
            shape.setAttribute('role', 'button');
            shape.setAttribute('aria-label', `${shape.dataset.id}: ${name}`);
            const activate = () => selectShop(matches[0]?.dataset.number || shape.dataset.id, name);
            shape.addEventListener('click', activate);
            shape.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); activate(); }
            });
        });
    }
    bindMap(svg);
    document.querySelector('#clear-selection').addEventListener('click', () => {
        selected = null;
        updateShapes();
        items.forEach(item => item.setAttribute('aria-pressed', 'false'));
        document.querySelector('#selected-name').textContent = 'Kies een winkel';
        document.querySelector('#selected-note').textContent = 'Tik op een winkel in de lijst of op de kaart.';
        document.querySelector('.selection-number').textContent = '↗';
        document.querySelector('#dialog-selected').textContent = 'Kies een winkel op de kaart.';
        document.querySelector('#clear-selection').hidden = true;
    });
    const mapCopy = svg.cloneNode(true);
    mapCopy.removeAttribute('id');
    mapCopy.querySelectorAll('[id]').forEach(el => el.removeAttribute('id'));
    document.querySelector('.dialog-map').append(mapCopy);
    bindMap(mapCopy);
    document.querySelector('#enlarge').addEventListener('click', () => {
        scale = 1;
        mapCopy.style.width = '100%';
        dialog.showModal();
        document.querySelector('#close-map').focus();
        updateShapes();
    });
    document.querySelector('#close-map').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => document.querySelector('#enlarge').focus());
    document.querySelectorAll('[data-zoom]').forEach(button => button.addEventListener('click', () => {
        scale = button.dataset.zoom === 'reset' ? 1 : Math.min(5, Math.max(1, scale + (button.dataset.zoom === 'in' ? .5 : -.5)));
        mapCopy.style.width = `${scale * 100}%`;
        if (scale === 1) document.querySelector('.dialog-map').scrollTo(0, 0);
    }));
    // No wheel interception, pointer capture or drag handlers on the inline map.
    filter();
})();
