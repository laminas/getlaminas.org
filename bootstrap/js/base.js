'use strict';

{
    // Anchors
    anchors.options.placement = 'left';
    anchors.add(
        'article h1, article h2, article h3, article h4, article h5'
    );

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
