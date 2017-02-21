// gulpfile.js
var gulp        = require('gulp');
var browserSync = require('browser-sync').create();
var sass 		= require('gulp-sass');
var concatCss 	= require('gulp-concat-css');
var concat 		= require('gulp-concat');
var sync        = require('gulp-npm-script-sync');

gulp.task('browser-sync', function() {
    browserSync.init({
        proxy: "pokenotifier.lo"
    });
});

gulp.task('watch', ['browser-sync', 'sass', 'scriptmerge'], function () {
    gulp.watch('assets/sass/*.scss', ['sass']);
    gulp.watch('assets/sass/**/*.scss', ['sass']);
    gulp.watch('app/*.js', ['scriptmerge']);
    gulp.watch('app/**/*.js', ['scriptmerge']);
    gulp.watch('assets/css/*.css', browserSync.reload);
    gulp.watch('*.html', browserSync.reload);
    gulp.watch('app/template/*.html', browserSync.reload);
    gulp.watch('app/template/**/*.html', browserSync.reload);
    gulp.watch('app/*.js', browserSync.reload);
    gulp.watch('app/**/*.js', browserSync.reload);
});


gulp.task('default', ['sass', 'scriptmerge']);

gulp.task('sass', function() {
    return gulp.src([
        'node_modules/bootstrap/scss/bootstrap.scss',
        'node_modules/tether/src/css/tether.sass',
        'node_modules/font-awesome/scss/font-awesome.scss',
        'assets/sass/style.scss'
        ])
        .pipe(sass())
        .pipe(concatCss("style.css"))
        .pipe(gulp.dest('assets'));
});


gulp.task('scriptmerge', function() {
    return gulp.src([
        'node_modules/jquery/dist/jquery.js',
        'node_modules/angular/angular.js',
        'node_modules/angular-ui-router/release/angular-ui-router.js',
        'node_modules/angular-animate/angular-animate.js',
        'node-modules/bootstrap/dist/js/bootstrap.js',
        'node_modules/tether/dist/tether.js',
        'app/app.js',
        'app/**/*.js'
        ])
        .pipe(concat('script.js'))
        .pipe(gulp.dest('assets'));
});




sync(gulp, {
  path: 'package.json',
  excluded: ['sass', 'scriptmerge', 'browser-sync', 'default', 'watch']
});
