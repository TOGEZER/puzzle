<?php
/**
 * Full Width routines.
 *
 * @version 1.1
 * @package Parallax Backgrounds for VC
 */

// Initializes the Full Width functionality.
if ( ! class_exists( 'GambitVCParallaxFullwidthRow' ) ) {

	/**
	 * This is where all the Full Width functionality happens.
	 */
	class GambitVCParallaxFullwidthRow {
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
			add_shortcode( 'fullwidth_row', array( $this, 'create_shortcode' ) );

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

			global $content_width;

			vc_map( array(
				'name' => __( 'Full-Width Row', GAMBIT_VC_PARALLAX_BG ),
				'base' => 'fullwidth_row',
				'icon' => plugins_url( 'parallax/images/vc-fullwidth.png', __FILE__ ),
				'description' => __( 'Add this to a row to make it full-width.', GAMBIT_VC_PARALLAX_BG ),
				'category' => __( 'Row Adjustments', GAMBIT_VC_PARALLAX_BG ),
				'params' => array(
				array(
					'type' => 'textfield',
					'heading' => __( 'Row Content Width', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'content_width',
					'value' => '',
					'description' => __( 'When your row gets stretched, your content will by default be adjusted to the <strong>content width</strong> defined by your theme. Enter a value here with units (px or %) to adjust the width of your full-width content.<br>e.g. Use <code>100%</code> to stretch your row content to the entire full-width,<br>Use <code>50%</code> to make your content exactly half of the page,<br>Use <code>700px</code> to ensure your content is at maximum 700 pixels wide,<br>or leave <strong>blank</strong> to follow the default row content width,<br>', GAMBIT_VC_PARALLAX_BG ),
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
			global $content_width;

			$defaults = array(
				'content_width' => $content_width,
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

			// We just add a placeholder for this.
			return '<div ' . $id . 'class="gambit_fullwidth_row' . $class . '" data-content-width="' . esc_attr( $atts['content_width'] ) . '" style="display: none"></div>';
		}
	}

	new GambitVCParallaxFullwidthRow();

}
