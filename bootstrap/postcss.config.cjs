// The stylesheet still declares the deprecated "color-adjust" property and does not include "print-color-adjust".
// The auto-prefixer warns on that combination. This plugin runs first and renames "color-adjust"
// to "print-color-adjust",or removes it when the standard property is already there. The auto-prefixer can then
// add the prefixed fallbacks, including "color-adjust" for the older browsers.
const fixColorAdjust = () => ({
    postcssPlugin: 'fix-color-adjust',
    Declaration(decl) {
        if (decl.prop !== 'color-adjust') {
            return;
        }

        const hasStandardProperty = decl.parent.nodes.some(
            (node) => node.prop === 'print-color-adjust'
        );

        if (hasStandardProperty) {
            decl.remove();
            return;
        }

        decl.prop = 'print-color-adjust';
    },
});

fixColorAdjust.postcss = true;

module.exports = {
    plugins: [
        fixColorAdjust(),
        require('autoprefixer')(),
    ],
};
