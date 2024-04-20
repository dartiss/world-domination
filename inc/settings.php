<?php
/**
 * Settings
 *
 * Add to the setting screen.
 *
 * @package world-domination
 */

/**
 * Add to settings
 *
 * Add a field to the general settings screen for switching the image on/off
 */
function world_domination_settings_init() {

	add_settings_field( 'wd_image_toggle', __( 'Enable World Domination image', 'world-domination' ), 'world_domination_setting_callback', 'general', 'default', array( 'label_for' => 'wd_image_toggle' ) );

	register_setting( 'general', 'wd_image_toggle' );
}

add_action( 'admin_init', 'world_domination_settings_init' );

/**
 * Show image option switch
 *
 * Output the settings field for toggling the image on the dashboard
 */
function world_domination_setting_callback() {

	echo '<label><input name="wd_image_toggle" type="checkbox" value="1" ' . checked( 1, get_option( 'wd_image_toggle', 1 ), false ) . '>&nbsp;&nbsp;' . esc_attr( __( 'Untick to remove the image from the dashboard', 'world-domination' ) ) . '</label>';
}
