const gulp = require('gulp'),
    watch = require('gulp-watch'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify'),
    compressFiles = [
        'assets/js/*.js', '!assets/js/*.min.js'
    ];

gulp.task('watch', function(){
    watch('**/*.js', ['compress'])
});

gulp.task('compress', function(){
    return gulp.src(compressFiles, {base: "./"})
        // This will output the non-minified version
        .pipe(gulp.dest('.'))
        // This will minify and rename to foo.min.js
        .pipe(uglify())
        .pipe(rename({extname: '.min.js'}))
        .pipe(gulp.dest('.'));
});