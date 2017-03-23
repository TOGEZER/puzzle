<?php
/**
 * Uninstallation routine.
 *
 * @version 1.1
 * @package Parallax Backgrounds for VC
 */

// If uninstall is not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// NOTE: This should correspond to the pointer_name in the main plugin file.
$pointer_name = 'gambitprlx';

// Deletes the dismissed admin pointer for this plugin.
$dismissed_admin_pointers = get_user_meta( get_current_user_id(), 'dismissed_wp_pointers' );
$dismissed_admin_pointers = preg_replace( '/' . $pointer_name . '(,)?)/', null, $dismissed_admin_pointers['0'] );
$dismissed_admin_pointers = preg_replace( '/(,)$/', null, $dismissed_admin_pointers );
update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', $dismissed_admin_pointers );
