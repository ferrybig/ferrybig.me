'use strict';

var gulp = require('gulp');
var gutil = require('gulp-util');
var start = require('gulp-start-process');
var runSequence = require('run-sequence').use(gulp);

gulp.task('content:production', function (cb) {
	start('php src/dynamic/main.php', cb);
});
gulp.task('content:development', function (cb) {
	start('php src/dynamic/main.php', cb);
});
gulp.task('content', function (cb) {
	if (gutil.env.env === 'production') {
		runSequence('content:production', cb);
	} else {
		runSequence('content:development', cb);
	}
});
