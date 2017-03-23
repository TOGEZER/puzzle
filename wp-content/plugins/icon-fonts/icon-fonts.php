<?php
/*
Plugin Name: Icon Fonts
Plugin URI: http://wordpress.org/plugins/icon-fonts
Description: This plugin adds support for 18 free icon fonts (over 6000 icons). The supported fonts are Dashicons, Elegant, Elusive, Entypo, Font Awesome, Foundation, Genericons, IcoMoon Free, Ionicons, Map Icons, Material Design, MFG Labs, Octicons, Open Iconic, OpenWeb, Sosa, Themify, and Typicons.
Version: 1.0.0
Author: sydcode
Author URI: http://profiles.wordpress.org/sydcode
License: GPLv2 or later

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Load class
if( class_exists( 'IconFonts' ) ) {
	add_action( 'plugins_loaded', array( IconFonts::get_instance(), 'setup' ) );
}

class IconFonts {

	const PLUGIN_SLUG = 'icon-fonts';
	const PLUGIN_VERSION = '1.0.0';
	protected static $instance = null;
	protected static $fonts = array( 
		'dashicons' => array( 'name' => 'Dashicons', 'class' => 'dashicons' ),
		'elegant' => array( 'name' => 'Elegant', 'class' => '' ),
		'elusive' => array( 'name' => 'Elusive', 'class' => 'el' ),
		'entypo' => array( 'name' => 'Entypo', 'class' => 'entypo' ),
		'font-awesome' => array( 'name' => 'Font Awesome', 'class' => 'fa' ),
		'foundation' => array( 'name' => 'Foundation', 'class' => '' ),
		'genericons' => array( 'name' => 'Genericons', 'class' => 'genericon' ),
		'icomoon-free' => array( 'name' => 'Icomoon Free', 'class' => 'icon' ),
		'ionicons' => array( 'name' => 'Ionicons', 'class' => 'icon' ),
		'map-icons' => array( 'name' => 'Map Icons', 'class' => '' ),
		'material-design' => array( 'name' => 'Material Design', 'class' => 'mdi' ),
		'mfglabs' => array( 'name' => 'MFG Labs', 'class' => '' ),
		'octicons' => array( 'name' => 'Octicons', 'class' => 'octicon' ),
		'open-iconic' => array( 'name' => 'Open Iconic', 'class' => 'oi' ),
		'openweb' => array( 'name' => 'Openweb', 'class' => '' ),
		'sosa' => array( 'name' => 'Sosa', 'class' => '' ),
		'themify' => array( 'name' => 'Themify', 'class' => '' ),
		'typicons' => array( 'name' => 'Typicons', 'class' => 'typcn' )
	);	

	/**
	 * Constructor
	 */
	function __construct() {}
	
	/**
	 * Get Instance
	 * @return object
	 */	
	public static function get_instance() {
		null === self::$instance and self::$instance = new self;
	 	return self::$instance;
	}
	
	/**
	 * Setup actions and filters
	 */	
	public function setup() {
		// Actions
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_menu', array( $this, 'settings_menu' ) );	
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		
		// Filters
		add_filter( 'mce_css', array( $this, 'mce_css' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		add_filter( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ) );
	}	
	
	/**
	 * Register settings
	 */
	public function admin_init() {
		load_plugin_textdomain( 'icon-fonts', false, plugin_basename( __FILE__ ) . '/languages' );
		register_setting( 'icon_fonts_settings_group', 'icon_fonts_settings' );
		add_settings_section( 'icon_fonts_settings_section', '', array( $this, 'settings_section' ), 'icon_fonts_settings_page' );
		// Add button to visual editor
		if( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			if( 'true' == get_user_option( 'rich_editing' ) ) {
				add_filter( 'mce_external_plugins', array( $this, 'add_editor_plugin' ) );
				add_filter( 'mce_buttons', array( $this, 'register_editor_button' ) );
			}
		}		
	}
	
	/**
	 * Add settings properties for editor dialog
	 */
	public function admin_head() {
		$settings = get_option( 'icon_fonts_settings' );
		
		// Fonts
		$items = array();
		foreach( self::$fonts as $font => $data ) {
			if( 'dashicons' == $font || ! empty( $settings[ $font ] ) ) {
				$item = array();
				$item['text'] = $data['name'];
				$item['value'] = $font;
				$items[] = $item;
			}
		}
		$fonts = json_encode( $items );
		
		// Names
		$url = plugins_url( '/icon-names.json', __FILE__ );
		$json = file_get_contents( $url );
		$items = json_decode( $json, true );
		foreach( self::$fonts as $font => $text ) {
			if( 'dashicons' != $font && empty( $settings[ $font ] ) ) {
				unset( $items[ $font ] );
			}
		}
		$names = json_encode( $items );
		
		// Classes
		$items = array();
		if( ! empty( $settings['classes'] ) ) {
			$extra = $settings['classes'];
		} else {
			$extra = '';
		}
		foreach( self::$fonts as $font => $data ) {
			if( 'dashicons' == $font || ! empty( $settings[ $font ] ) ) {
				if( ! empty( $data['class'] ) ) {
					$items[$font] = trim( $extra . ' ' . $data['class'] );
				} else {
					$items[$font] = $extra;
				}
			}
		}
		$classes = json_encode( $items );

		// Styles
		if( ! empty( $settings['styles'] ) ) {
			$styles = $settings['styles'];
		} else {
			$styles = '';
		}
		if( ! empty( $settings['color'] ) ) {
			$styles .= ' ' . sprintf( 'color: %s;', $settings['color'] );
		}
		if( ! empty( $settings['size'] ) ) {
			$styles .= ' ' . sprintf( 'font-size: %spx;', $settings['size'] );
		}		
		$styles = trim( $styles );
		
		// Output properties
		$string = <<<CODE
<script type='text/javascript'>
	var iconFonts = {
		currentFont : 'dashicons',
		currentName : '',
		Fonts : {$fonts},
		Names : {$names},
		Classes : {$classes},
		Styles: '{$styles}',
	}
</script>
CODE;
		print( $string . PHP_EOL );
	}	
	
	/**
	 * Add TinyMCE plugin for editor button
	 * @param array $plugin_array
	 * @return array	
	 */		
	function add_editor_plugin( $plugin_array ) {
		$plugin_array['icon_fonts'] = plugins_url( '/icon-fonts-button.js', __FILE__ );
		return $plugin_array;
	}

	/**
	 * Register editor button
	 * @param array $$buttons
	 * @return array	
	 */		
	function register_editor_button( $buttons ) {
		array_push( $buttons, 'icon_fonts' );
		return $buttons;
	}	
	
	/**
	 * Add font stylesheets to TinyMCE editor
	 * @param string $mce_css
	 * @return string	
	 */	
	public function mce_css( $mce_css ) {
		$settings = get_option( 'icon_fonts_settings' );
		foreach( self::$fonts as $font => $data ) {
			// Skip Dashicons because it's loaded by Wordpress
			if( 'dashicons' != $font && ! empty( $settings[ $font ] ) ) {
				if( ! empty( $mce_css ) ) {
					$mce_css .= ',';
				}			
				$mce_css .= $this->font_stylesheet_url( $font );
			}
		}	
		return $mce_css;
	}
	
	/**
	 * Create action link
	 * @param array $links
	 * @return array	
	 */	
	public function plugin_action_links( $links ) { 
		$link = "<a href='options-general.php?page=" . self::PLUGIN_SLUG . "'>Settings</a>"; 
		array_unshift($links, $link); 
		return $links; 
	}	

	/**
	 * Create settings menu
	 */
	public function settings_menu() {
		add_options_page(
			__( 'Icon Fonts Settings', self::PLUGIN_SLUG),
			__( 'Icon Fonts', self::PLUGIN_SLUG), 
			'manage_options', 
			self::PLUGIN_SLUG, 
			array($this, 'settings_page' )
		);
	}

	/**
	 * Load template for settings page
	 * @return string	 
	 */
	public function settings_page() {
		if( ! current_user_can( 'manage_options' ) ) { 
			wp_die( __( 'You do not have sufficient permissions to access this page' ) );
		} else {
			include( 'settings.php' );
		}
	}

	/**
	 * Register settings fields 
	 */
	public function settings_section() {
		add_settings_field(
			'icon_fonts_settings_fonts', 
			__( 'Fonts', self::PLUGIN_SLUG), 
			function() { include( 'settings-fonts.php' ); },
			'icon_fonts_settings_page', 
			'icon_fonts_settings_section'
		); 
		add_settings_field(
			'icon_fonts_settings_stylesheets', 
			__( 'Stylesheets', self::PLUGIN_SLUG), 
			function() { include( 'settings-stylesheets.php' ); },
			'icon_fonts_settings_page', 
			'icon_fonts_settings_section'
		);
		add_settings_field(
			'icon_fonts_settings_format', 
			__( 'Format', self::PLUGIN_SLUG), 
			function() { include( 'settings-format.php' ); },
			'icon_fonts_settings_page', 
			'icon_fonts_settings_section'
		); 			
	}
	
	/**
	 * Enqueue button stylesheet or color picker script
	 */	
	public function admin_enqueue_scripts( $hook ) {
    if( 'edit.php' == $hook || 'post.php' == $hook || 'post-new.php' == $hook ) {
			// Add button stylesheet to TinyMCE editor
      $src = plugins_url( 'icon-fonts-button.css' , __FILE__ );
			wp_enqueue_style( 'icon-fonts-button', $src, array(), self::PLUGIN_VERSION );
    }	
		if( 'settings_page_icon-fonts' == $hook ) {
			// Add color picker script to plugin settings
			wp_enqueue_style( 'wp-color-picker' );
			$src = plugins_url( 'settings-color-picker.js' , __FILE__ );
			wp_enqueue_script( 'icon-fonts-color-picker', $src, array( 'wp-color-picker' ), false, true );
		}
	}

	/**
	 * Add aria-hidden to allowed attributes for editor
	 */	
	public function tiny_mce_before_init( $options ) {
		$ext = 'span[*]';
		if ( ! isset( $options['extended_valid_elements'] ) ) {
			$options['extended_valid_elements'] = $ext;
		} else {
			$options['extended_valid_elements'] .= ',' . $ext;
		}
		if ( ! isset( $options['custom_elements'] ) ) {
			$options['custom_elements'] = $ext;
		} else {
			$options['custom_elements'] .= ',' . $ext;
		}
		return $options;	
	}		
	
	/**
	 * Enqueue stylesheets for icon fonts
	 */	
	public function wp_enqueue_scripts() {
		$settings = get_option( 'icon_fonts_settings' );
		foreach( self::$fonts as $font => $data ) {
			// Skip Dashicons because it's loaded by Wordpress
			if( 'dashicons' != $font && ! empty( $settings[ $font ] ) ) {
				$src = $this->font_stylesheet_url( $font );
				wp_enqueue_style( $font . '-font', $src, array(), self::PLUGIN_VERSION ); 
			}
		}
	}
	
	/**
	 * Get URL of font stylesheet
	 * @param string $font
	 * @return string		 
	 */	
	public function font_stylesheet_url( $font ) {
		// Get settings for font stylesheets
		$settings = get_option( 'icon_fonts_settings' );
		$extension = ( ! empty( $settings[ 'minimised' ] ) ) ? '.min.css' : '.css';
		$framework = ( ! empty( $settings[ 'framework' ] ) ) ? $settings[ 'framework' ] : '';

		// Return URL
		switch( $font ) {
			case 'elegant':
				return plugins_url( 'elegant/style' . $extension , __FILE__ );
			case 'elusive':
				return plugins_url( 'elusive/css/elusive-icons' . $extension , __FILE__ );				
			case 'entypo':
				return plugins_url( 'entypo/entypo' . $extension, __FILE__ );
			case 'font-awesome':
				return plugins_url( 'font-awesome/css/font-awesome' . $extension, __FILE__ );
			case 'foundation':
				return plugins_url( 'foundation/foundation-icons' . $extension, __FILE__ );
			case 'genericons':
				return plugins_url( 'genericons/genericons' . $extension, __FILE__ );
			case 'icomoon-free':
				return plugins_url( 'icomoon-free/style' . $extension, __FILE__ );
			case 'ionicons':
				return plugins_url( 'ionicons/css/ionicons' . $extension, __FILE__ );
			case 'material-design':
				return plugins_url( 'material-design/css/materialdesignicons' . $extension, __FILE__ );				
			case 'map-icons':
				return plugins_url( 'map-icons/css/map-icons' . $extension, __FILE__ );
			case 'mfglabs':
				return plugins_url( 'mfglabs/mfglabs_iconset' . $extension, __FILE__ );
			case 'octicons':
				return plugins_url( 'octicons/octicons' . $extension, __FILE__ );				
			case 'open-iconic':
				if( 'bootstrap' == $framework ) {
					$file = 'open-iconic-bootstrap';
				} elseif( 'foundation' == $framework ) {
					$file = 'open-iconic-foundation';
				} else {
					$file = 'open-iconic';
				}
				return plugins_url( 'open-iconic/css/' . $file . $extension, __FILE__ );
			case 'openweb':
				if( 'bootstrap' == $framework ) {
					$file = 'openwebicons-bootstrap';
				} else {
					$file = 'openwebicons';
				}						
				return plugins_url( 'openweb/css/' . $file . $extension, __FILE__ );
			case 'sosa':
				return plugins_url( 'sosa/style' . $extension, __FILE__ );
			case 'themify':
				return plugins_url( 'themify/themify-icons' . $extension, __FILE__ );
			case 'typicons':
				return plugins_url( 'typicons/typicons' . $extension, __FILE__ );
			default:
				return '';
		}
	}
	
} // End class