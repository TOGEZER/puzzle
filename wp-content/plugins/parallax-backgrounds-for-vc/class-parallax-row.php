<?php
/**
 * Parallax Row routines.
 *
 * @version 1.1
 * @package Parallax Backgrounds for VC
 */

// Initializes the Parallax Row functionality.
if ( ! class_exists( 'GambitVCParallaxRow' ) ) {

	/**
	 * This is where all the Parallax Row functionality happens.
	 */
	class GambitVCParallaxRow {
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
			add_shortcode( 'parallax_row', array( $this, 'create_shortcode' ) );

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
				'name' => __( 'Parallax Row Background', GAMBIT_VC_PARALLAX_BG ),
				'base' => 'parallax_row',
				'icon' => plugins_url( 'parallax/images/vc-parallax.png', __FILE__ ),
				'description' => __( 'Add a parallax bg to your row.', GAMBIT_VC_PARALLAX_BG ),
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
					'heading' => __( 'Background Image Parallax', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'direction',
					'value' => array(
						'Up' => 'up',
						'Down' => 'down',
						'Left' => 'left',
						'Right' => 'right',
						'Fixed' => 'fixed',
					),
					'description' => __( "Choose the direction of your parallax. <strong>Note that browsers render fixed directions very slow since they aren't hardware accelerated.</strong>", GAMBIT_VC_PARALLAX_BG ),
				),
				array(
					'type' => 'textfield',
					'class' => '',
					'heading' => __( 'Parallax Speed', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'speed',
					'value' => '0.3',
					'description' => __( 'The movement speed, value should be between 0.1 and 1.0. A lower number means slower scrolling speed.', GAMBIT_VC_PARALLAX_BG ),
	                'dependency' => array(
	                    'element' => 'direction',
	                    'value' => array( 'up', 'down', 'left', 'right' ),
	                ),
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __( 'Background Style / Repeat', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'background_repeat',
					'value' => array(
						__( 'Cover Whole Row (covers the whole row)', GAMBIT_VC_PARALLAX_BG ) => '',
						__( 'Repeating Image Pattern', GAMBIT_VC_PARALLAX_BG ) => 'repeat',
					),
					'description' => __( 'Select whether the background image above should cover the whole row, or whether the image is a background seamless pattern.', GAMBIT_VC_PARALLAX_BG ),
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __( 'Background Position / Alignment', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'background_position',
					'value' => array(
						__( 'Centered', GAMBIT_VC_PARALLAX_BG ) => '',
						__( 'Left (only applies to up, down parallax or fixed)', GAMBIT_VC_PARALLAX_BG ) => 'left',
						__( 'Right (only applies to up, down parallax or fixed)', GAMBIT_VC_PARALLAX_BG ) => 'right',
						__( 'Top (only applies to left or right parallax)', GAMBIT_VC_PARALLAX_BG ) => 'top',
						__( 'Bottom (only applies to left or right parallax)', GAMBIT_VC_PARALLAX_BG ) => 'bottom',
					),
					'description' => __( 'The alignment of the background / parallax image. Note that this most likely will only be noticeable in smaller screens, if the row is large enough, the image will most likely be fully visible. Use this if you want to ensure that a certain area will always be visible in your parallax in smaller screens.', GAMBIT_VC_PARALLAX_BG ),
				),
				array(
					'type' => 'textfield',
					'class' => '',
					'heading' => __( 'Opacity', GAMBIT_VC_PARALLAX_BG ),
					'param_name'  => 'opacity',
					'value' => '100',
					'description' => __( 'You may set the opacity level for your parallax. You can add a background color to your row and add an opacity here to tint your parallax. <strong>Please choose an integer value between 1 and 100.</strong>', GAMBIT_VC_PARALLAX_BG ),
				),
				array(
					'type' => 'checkbox',
					'class' => '',
					'param_name' => 'enable_mobile',
					'value' => array( __( 'Check this to enable the parallax effect in mobile devices', GAMBIT_VC_PARALLAX_BG ) => 'parallax-enable-mobile' ),
					'description' => __( 'Parallax effects would most probably cause slowdowns when your site is viewed in mobile devices. If the device width is less than 980 pixels, then it is assumed that the site is being viewed in a mobile device.', GAMBIT_VC_PARALLAX_BG ),
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
					'heading' => __( 'Parallax ID', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'prlx_id',
					'value' => '',
					'description' => __( 'Assign an ID for the actual parallax row here.', GAMBIT_VC_PARALLAX_BG ),
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
				'direction' => 'up',
				'speed' => '0.3',
				'background_repeat' => '',
				'background_position' => '',
				'opacity' => '100',
				'enable_mobile' => '',
				'class' => '',
				'id' => '',
				'prlx_id' => '',
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

			return  '<div ' . $id . "class='gambit_parallax_row" . $class . "' " .
			"data-bg-align='" . esc_attr( $atts['background_position'] ) . "' " .
			"data-direction='" . esc_attr( $atts['direction'] ) . "' " .
	        "data-opacity='" . esc_attr( $atts['opacity'] ) . "' " .
			"data-velocity='" . esc_attr( (float) $atts['speed'] * -1 ) . "' " .
			"data-mobile-enabled='" . esc_attr( $atts['enable_mobile'] ) . "' " .
			"data-bg-height='" . esc_attr( $bg_image_height ) . "' " .
			"data-bg-width='" . esc_attr( $bg_image_width ) . "' " .
			"data-bg-image='" . esc_attr( $bg_image ) . "' " .
			"data-bg-repeat='" . esc_attr( empty( $atts['background_repeat'] ) ? 'false' : 'true' ) . "' " .
			"data-id='" . esc_attr( $atts['prlx_id'] ) . "' " .
			"style='display: none'></div>";
		}
	}

	new GambitVCParallaxRow();

}
