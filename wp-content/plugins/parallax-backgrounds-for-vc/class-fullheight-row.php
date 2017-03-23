<?php
/**
 * Full Height routines.
 *
 * @version 1.1
 * @package Parallax Backgrounds for VC
 */

// Initializes the Full Height functionality.
if ( ! class_exists( 'GambitVCParallaxFullheightRow' ) ) {

	/**
	 * This is where all the Full Height functionality happens.
	 */
	class GambitVCParallaxFullheightRow {
		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {
			// Initialize as a Visual Composer addon.
			add_filter( 'init', array( $this, 'create_row_shortcodes' ), 999 );

			// Makes the plugin function accessible as a shortcode.
			add_shortcode( 'fullheight_row', array( $this, 'create_shortcode' ) );

			// Our admin-side scripts & styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}


		/**
		 * Includes admin scripts and styles needed.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function admin_enqueue_scripts() {
			wp_enqueue_style( 'gambit_parallax_admin', plugins_url( 'parallax/css/admin.css', __FILE__ ), array(), VERSION_GAMBIT_VC_PARALLAX_BG );
		}


		/**
		 * Creates our shortcode settings in Visual Composer.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function create_row_shortcodes() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			vc_map( array(
				'name' => __( 'Full-Height Row', GAMBIT_VC_PARALLAX_BG ),
				'base' => 'fullheight_row',
				'icon' => plugins_url( 'parallax/images/vc-fullheight.png', __FILE__ ),
				'description' => __( 'Add this to a row to make it full-height.', GAMBIT_VC_PARALLAX_BG ),
				'category' => __( 'Row Adjustments', GAMBIT_VC_PARALLAX_BG ),
				'params' => array(
				array(
					'type' => 'dropdown',
					'heading' => __( 'Row Content Location', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'content_location',
					'value' => array(
						__( 'Center', GAMBIT_VC_PARALLAX_BG ) => 'center',
						__( 'Top', GAMBIT_VC_PARALLAX_BG ) => 'top',
						__( 'Bottom', GAMBIT_VC_PARALLAX_BG ) => 'bottom',
					),
					'description' => __( 'When your row height gets stretched, your content can be smaller than your row height. Choose the location here.<br><br><em>Please remove your row&apos;s top and bottom margins to make this work correctly.</em>', GAMBIT_VC_PARALLAX_BG ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Custom ID', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'id',
					'value' => '',
					'description' => __( 'Add a custom id for the element here. Only one ID can be defined.', GAMBIT_VC_PARALLAX_BG ),
					'group' => __( 'Advanced', GAMBIT_VC_PARALLAX_BG ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Custom Class', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'class',
					'value' => '',
					'description' => __( 'Add a custom class name for the element here. If defining multiple classes, separate them by lines and define them like you would in HTML code.', GAMBIT_VC_PARALLAX_BG ),
					'group' => __( 'Advanced', GAMBIT_VC_PARALLAX_BG ),
				),
				),
			) );
		}


		/**
		 * Shortcode logic.
		 *
		 * @param array  $atts - The attributes of the shortcode.
		 * @param string $content - The content enclosed inside the shortcode if any.
		 * @return string - The rendered html.
		 * @since 1.0
		 */
		public function create_shortcode( $atts, $content = null ) {
			$defaults = array(
				'content_location' => 'center',
				'class' => '',
				'id' => '',
			);
			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );
			$id = '';
			$class = '';

			// See if classes and IDs are defined.
			if ( ! empty( $atts['class'] ) ) {
				$class = ' ' . esc_attr( $atts['class'] );
			} else {
				$class = '';
			}
			if ( ! empty( $atts['id'] ) ) {
				$id = 'id="' . esc_attr( $atts['id'] ) . '" ';
			} else {
				$id = '';
			}

			wp_enqueue_script( 'gambit_parallax', plugins_url( 'parallax/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_VC_PARALLAX_BG, true );
			wp_enqueue_style( 'gambit_parallax', plugins_url( 'parallax/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_PARALLAX_BG );

			// We just add a placeholder for this.
			return '<div ' . $id . 'class="gambit_fullheight_row' . $class . '" data-content-location="' . esc_attr( $atts['content_location'] ) . '" style="display: none"></div>';
		}
	}

	new GambitVCParallaxFullheightRow();

}
