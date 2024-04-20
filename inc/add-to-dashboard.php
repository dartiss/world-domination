<?php
/**
 * Add to Dashboard
 *
 * Generated stats and add them to the dashboard.
 *
 * @package world-domination
 */

/**
 * Add market data to Dashboard
 *
 * Grab market share data and then output to dashboard.
 */
function world_domination_add_to_dashboard() {

	$data = world_domination_market_share_data();

	$total   = $data['total'];
	$cms     = $data['cms'];
	$updated = $data['updated'];

	$source = 'https://w3techs.com/technologies/details/cm-wordpress';

	echo '<p class="domination-right-now"';

	if ( ! $total ) {

		echo ' style="color: #f00;"><a alt="' . esc_attr( __( 'Link to the source website', 'world-domination' ) ) . '" href="' . esc_url( $source ) . '">' . esc_html( __( 'Error fetching the WordPress market data.', 'world-domination' ) ) . '</a> ' . esc_html( __( 'Please try again later.', 'world-domination' ) ) . '</p>';
	} else {

		/* translators: 1: end of link anchor, 2: total percentage of web use, 3: total percentage of CMSs. */
		echo '><a alt="' . esc_attr( __( 'Link to the source website', 'world-domination' ) ) . '" title="' . esc_attr( __( 'Last checked on ', 'world-domination' ) ) . esc_attr( gmdate( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $updated ) ) . '" href="' . esc_url( $source ) . '">' . sprintf( esc_html( __( 'WordPress is currently used%1$s by %2$s of all websites and represents %3$s of all CMS usage.', 'world-domination' ) ), '</a>', esc_attr( $total ) . '%', esc_attr( $cms ) . '%' ) . '</p>';

		if ( '' !== get_option( 'wd_image_toggle', 1 ) ) {
			// Create a class for converting the percent to words. Use the blog's current language setting for which to use.
			$format = new NumberFormatter( get_bloginfo( 'language' ), NumberFormatter::SPELLOUT );
			/* translators: s: percentage of market */
			echo '<p><img src="' . esc_url( plugin_dir_url( __DIR__ ) ) . 'wp-somethingty-percent/wpbokeh-somethingty.php?hash=' . esc_attr( gmdate( 'Ymd' ) ) . '&hi=' . esc_attr( $format->format( $total ) ) . '&lo=' . esc_attr__( 'percent of the internet', 'world-domination' ) . '" style="width:100%"></p>';
		}
	}

	return true;
}

add_filter( 'activity_box_end', 'world_domination_add_to_dashboard', 10, 1 );

/**
 * Grab market share data
 *
 * Screen scrape W3Techs site to get the current usage of WordPress.
 */
function world_domination_market_share_data() {

	// Attempt to fetch data from options.
	$cache = get_option( 'world_domination' );

	// Check if data was returned and, if so, had is expired?
	if ( ( ! $cache ) || ( is_array( $cache ) && esc_attr( $cache['timeout'] ) < gmdate( 'U' ) ) ) {

		// Number of days that cache lasts for, as well as days that data can be considered fresh.
		$cache_days  = 1;
		$data_expiry = 7;

		// If cache was missing or it's expired, fetch fresh data.
		$data  = world_domination_scrape_data( 'https://w3techs.com/technologies/details/cm-wordpress' );
		$total = esc_attr( $data['total'] );
		$cms   = esc_attr( $data['cms'] );

		// If a false value is returned, it couldn't be fetched.
		if ( ! $total ) {

			// If there was no saved data then we have nothing to work with here.
			// Otherwise, we can use the stale, saved data.
			if ( is_array( $cache ) ) {
				$total = esc_attr( $cache['percent'] );
				$cms   = esc_attr( $cache['cms'] );
				// Check if the retrieved update date > 7 days. If so, stop using it and error.
				if ( $cache['updated'] < gmdate( 'U' ) - ( DAY_IN_SECONDS * $data_expiry ) ) {
					$cache['total'] = false;
					$cache['cms']   = false;
				}
			} else {
				$cache['total'] = false;
				$cache['cms']   = false;
			}
		} else {

			// If new data was fetched, save it with a new expiry.

			$cache = array();

			$cache['total']   = esc_attr( $total );
			$cache['cms']     = esc_attr( $cms );
			$cache['timeout'] = esc_attr( gmdate( 'U' ) + ( DAY_IN_SECONDS * $cache_days ) );
			$cache['updated'] = esc_attr( gmdate( 'U' ) );

			update_option( 'world_domination', $cache );

		}
	}

	return $cache;
}

/**
 * Scrape World Domination Data
 *
 * Fetch and extract WordPress market data
 *
 * @param  string $source  URL to extract data.
 * @return string          The percent of market share or FALSE, if all went wrong.
 */
function world_domination_scrape_data( $source ) {

	$data = array();

	// Fetch the website data.
	$text = world_domination_get_file( $source );

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
 * Fetch a URL
 *
 * Fetch a file based on a supplier URL. WordPress VIP friendly.
 *
 * @param  string $source  URL to extract data.
 * @return string          Returned data.
 */
function world_domination_get_file( $source ) {

	if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
		$response = vip_safe_wp_remote_get( $source, '', 3, 3 );
	} else {
		$response = wp_remote_get( $source, array( 'timeout' => 3 ) ); // @codingStandardsIgnoreLine -- for non-VIP environments
	}

	if ( is_array( $response ) ) {
		return wp_strip_all_tags( $response['body'] );
	} else {
		return false;
	}
}
