<?php
/**
 * Plugin Name: Envira Gallery - Videos Addon
 * Plugin URI:  http://enviragallery.com
 * Description: Displays Youtube, Vimeo, Wistia or self-hosted videos in Lightbox views for Envira galleries
 * Author:      Envira Gallery Team
 * Author URI:  http://enviragallery.com
 * Version:     1.2.8
 * Text Domain: envira-videos
 * Domain Path: languages
 *
 * Envira Gallery is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Envira Gallery is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Envira Gallery. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin class.
 *
 * @since 1.0.0
 *
 * @package Envira_Videos
 * @author  Tim Carr
 */
class Envira_Videos {

	/**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = '1.2.8';

    /**
     * The name of the plugin.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_name = 'Envira Videos';

    /**
     * Unique plugin slug identifier.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_slug = 'envira-videos';

    /**
     * Plugin file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the plugin textdomain.
        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

        // Load the plugin.
        add_action( 'envira_gallery_init', array( $this, 'init' ), 99 );

    }

	/**
     * Loads the plugin textdomain for translation.
     *
     * @since 1.0.0
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain( $this->plugin_slug, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    }

    /**
     * Loads the plugin into WordPress.
     *
     * @since 1.0.0
     */
    public function init() {

        // Load admin only components.
        if ( is_admin() ) {
            $this->require_admin();
        }

        // Load global components.
        $this->require_global();

        // Load the updater
        add_action( 'envira_gallery_updater', array( $this, 'updater' ) );

    }

    /**
     * Loads all admin related files into scope.
     *
     * @since 1.0.0
     */
    public function require_admin() {

		require plugin_dir_path( __FILE__ ) . 'includes/admin/ajax.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/media-view.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/metaboxes.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/vimeo.php';

    }

    /**
	 * Initializes the addon updater.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The user license key.
	 */
	function updater( $key ) {

	    $args = array(
	        'plugin_name' => $this->plugin_name,
	        'plugin_slug' => $this->plugin_slug,
	        'plugin_path' => plugin_basename( __FILE__ ),
	        'plugin_url'  => trailingslashit( WP_PLUGIN_URL ) . $this->plugin_slug,
	        'remote_url'  => 'http://enviragallery.com/',
	        'version'     => $this->version,
	        'key'         => $key
	    );
        
	    $updater = new Envira_Gallery_Updater( $args );

	}

    /**
     * Loads all global files into scope.
     *
     * @since 1.0.0
     */
    public function require_global() {

		require plugin_dir_path( __FILE__ ) . 'includes/global/common.php';
		require plugin_dir_path( __FILE__ ) . 'includes/global/shortcode.php';

    }

     /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Videos object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Videos ) ) {
            self::$instance = new Envira_Videos();
        }

        return self::$instance;

    }

}

// Load the main plugin class.
$envira_videos = Envira_Videos::get_instance();