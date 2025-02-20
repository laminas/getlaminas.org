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
