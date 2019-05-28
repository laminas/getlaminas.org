var gulp = require('gulp'),
  sass = require('gulp-sass'),
  sourcemaps = require('gulp-sourcemaps'),
  cleanCss = require('gulp-clean-css'),
  rename = require('gulp-rename'),
  postcss = require('gulp-postcss'),
  autoprefixer = require('autoprefixer');

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
        .pipe(gulp.dest('css/'))
        .pipe(cleanCss())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('css/'))
});

gulp.task('copy-css', function () {
    return gulp.src('css/*')
        .pipe(gulp.dest('../public/css'));
});

/* Primary build task
 * Add items to this series that need to occur when building the final
 * production image.
 */
gulp.task('deploy', gulp.series('sass'));

gulp.task('default', gulp.series('deploy', 'copy-css', function () {
    var watcher = gulp.watch('scss/*.scss');
    watcher.on('all', function (event, path, stats) {
        sass();
    });
}));
