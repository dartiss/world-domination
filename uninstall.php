<?php
/**
 * Uninstaller
 *
 * Uninstall the plugin by removing any options from the database
 *
 * @package world-domination
 */

// If the uninstall was not called by WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Remove options.
delete_option( 'world_domination' );
delete_option( 'wd_image_toggle' );
