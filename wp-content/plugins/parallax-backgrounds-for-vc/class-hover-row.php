<?php
/**
 * Hover Row routines.
 *
 * @version 1.1
 * @package Parallax Backgrounds for VC
 */

// Initializes the Hover Row functionality.
if ( ! class_exists( 'GambitVCHoverRow' ) ) {

	/**
	 * This is where all the Hover Row functionality happens.
	 */
	class GambitVCHoverRow {
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
			add_shortcode( 'hover_row', array( $this, 'create_shortcode' ) );

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
				'name' => __( 'Hover Row Background', GAMBIT_VC_PARALLAX_BG ),
				'base' => 'hover_row',
				'icon' => plugins_url( 'parallax/images/vc-hover.png', __FILE__ ),
				'description' => __( 'Add a hover bg to your row.', GAMBIT_VC_PARALLAX_BG ),
				'category' => __( 'Row Adjustments', GAMBIT_VC_PARALLAX_BG ),
				'params' => array(
				array(
					'type' => 'attach_image',
					'class' => '',
					'heading' => __( 'Background Image', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'image',
					'description' => __( 'Select your background image. <strong>Make sure that your image is of high resolution, we will resize the image to make it fit.</strong><br><strong>For optimal performance, try keeping your images close to 1600 x 900 pixels</strong>', GAMBIT_VC_PARALLAX_BG ),
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __( 'Hover Type', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'type',
					'value' => array(
						__( 'Move', GAMBIT_VC_PARALLAX_BG ) => 'move',
						__( 'Tilt', GAMBIT_VC_PARALLAX_BG ) => 'tilt',
					),
					'description' => __( 'Choose the type of effect when the row is hovered on.', GAMBIT_VC_PARALLAX_BG ),
				),
				array(
					'type' => 'textfield',
					'class' => '',
					'heading' => __( 'Move/Tilt Amount', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'amount',
					'value' => '30',
					'description' => __( 'The move (pixels) or tilt (degrees) amount when the background is hovered on. For tilt types, the maximum allowed amount is <code>45 degrees</code>', GAMBIT_VC_PARALLAX_BG ),
				),
				array(
					'type' => 'textfield',
					'class' => '',
					'heading' => __( 'Opacity', GAMBIT_VC_PARALLAX_BG ),
					'param_name'  => 'opacity',
					'value' => '100',
					'description' => __( 'You may set the opacity level for your background. You can add a background color to your row and add an opacity here to tint your background. <strong>Please choose an integer value between 1 and 100.</strong>', GAMBIT_VC_PARALLAX_BG ),
				),
				array(
					'type' => 'checkbox',
					'class' => '',
					'heading' => __( 'Invert Move/Tilt Movement', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'inverted',
					'value' => array( __( 'Check this to invert the movement of the effect with regards the direction of the mouse', GAMBIT_VC_PARALLAX_BG ) => 'inverted' ),
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
				'image' => '',
				'type' => 'move',
				'amount' => '30',
				'opacity' => '100',
				'inverted' => '',
				'class' => '',
				'id' => '',
			);
			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );
			$id = '';
			$class = '';

			if ( empty( $atts['image'] ) ) {
				return '';
			}

			wp_enqueue_script( 'gambit_parallax', plugins_url( 'parallax/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_VC_PARALLAX_BG, true );
			wp_enqueue_style( 'gambit_parallax', plugins_url( 'parallax/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_PARALLAX_BG );

			// Jetpack issue, Photon is not giving us the image dimensions.
			// This snippet gets the dimensions for us.
			add_filter( 'jetpack_photon_override_image_downsize', '__return_true' );
			$image_info = wp_get_attachment_image_src( $atts['image'], 'full' );
			remove_filter( 'jetpack_photon_override_image_downsize', '__return_true' );

			$attachment_image = wp_get_attachment_image_src( $atts['image'], 'full' );
			if ( empty( $attachment_image ) ) {
				return '';
			}

			// See if classes and IDs are defined.
			if ( ! empty( $atts['class'] ) ) {
				$class = ' ' . esc_attr( $atts['class'] );
			} else {
				$class = '';
			}
			if ( ! empty( $atts['id'] ) ) {
				$id = "id='" . esc_attr( $atts['id'] ) . "' ";
			} else {
				$id = '';
			}

			$bg_image_width = $image_info[1];
			$bg_image_height = $image_info[2];
			$bg_image = $attachment_image[0];

			return  '<div ' . $id . "class='gambit_hover_row" . $class . "' " .
			"data-bg-image='" . esc_url( $bg_image ) . "' " .
			"data-type='" . esc_attr( $atts['type'] ) . "' " .
			"data-amount='" . esc_attr( $atts['amount'] ) . "' " .
	        "data-opacity='" . esc_attr( $atts['opacity'] ) . "' " .
			"data-inverted='" . esc_attr( empty( $atts['inverted'] ) ? 'false' : 'true' ) . "' " .
			"style='display: none'></div>";
		}
	}

	new GambitVCHoverRow();

}
