<?php
$path  = '';

if ( ! defined('WP_LOAD_PATH') ) {

	$classic_root = dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/';

	if ( file_exists( $classic_root . 'wp-load.php' ) ) {
		define( 'WP_LOAD_PATH', $classic_root );
	} else {
		if ( file_exists( $path . 'wp-load.php' ) ) {
			define( 'WP_LOAD_PATH', $path );
		} else {
			exit( "Could not find wp-load.php" );
		}
	}
}
require_once( WP_LOAD_PATH . 'wp-load.php');
