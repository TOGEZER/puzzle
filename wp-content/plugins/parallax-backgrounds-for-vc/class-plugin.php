<?php
/**
Plugin Name: Parallax Backgrounds for VC
Description: Adds new options to Visual Composer for adding parallax scrolling images & video backgrounds.
Author: Gambit Technologies, Inc.
Version: 4.4.1
Author URI: http://gambit.ph
Plugin URI: http://gambit.ph/downloads/parallax-backgrounds-for-vc/
Text Domain: parallax
Domain Path: /languages
SKU: PRLX
 *
 * @package Parallax Backgrounds for VC
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_VC_PARALLAX_BG' ) or define( 'VERSION_GAMBIT_VC_PARALLAX_BG', '4.4.1' );

// The slug used for translations & other identifiers.
defined( 'GAMBIT_VC_PARALLAX_BG' ) or define( 'GAMBIT_VC_PARALLAX_BG', 'parallax' );

// Plugin automatic updates.
require_once( 'class-admin-license.php' );

// Loads all the modules related to the plugin.
require_once( 'class-fullwidth-row.php' );
require_once( 'class-fullheight-row.php' );
require_once( 'class-parallax-row.php' );
require_once( 'class-video-row.php' );
require_once( 'class-hover-row.php' );
require_once( 'class-background-row.php' );
require_once( 'class-color-cycle-bg.php' );

// Initializes plugin class.
if ( ! class_exists( 'GambitVCParallaxBackgrounds' ) ) {

	/**
	 * Parallax Background Class.
	 *
	 * @since	1.0
	 */
	class GambitVCParallaxBackgrounds {

		/**
		 * Constructor, checks for Visual Composer and defines hooks.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Admin pointer reminders for automatic updates.
			require_once( 'class-admin-pointers.php' );
			if ( class_exists( 'GambitAdminPointers' ) ) {
				new GambitAdminPointers( array(
					'pointer_name' => 'gambitprlx', // This should also be placed in uninstall.php.
					'header' => __( 'Automatic Updates', GAMBIT_VC_PARALLAX_BG ),
					'body' => __( 'Keep your Parallax Backgrounds for VC plugin updated by entering your purchase code here.', GAMBIT_VC_PARALLAX_BG ),
				) );
			}

			// Our translations.
			add_action( 'plugins_loaded', array( $this, 'load_text_domain' ), 1 );

			// Gambit links.
			add_filter( 'plugin_row_meta', array( $this, 'plugin_links' ), 10, 2 );

			// Add plugin specific filters and actions here.
			add_action( 'wp_head', array( $this, 'ie9_detector' ) );
		}


		/**
		 * Enables legacy Internet Explorer 9 compatibility.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function ie9_detector() {
			echo '<!--[if IE 9]> <script>var _gambitParallaxIE9 = true;</script> <![endif]-->';
		}


		/**
		 * Loads the translations.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function load_text_domain() {
			load_plugin_textdomain( GAMBIT_VC_PARALLAX_BG, false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Adds plugin links.
		 *
		 * @access public
		 * @param array  $plugin_meta - The current array of links.
		 * @param string $plugin_file - The plugin file.
		 * @return array - The current array of links together with our additions.
		 * @since	2.6
		 **/
		public function plugin_links( $plugin_meta, $plugin_file ) {
			if ( plugin_basename( __FILE__ ) == $plugin_file ) {
				$plugin_data = get_plugin_data( __FILE__ );

				$plugin_meta[] = sprintf( "<a href='%s' target='_blank'>%s</a>",
					'http://support.gambit.ph?utm_source=' . urlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get Customer Support', GAMBIT_VC_PARALLAX_BG )
				);
				$plugin_meta[] = sprintf( "<a href='%s' target='_blank'>%s</a>",
					'http://codecanyon.net/user/GambitTech/portfolio?utm_source=' . urlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get More Plugins', GAMBIT_VC_PARALLAX_BG )
				);
			}
			return $plugin_meta;
		}
	}


	new GambitVCParallaxBackgrounds();
}
