<?php
/**
 * @copyright (C) Copyright Bobbing Wide 2024
 * @package oik-ai
 *
 * A new version of bobbset.inc for use by a standalone .php file inside a WordPress plugin being run outside of WordPress.
 * Yes, it may be insecure but I'm not bothered about that at the moment since the code's only intended for running in a local development environment.
 * For now (18th Jan 2024) it seems that just including bobbset.inc does the job.
 */

if ( file_exists( "C:/apache/htdocs/bw/bobbset.inc")) {
	require_once('C:/apache/htdocs/bw/bobbset.inc');
	/**
	 * In case we're not in WordPress we need some functions from general-template for BW_::select to work
	 */
	if ( !function_exists( 'selected') ) {
		require_once 'c:/apache/htdocs/wordpress/wp-includes' .'/general-template.php';
		// We can't do this.
		//require_once 'c:/apache/htdocs/wordpress/wp-includes' . '/plugin.php';
		/*
		function selected( $key, $value, $echo ) {
			return __checked_selected_helper( $selected, $current, $echo, 'selected' );
		}
		*/
	}
} else {
	// Not quite a 401 or 403. More a 404.
	echo "Sorry you can't do that!";
	die();
}