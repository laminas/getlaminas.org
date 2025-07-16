'use strict';

document.querySelectorAll('.package-button.type, .package-button.category, .package-button.usage').forEach(button => {
    button.addEventListener('click', handleFilters);
})

document.querySelectorAll('.package-button.keyword').forEach(button => {
    button.addEventListener('click', handleKeywords);
})

document.querySelectorAll('.ecosystem-filter').forEach(button => {
    button.addEventListener('click', removeKeyword);
})

document.querySelectorAll('#ecosystem-pagination a').forEach(a => {
    const url = new URL(a.href)
    for (let [k,v] of new URLSearchParams(window.location.search).entries()) {
        if (k === 'keywords[]' || k === 'q' || k === 'type' || k === 'category' || k === 'usage') {
            url.searchParams.set(k,v);
        }
    }
    a.href = url.toString();
})

document.querySelector('#clear-filters-button')?.addEventListener('click', function () {
    const url = new URL(window.location.href);

    for (let [k,v] of new URLSearchParams(window.location.search).entries()) {
        if (k !== 'page') {
            url.searchParams.delete(k);
        }
    }

    window.location.replace(url.toString());
});

document.querySelector('#ecosystem-search-btn').addEventListener('click', function () {
    setSearchQuery(document.querySelector('#ecosystem-search').value);
});

document.querySelector('#ecosystem-search').addEventListener('keypress', function (e) {
    const search = this.value;
    if (e.which === 13) {
        setSearchQuery(search);
    }
})

function handleFilters() {
    for (const filter of ['type', 'category', 'usage']) {
        if (this.classList.contains(filter)) {
            handleParams(filter, this.dataset.value);
        }
    }
}

function handleParams(filterKey, filterValue) {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);

    if (filterValue === 'all' || params.get(filterKey) === filterValue) {
        url.searchParams.delete(filterKey);

        window.location.replace(url.toString());
    } else if (! params.has(filterKey, filterValue)) {
        url.searchParams.set(filterKey, filterValue);

        window.location.replace(url.toString());
    }
}

function handleKeywords() {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    const keyword = this.dataset.value;

    if (! params.has("keywords[]", keyword)) {
        params.append("keywords[]", keyword);
        url.search = params.toString();

        window.location.replace(url.toString());
    }
}

function removeKeyword() {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    const keyword = this.dataset.value;

    if (params.has("keywords[]", keyword)) {
        params.delete("keywords[]", keyword);
        url.search = params.toString();

        window.location.replace(url.toString());
    }
}

function setSearchQuery(search) {
    const url = new URL(window.location.href);

    url.searchParams.set('q', search);
    window.location.replace(url.toString());
}
