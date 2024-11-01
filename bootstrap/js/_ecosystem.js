'use strict';

$(document).ready(function () {
    $('.package-button').click(function (e) {
        e.preventDefault();

        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        const entry = $(this).data('value');

        if ($(this).hasClass('tag')) {
            if (! params.has("tags[]", entry)) {
                params.append("tags[]", entry);
                url.search = params.toString();

                window.location.replace(url.toString());
            }
        }

        if ($(this).hasClass('category')) {
            if (! params.has("categories[]", entry)) {
                params.append("categories[]", entry);
                url.search = params.toString();

                window.location.replace(url.toString());
            }
        }
    });

    $('.ecosystem-filter').click(function (e) {
        e.preventDefault();

        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        const entry = $(this).data('value');

        if ($(this).hasClass('tag')) {
            if (params.has("tags[]", entry)) {
                params.delete("tags[]", entry);
                url.search = params.toString();

                window.location.replace(url.toString());
            }
        }

        if ($(this).hasClass('category')) {
            if (params.has("categories[]", entry)) {
                params.delete("categories[]", entry);
                url.search = params.toString();

                window.location.replace(url.toString());
            }
        }
    });

    [...$('#ecosystem-pagination a')].forEach(a => {
        const url = new URL(a.href)
        for (let [k,v] of new URLSearchParams(window.location.search).entries()) {
            if (k === 'tags[]' || k === 'categories[]' || k === 'q') {
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
});
