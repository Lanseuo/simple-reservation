const gulp = require('gulp');
const uglify = require('gulp-uglify-es').default;
const concat = require('gulp-concat')
const cleanCSS = require('gulp-clean-css');
const concatCSS = require('gulp-concat-css');

gulp.task('minifyJS', () => {
    gulp.src(['assets/frontend/js/utils.js', 'assets/frontend/js/service.js', 'assets/frontend/js/store.js', 'assets/frontend/js/main.js'])
        .pipe(concat('main.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('assets/frontend/dist'));
});

gulp.task('minifyCSS', () => {
    gulp.src('assets/frontend/css/*.css')
        .pipe(concatCSS('style.min.css'))
        .pipe(cleanCSS({ compatibility: 'ie8' }))
        .pipe(gulp.dest('assets/frontend/dist'));
});

gulp.task('default', ['minifyJS', 'minifyCSS']);

gulp.task('watch', () => {
    gulp.start('default');

    gulp.watch('assets/frontend/js/*.js', ['minifyJS']);
    gulp.watch('assets/frontend/css/*.css', ['minifyCSS']);
});