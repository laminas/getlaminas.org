'use strict';

$(document).ready(function () {
    $('.package-button').click(function (e) {
        e.preventDefault();

        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        const entry = $(this).data('value');

        if ($(this).hasClass('keyword')) {
            if (! params.has("keywords[]", entry)) {
                params.append("keywords[]", entry);
                url.search = params.toString();

                window.location.replace(url.toString());
            }
        }

        if ($(this).hasClass('type')) {
            if (! params.has("type", entry)) {
                url.searchParams.set('type', entry);

                window.location.replace(url.toString());
            } else if (params.get("type") === entry) {
                url.searchParams.delete("type");

                window.location.replace(url.toString());
            }
        }

        if ($(this).hasClass('category')) {
            if (! params.has("category", entry)) {
                url.searchParams.set('category', entry);

                window.location.replace(url.toString());
            } else if (params.get("category") === entry) {
                url.searchParams.delete("category");

                window.location.replace(url.toString());
            }
        }

        if ($(this).hasClass('usage')) {
            if (! params.has("usage", entry)) {
                url.searchParams.set('usage', entry);

                window.location.replace(url.toString());
            } else if (params.get("usage") === entry) {
                url.searchParams.delete("usage");

                window.location.replace(url.toString());
            }
        }
    });

    $('.ecosystem-filter').click(function (e) {
        e.preventDefault();

        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        const entry = $(this).data('value');

        if ($(this).hasClass('keyword')) {
            if (params.has("keywords[]", entry)) {
                params.delete("keywords[]", entry);
                url.search = params.toString();

                window.location.replace(url.toString());
            }
        }
    });

    [...$('#ecosystem-pagination a')].forEach(a => {
        const url = new URL(a.href)
        for (let [k,v] of new URLSearchParams(window.location.search).entries()) {
            if (k === 'keywords[]' || k === 'q' || k === 'type' || k === 'category' || k === 'usage') {
                url.searchParams.set(k,v)
            }
        }
        a.href = url.toString();
    })

    $('#ecosystem-search').keypress(function (e) {
        const search = $(this).val();
        if (e.which === 13) {
            setSearchQuery(search);
        }
    });

    $('#ecosystem-search-btn').click(function (e) {
        const search = $('#ecosystem-search').val();
        setSearchQuery(search);
    });

    function setSearchQuery(search) {
        const url = new URL(window.location.href);

        url.searchParams.set('q', search);
        window.location.replace(url.toString());
    }

    $('#clear-filters-button').click(function (e) {
        const url = new URL(window.location.href);

        for (let [k,v] of new URLSearchParams(window.location.search).entries()) {
            if (k === 'type' || k === 'category' || k === 'usage') {
                url.searchParams.delete(k)
            }
        }

        window.location.replace(url.toString());
    });
});
