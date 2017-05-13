'use strict';

var gulp = require('gulp');
var gutil = require('gulp-util');
var runSequence = require('run-sequence').use(gulp);

gulp.task('halfbuild:development', function (cb) {
	runSequence('lint', 'clean', ['javascript', 'styles', 'copy'], cb);
});
gulp.task('halfbuild:production', function (cb) {
	runSequence('lint', 'clean', ['javascript', 'styles', 'copy'], 'clean:production', cb);
});
gulp.task('halfbuild', function (cb) {
	if(gutil.env.env === 'production') {
		runSequence('halfbuild:production', cb);
	} else {
		runSequence('halfbuild:development', cb);
	}
});
