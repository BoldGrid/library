var gulp = require( 'gulp' );

gulp.task( 'build', function() {
	gulp.src( [ 'node_modules/jquery-toggles/toggles.min.js' ] ).pipe( gulp.dest( './build' ) );
	gulp.src( [ 'node_modules/jquery-toggles/css/toggles-full.css' ] ).pipe( gulp.dest( './build' ) );
} );

gulp.task( 'default', [ 'build' ] );
