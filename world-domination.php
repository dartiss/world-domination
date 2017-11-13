<?php
/*
Plugin Name: World Domination
Plugin URI: https://wordpress.org/plugins/world-domination
Description: Add WordPress market coverage summary to your dashboard.
Version: 1.0.0
Author: David Artiss
Author URI: https://artiss.blog
Text Domain: world-domination
*/

/**
* World Domination
*
* Plugin to add WordPress market coverage summary to dashboard
*
* @package	world-domination
* @since	1.0
*/

/**
* Add meta to plugin details
*
* Add options to plugin meta line
*
* @since	1.0
*
* @param	string  $links	Current links
* @param	string  $file	File in use
* @return   string			Links, now with settings added
*/

function world_domination_plugin_meta( $links, $file ) {

	if ( false !== strpos( $file, 'simple-timed-plugin.php' ) ) { $links = array_merge( $links, array( '<a href="https://wordpress.org/support/plugin/world-domination">' . __( 'Support', 'world-domination' ) . '</a>' ) ); }

	return $links;
}

add_filter( 'plugin_row_meta', 'world_domination_plugin_meta', 10, 2 );

/**
* Add World Domination Data to Dashboard
*
* Screen scrape W3Techs site to get the current usage of WordPress
*
* @since	1.0
*/

function add_wd_data_to_dashboard() {

	$source = 'https://w3techs.com/technologies/details/cm-wordpress/all/all';
	$cache_days = 7;

	// Attempt to fetch data from transient

	$content = get_transient( 'world_domination' );

	// If no transient data exists, fetch the data from the W3Tech site and extract the require percentage

	if ( !$content ) {

		$response = wp_remote_get( $source );

		if ( is_array( $response ) ) {

	  		$text = $response[ 'body' ];

			$pos = strpos( $text, 'we know. This is ' );

			if ( 0 < $pos ) {

				$start = $pos + 17;
				$pos = strpos( $text, '%', $start );

				if ( 0 < $pos ) {

					$length = $pos - $start;
					$content[ 'percent' ] = substr( $text, $start, $length );

					// Now save the result as a transient

					set_transient( 'world_domination', $content, $cache_days * 1440 );
				}
				
			}
		}

	}

	// Output to the dashboard

	echo '<p class="domination-right-now"';

	if ( !isset( $content[ 'percent' ] ) ) {

		echo ' style="color: #f00;">' . __( 'Error fetching the WordPress market data. Please try again later.' , 'world-domination' ) . ' <a href="' . $source . '">' . __( 'Source.', 'world-domination' ) . '</a></p>';

	} else {

		echo '><a href="' . $source . '">' . sprintf( __( 'WordPress is currently used</a> by %s of all websites.' , 'world-domination' ), round( $content[ 'percent' ], 1 ) . '%' ) . '</p>';
	}

	return;

}

add_filter( 'activity_box_end', 'add_wd_data_to_dashboard', 10, 1 );