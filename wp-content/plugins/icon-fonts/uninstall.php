<?php
/**
 * Uninstall "Icon Fonts" Wordpress plugin
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'icon_fonts_settings' );

// End