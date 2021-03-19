<?php
/**
 * World Domination
 *
 * @package           world-domination
 * @author            David Artiss
 * @license           GPL-2.0-or-later
 *
 * Plugin Name:       World Domination
 * Plugin URI:        https://wordpress.org/plugins/world-domination/
 * Description:       Add WordPress market coverage summary to your dashboard.
 * Version:           2.0.3
 * Requires at least: 4.6
 * Requires PHP:      5.3
 * Author:            David Artiss
 * Author URI:        https://artiss.blog
 * Text Domain:       world-domination
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Add meta to plugin details
 *
 * Add options to plugin meta line
 *
 * @param   string $links  Current links.
 * @param   string $file   File in use.
 * @return  string         Links, now with settings added.
 */
function world_domination_plugin_meta( $links, $file ) {

	if ( false !== strpos( $file, 'plugin_skeleton.php' ) ) {

		$links = array_merge(
			$links,
			array( '<a href="https://github.com/dartiss/world-domination">' . __( 'Github', 'world-domination' ) . '</a>' ),
			array( '<a href="https://wordpress.org/support/plugin/world-domination">' . __( 'Support', 'world-domination' ) . '</a>' ),
			array( '<a href="https://artiss.blog/donate">' . __( 'Donate', 'world-domination' ) . '</a>' ),
			array( '<a href="https://wordpress.org/support/plugin/world-domination/reviews/#new-post">' . __( 'Write a Review', 'world-domination' ) . '&nbsp;⭐️⭐️⭐️⭐️⭐️</a>' )
		);
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'world_domination_plugin_meta', 10, 2 );

/**
 * World Domination total shortcode
 *
 * Shortcode function to display the total market share percentage
 *
 * @param   string $paras    Shortcode parameters.
 * @param   string $content  Content.
 * @return  string           Percentage output.
 */
function world_domination_total_shortcode( $paras = '', $content = '' ) {

	$output = wd_market_share_data( 'total' );

	if ( ! $output ) {
		return __( 'N/A', 'world-domination' );
	} else {
		return esc_attr( $output ) . '%';
	}

}

add_shortcode( 'wp_total_market', 'world_domination_total_shortcode' );

/**
 * World Domination CMS shortcode
 *
 * Shortcode function to display the CMS market share percentage
 *
 * @param  string $paras    Shortcode parameters.
 * @param  string $content  Content.
 * @return string           Percentage output.
 */
function world_domination_cms_shortcode( $paras = '', $content = '' ) {

	$output = wd_market_share_data( 'cms' );

	if ( ! $output ) {
		return __( 'N/A', 'world-domination' );
	} else {
		return esc_attr( $output ) . '%';
	}

}

add_shortcode( 'wp_crm_market', 'world_domination_cms_shortcode' ); // Retained for backwards compatibility!
add_shortcode( 'wp_cms_market', 'world_domination_cms_shortcode' );

/**
 * Add World Domination Data to Dashboard
 *
 * Screen scrape W3Techs site to get the current usage of WordPress.
 *
 * @param  string $shortcode Name of shortcode in use. Or false.
 */
function wd_market_share_data( $shortcode = false ) {

	// Attempt to fetch data from options.
	$cache = get_option( 'world_domination' );

	// Check if data was returned and, if so, had is expired?
	if ( ( ! $cache ) || ( is_array( $cache ) && esc_attr( $cache['timeout'] ) < date( 'U' ) ) ) {

		$source = esc_url( 'https://w3techs.com/technologies/details/cm-wordpress/all/all' );

		$cache_days = 1;

		// If cache was missing or it's expired, fetch fresh data.
		$data  = scrape_wd_data( $source );
		$total = esc_attr( $data['total'] );
		$cms   = esc_attr( $data['cms'] );

		// If a false value is returned, it couldn't be fetched.
		if ( ! $total ) {

			// If there was no saved data then we have nothing to work with here.
			// Otherwise, we can use the stale, saved data.
			if ( is_array( $cache ) ) {
				$total = esc_attr( $cache['percent'] );
				$cms   = esc_attr( $cache['cms'] );
			} else {
				$total = false;
				$cms   = false;
			}

			// If new data was fetched, save it with a new expiry.

		} else {

			$cache['total']   = esc_attr( $total );
			$cache['cms']     = esc_attr( $cms );
			$cache['timeout'] = esc_attr( date( 'U' ) + ( DAY_IN_SECONDS * $cache_days ) );
			$cache['updated'] = esc_attr( date( 'U' ) );
			update_option( 'world_domination', $cache );

		}
	} else {

		// Saved data was found and it hadn't expired. Hurrah!
		$total = esc_attr( $cache['total'] );
		$cms   = esc_attr( $cache['cms'] );

	}

	// Output to the dashboard or return percent.
	if ( 'total' === $shortcode ) {
		return $total;

	} else {

		if ( 'cms' === $shortcode ) {
			return $cms;

		} else {
			echo '<p class="domination-right-now"';

			if ( ! $total ) {

				echo ' style="color: #f00;"><a alt="' . esc_attr( __( 'Link to the source website', 'world-domination' ) ) . '" href="' . esc_url( $source ) . '">' . esc_html( __( 'Error fetching the WordPress market data.', 'world-domination' ) ) . '</a> ' . esc_html( __( 'Please try again later.', 'world-domination' ) ) . '</p>';
			} else {
				/* translators: 1: end of link anchor, 2: total percentage of web use, 3: total percentage of CMSs. */
				echo '><a alt="' . esc_attr( __( 'Link to the source website', 'world-domination' ) ) . '" title="' . esc_attr( __( 'Last checked on ', 'world-domination' ) ) . esc_attr( date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $cache['updated'] ) ) . '" href="' . esc_url( $source ) . '">' . sprintf( esc_html( __( 'WordPress is currently used%1$s by %2$s of all websites and represents %3$s of all CMS usage.', 'world-domination' ) ), '</a>', esc_attr( $total ) . '%', esc_attr( $cms ) . '%' ) . '</p>';
			}
			
			return false;
		}
	}

}

add_filter( 'activity_box_end', 'wd_market_share_data', 10, 1 );

/**
 * Scrape World Domination Data
 *
 * Fetch and extract WordPress market data
 *
 * @param  string $source  URL to extract data.
 * @return string          The percent of market share or FALSE, if all went wrong.
 */
function scrape_wd_data( $source ) {

	$data = array();

	// Fetch the website data.
	$text = get_wd_page_data( $source );

	// If data was found, attempt to extract out the market data that we need.
	$total = false;
	$cms   = false;

	if ( false !== $text ) {

		// Get over all market share.
		$pos = strpos( $text, 'we know. This is ' );

		if ( 0 < $pos ) {

			$start = $pos + 17;
			$pos   = strpos( $text, '%', $start );

			if ( 0 < $pos ) {

				$length = $pos - $start;
				$total  = round( substr( $text, $start, $length ), 1 );

			}
		}

		// Get CMS market share.
		$pos = strpos( $text, 'WordPress is used by ' );

		if ( 0 < $pos ) {

			$start = $pos + 21;
			$pos   = strpos( $text, '%', $start );

			if ( 0 < $pos ) {

				$length = $pos - $start;
				$cms    = round( substr( $text, $start, $length ), 1 );

			}
		}
	}

	// Add results to an array and return.
	$data['total'] = $total;
	$data['cms']   = $cms;
	return $data;

}

/**
 * Fetch data from web page
 *
 * Fetch WordPress market data from supplied page
 *
 * @param  string $source  URL to extract data.
 * @return string          Returned data.
 */
function get_wd_page_data( $source ) {

	if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
		$response = vip_safe_wp_remote_get( $source, '', 3, 3 ); 
	} else {
		$response = wp_remote_get( $source, array( 'timeout' => 3 ) ); // @codingStandardsIgnoreLine -- for non-VIP environments
	}

	if ( is_array( $response ) ) {
		return $response['body'];
	} else {
		return false;
	}
}
