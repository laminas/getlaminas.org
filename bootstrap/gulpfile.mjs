import autoprefixer from 'autoprefixer';
import concat from 'gulp-concat';
import cleanCss from 'gulp-clean-css';
import gulp from 'gulp';
import postcss from 'gulp-postcss';
import rev from 'gulp-rev-all';
import revCleaner from 'gulp-rev-dist-clean';
import sass from 'gulp-dart-sass';
import terser from'gulp-terser';

const {series, src, dest, watch} = gulp;

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

function fonts() {
    return src([
        'node_modules/font-awesome/fonts/**.*',
    ])
        .pipe(dest('build/css/fonts'));
}

function revGenerate() {
    return src(['build/css/laminas.css', 'build/js/laminas.js'], {base: 'build'})
        .pipe(rev.revision({ fileNameManifest: "build/assets.json" }))
        .pipe(dest('build/'))
        .pipe(rev.manifestFile())
        .pipe(dest('.'));
}

function revClean() {
    return src(['build/*/*'], {read: false})
        .pipe(revCleaner('build/assets.json', {keepOriginalFiles: false}));
}

function copyAssets() {
  return src(['build/css/*.css', 'build/css/fonts/*', 'build/js/*.js'], {base: 'build'})
        .pipe(dest('../public'));
}

function copyRev() {
  return src('build/assets.json')
        .pipe(dest('../data'));
}

export {
    js,
    css,
    revGenerate,
    revClean,
    copyAssets,
    copyRev,
    fonts
};

/* Primary build task
 * Add items to this series that need to occur when building the final
 * production image.
 */
export const deploy = series(js, fonts, css, revGenerate, revClean);

/* Development build task
 * Add items to this series that need to occur when building assets during
 * development.
 */
export const develop = series(deploy, copyAssets, copyRev);

export default () => {
    watch('scss/*.scss', develop);
};
