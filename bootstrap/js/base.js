'use strict';

{
    // Pre elements - Prism code samples
    const preElements = document.querySelectorAll('pre');
    preElements.forEach(element => {
        if (element.firstElementChild
            && ! element.firstElementChild.classList.contains('language-treeview')
        ) {
            element.classList.add('line-numbers');
        }
    });
}

{
    document.querySelectorAll('.status-button').forEach(button => {
        button.addEventListener('click', handleKeywords);
    })

    function handleKeywords() {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        const keyword = this.dataset.value;

        if (! params.has("status[]", keyword)) {
            params.append("status[]", keyword);
            url.search = params.toString();

            window.location.replace(url.toString());
        }
    }

    document.querySelectorAll('.status-button.active').forEach(button => {
        button.addEventListener('click', removeStatus);
    })

    function removeStatus() {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        const status = this.dataset.value;

        if (params.has("status[]", status)) {
            params.delete("status[]", status);
            url.search = params.toString();

            window.location.replace(url.toString());
        }
    }

    document.querySelector('#clear-filters-button')?.addEventListener('click', function () {
        const url = new URL(window.location.href);

        for (let [k,v] of new URLSearchParams(window.location.search).entries()) {
            if (k !== 'page') {
                url.searchParams.delete(k);
            }
        }

        window.location.replace(url.toString());
    });

    document.querySelector('#package-search-btn').addEventListener('click', function () {
        setSearchQuery(document.querySelector('#package-search').value);
    });

    document.querySelector('#package-search').addEventListener('keypress', function (e) {
        const search = this.value;
        if (e.which === 13) {
            setSearchQuery(search);
        }
    })

    function setSearchQuery(search) {
        const url = new URL(window.location.href);

        url.searchParams.set('q', search);
        window.location.replace(url.toString());
    }
}
