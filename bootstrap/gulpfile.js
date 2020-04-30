'use strict';

const autoprefixer = require('autoprefixer');
const concat = require('gulp-concat');
const cleanCss = require('gulp-clean-css');
const {series, src, dest, watch} = require('gulp');
const postcss = require('gulp-postcss');
const rev = require('gulp-rev');
const revDel = require('rev-del');
const revCleaner = require('gulp-rev-dist-clean');
const sass = require('gulp-sass');
const terser = require('gulp-terser');

const prism = [
    'core',
    'markup',
    'css',
    'clike',
    'markup-templating',
    'javascript',
    'apacheconf',
    'bash',
    'batch',
    'css-extras',
    'diff',
    'docker',
    'git',
    'handlebars',
    'http',
    'ini',
    'json',
    'less',
    'makefile',
    'markdown',
    'nginx',
    'php',
    'php-extras',
    'powershell',
    'properties',
    'puppet',
    'sass',
    'scss',
    'smarty',
    'sql',
    'twig',
    'vim',
    'yaml'
];

function js() {
    let prismComponents = [];
    prism.forEach(component => prismComponents.push('node_modules/prismjs/components/prism-' + component + '.js'));

    return src(prismComponents.concat([
            'node_modules/prismjs/plugins/normalize-whitespace/prism-normalize-whitespace.min.js',
            'node_modules/prismjs/plugins/line-highlight/prism-line-highlight.min.js',
            'node_modules/prismjs/plugins/line-numbers/prism-line-numbers.min.js',
            'node_modules/prismjs/plugins/treeview/prism-treeview.min.js',
            'node_modules/jquery/dist/jquery.slim.min.js',
            'node_modules/popper.js/dist/umd/popper.min.js',
            'node_modules/popper.js/dist/umd/popper-utils.min.js',
            'node_modules/bootstrap/dist/js/bootstrap.min.js',
            'node_modules/anchor-js/anchor.min.js',
            'js/base.js'
        ]))
        .pipe(concat({path: 'laminas.js'}))
        .pipe(terser({mangle: false}).on('error', function (e) {
            console.log(e);
        }))
        .pipe(dest('build/js'));
}

function css() {
    return src('scss/*.scss')
        .pipe(sass({outputStyle: 'compressed'}))
        .on('error', sass.logError)
        .pipe(postcss([autoprefixer()]))
        .pipe(cleanCss({keepSpecialComments: 0}))
        .pipe(dest('build/css'));
}

function revGenerate() {
    return src(['build/css/laminas.css', 'build/js/laminas.js'], {base: 'build'})
        .pipe(rev())
        .pipe(dest('build/'))
        .pipe(rev.manifest('build/assets.json'))
        .pipe(revDel({
            oldManifest: 'build/assets.json',
            dest: 'build/'
        }))
        .pipe(dest('.'));
}

function revClean() {
    return src(['build/*/*'], {read: false})
        .pipe(revCleaner('build/assets.json', {keepOriginalFiles: false}));
}

function copyAssets() {
  return src(['build/css/*.css', 'build/js/*.js'], {base: 'build'})
        .pipe(dest('../public'));
}

function copyRev() {
  return src('build/assets.json')
        .pipe(dest('../data'));
}

exports.js = js;
exports.css = css;
exports.revGenerate = revGenerate;
exports.revClean = revClean;
exports.copyAssets = copyAssets;
exports.copyRev = copyRev;

/* Primary build task
 * Add items to this series that need to occur when building the final
 * production image.
 */
exports.deploy = series(js, css, revGenerate, revClean);

/* Development build task
 * Add items to this series that need to occur when building assets during
 * development.
 */
exports.develop = series(exports.deploy, copyAssets, copyRev);

exports.default = () => {
    watch('scss/*.scss', exports.develop);
};
