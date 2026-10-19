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
