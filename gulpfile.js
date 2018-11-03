const gulp = require('gulp');
const uglify = require('gulp-uglify-es').default;
const concat = require('gulp-concat')
const cleanCSS = require('gulp-clean-css');
const concatCSS = require('gulp-concat-css');

gulp.task('minifyFrontendJS', () => {
    gulp.src(['assets/frontend/js/utils.js', 'assets/frontend/js/service.js', 'assets/frontend/js/store.js', 'assets/frontend/js/main.js'])
        .pipe(concat('main.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('assets/frontend/dist'));
});

gulp.task('minifyFrontendCSS', () => {
    gulp.src('assets/frontend/css/*.css')
        .pipe(concatCSS('style.min.css'))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('assets/frontend/dist'));
});

gulp.task('minifyAdminJS', () => {
    gulp.src(['assets/admin/js/main.js'])
        .pipe(concat('main.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('assets/admin/dist'));
});

gulp.task('minifyAdminCSS', () => {
    gulp.src('assets/admin/css/*.css')
        .pipe(concatCSS('style.min.css'))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('assets/admin/dist'));
});

gulp.task('default', ['minifyFrontendJS', 'minifyFrontendCSS', 'minifyAdminJS', 'minifyAdminCSS']);

gulp.task('watch', () => {
    gulp.start('default');

    gulp.watch('assets/frontend/js/*.js', ['minifyFrontendJS']);
    gulp.watch('assets/frontend/css/*.css', ['minifyFrontendCSS']);

    gulp.watch('assets/admin/js/*.js', ['minifyAdminJS']);
    gulp.watch('assets/admin/css/*.css', ['minifyAdminCSS']);
});