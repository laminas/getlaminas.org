var autoprefixer = require('autoprefixer'),
  cleanCss = require('gulp-clean-css'),
  gulp = require('gulp'),
  postcss = require('gulp-postcss'),
  rename = require('gulp-rename'),
  rev = require('gulp-rev'),
  sass = require('gulp-sass'),
  sourcemaps = require('gulp-sourcemaps');

gulp.task('sass', function () {
    return gulp.src('scss/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .on('error', sass.logError)
        .pipe(postcss([autoprefixer({
            browsers: [
                'Chrome >= 35',
                'Firefox >= 38',
                'Edge >= 12',
                'Explorer >= 10',
                'iOS >= 8',
                'Safari >= 8',
                'Android 2.3',
                'Android >= 4',
                'Opera >= 12']
        })]))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('css'))
        .pipe(cleanCss())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('css'));
});

gulp.task('rev-css', function () {
    return gulp.src('css/*.css')
        .pipe(rev())
        .pipe(gulp.dest('build/css'))
        .pipe(rev.manifest('build/assets.json', {merge: true}))
        .pipe(gulp.dest('.'));
});

gulp.task('copy-css', function () {
  return gulp.src('build/*.css')
        .pipe(gulp.dest('../public/css'));
});

gulp.task('copy-rev', function () {
  return gulp.src('build/assets.json')
        .pipe(gulp.dest('../data'));
});

/* Primary build task
 * Add items to this series that need to occur when building the final
 * production image.
 */
gulp.task('deploy', gulp.series('sass', 'rev-css'));

gulp.task('default', gulp.series('deploy', 'copy-css', 'copy-rev', function () {
    var watcher = gulp.watch('scss/*.scss');
    watcher.on('all', function (event, path, stats) {
        sass();
    });
}));
