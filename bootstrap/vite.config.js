import { cpSync, existsSync, mkdirSync, rmSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';

const rootDir = dirname(fileURLToPath(import.meta.url));
const buildDir = resolve(rootDir, 'build');

function cleanBuildOutput() {
    for (const entry of ['css', 'js', 'assets.json']) {
        rmSync(resolve(buildDir, entry), { recursive: true, force: true });
    }
}

function copyBuiltAssets() {
    const publicDir = resolve(rootDir, '../public');
    const dataDir = resolve(rootDir, '../data');

    if (! existsSync(publicDir) || ! existsSync(dataDir)) {
        return;
    }

    mkdirSync(resolve(publicDir, 'css'), { recursive: true });
    mkdirSync(resolve(publicDir, 'js'), { recursive: true });
    cpSync(resolve(buildDir, 'css'), resolve(publicDir, 'css'), { recursive: true });
    cpSync(resolve(buildDir, 'js'), resolve(publicDir, 'js'), { recursive: true });
    cpSync(resolve(buildDir, 'assets.json'), resolve(dataDir, 'assets.json'));
}

// Prism language files and plugins reference a global Prism binding.
function providePrismGlobal() {
    return {
        name: 'provide-prism-global',
        enforce: 'pre',
        transform(code, id) {
            const normalized = id.split('\\').join('/');
            const isPrismAddOn = normalized.includes('/node_modules/prismjs/components/')
                || normalized.includes('/node_modules/prismjs/plugins/');

            if (! isPrismAddOn || code.includes("import Prism from 'prismjs'")) {
                return null;
            }

            return {
                code: `import Prism from 'prismjs';\n${code}`,
                map: null,
            };
        },
    };
}

// PHP resolves css/laminas.css and js/laminas.js through data/assets.json.
function phpAssetManifest() {
    return {
        name: 'php-asset-manifest',
        apply: 'build',
        enforce: 'post',
        buildStart() {
            cleanBuildOutput();
        },
        generateBundle(_options, bundle) {
            const manifest = {};

            for (const file of Object.values(bundle)) {
                if (file.type === 'chunk' && file.isEntry) {
                    manifest['js/laminas.js'] = file.fileName;
                }

                if (file.type === 'asset' && file.fileName.endsWith('.css')) {
                    manifest['css/laminas.css'] = file.fileName;
                }
            }

            if (! manifest['css/laminas.css'] || ! manifest['js/laminas.js']) {
                this.error('Expected compiled css/laminas.css and js/laminas.js in the Vite bundle.');
            }

            this.emitFile({
                type: 'asset',
                fileName: 'assets.json',
                source: `${JSON.stringify(manifest, null, 2)}\n`,
            });
        },
        closeBundle() {
            if (process.argv.includes('--watch')) {
                copyBuiltAssets();
            }
        },
    };
}

export default defineConfig({
    publicDir: false,
    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
            },
        },
    },
    build: {
        outDir: 'build',
        emptyOutDir: false,
        sourcemap: false,
        cssCodeSplit: false,
        modulePreload: false,
        assetsInlineLimit: Number.MAX_SAFE_INTEGER,
        rollupOptions: {
            input: resolve(rootDir, 'js/laminas.js'),
            // Hero images are root-relative files in public/, served by PHP.
            external(id) {
                return id.startsWith('/images/');
            },
            output: {
                format: 'iife',
                name: 'LaminasAssets',
                inlineDynamicImports: true,
                entryFileNames: 'js/laminas.[hash].js',
                assetFileNames(assetInfo) {
                    const name = assetInfo.names?.[0] ?? assetInfo.name ?? '';

                    if (name.endsWith('.css')) {
                        return 'css/laminas.[hash].css';
                    }

                    return 'assets/[name].[hash][extname]';
                },
            },
        },
    },
    plugins: [
        providePrismGlobal(),
        phpAssetManifest(),
    ],
});
