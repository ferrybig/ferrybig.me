'use strict';

var gulp = require('gulp');
var ignore = require('gulp-ignore');

gulp.task('copy', function () {
	return gulp.src('src/static/**/*')
		.pipe(ignore.exclude('js/**/*'))
		.pipe(ignore.exclude('sass/**/*'))
		.pipe(gulp.dest('public'));
});

