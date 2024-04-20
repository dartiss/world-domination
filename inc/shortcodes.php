<?php
/**
 * Shortcodes
 *
 * Define the various shortcodes.
 *
 * @package world-domination
 */

/**
 * World Domination total shortcode
 *
 * Shortcode function to display the total market share percentage
 *
 * @return  string           Percentage output.
 */
function world_domination_total_shortcode() {

	$data = world_domination_market_share_data();

	if ( ! $data ) {
		return __( 'N/A', 'world-domination' );
	} else {
		return esc_attr( $data['total'] ) . '%';
	}
}

add_shortcode( 'wp_total_market', 'world_domination_total_shortcode' );

/**
 * World Domination CMS shortcode
 *
 * Shortcode function to display the CMS market share percentage
 *
 * @return string           Percentage output.
 */
function world_domination_cms_shortcode() {

	$data = world_domination_market_share_data();

	if ( ! $data ) {
		return __( 'N/A', 'world-domination' );
	} else {
		return esc_attr( $data['cms'] ) . '%';
	}
}

add_shortcode( 'wp_crm_market', 'world_domination_cms_shortcode' ); // Retained for backwards compatibility!
add_shortcode( 'wp_cms_market', 'world_domination_cms_shortcode' );
