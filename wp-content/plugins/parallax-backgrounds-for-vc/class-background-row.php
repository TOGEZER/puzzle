<?php
/**
 * Background Row routines.
 *
 * @version 1.1
 * @package Parallax Backgrounds for VC
 */

// Initializes the Background Row functionality.
if ( ! class_exists( 'GambitVCBackgroundRow' ) ) {

	/**
	 * This is where all the Background Row functionality happens.
	 */
	class GambitVCBackgroundRow {
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
			add_shortcode( 'background_row', array( $this, 'create_shortcode' ) );

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
				'name' => __( 'Row Background', GAMBIT_VC_PARALLAX_BG ),
				'base' => 'background_row',
				'icon' => plugins_url( 'parallax/images/vc-background.png', __FILE__ ),
				'description' => __( 'Add a background image or color to your row.', GAMBIT_VC_PARALLAX_BG ),
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
					'type' => 'colorpicker',
					'class' => '',
					'heading' => __( 'Background Color', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'color',
					'value' => '',
					'description' => __( 'Choose a background color.', GAMBIT_VC_PARALLAX_BG ),
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __( 'Background Position', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'background_position',
					'value' => array(
						__( 'Center', GAMBIT_VC_PARALLAX_BG ) => 'center',
						__( 'Theme Default', GAMBIT_VC_PARALLAX_BG ) => '',
						__( 'Left Top', GAMBIT_VC_PARALLAX_BG ) => 'left top',
						__( 'Left Center', GAMBIT_VC_PARALLAX_BG ) => 'left center',
						__( 'Left Bottom', GAMBIT_VC_PARALLAX_BG ) => 'left bottom',
						__( 'Right Top', GAMBIT_VC_PARALLAX_BG ) => 'right top',
						__( 'Right Center', GAMBIT_VC_PARALLAX_BG ) => 'right center',
						__( 'Right Bottom', GAMBIT_VC_PARALLAX_BG ) => 'right bottom',
						__( 'Center Top', GAMBIT_VC_PARALLAX_BG ) => 'center top',
						__( 'Center Bottom', GAMBIT_VC_PARALLAX_BG ) => 'center bottom',
					),
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __( 'Background Image Size', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'background_size',
					'value' => array(
						__( 'Cover', GAMBIT_VC_PARALLAX_BG ) => 'cover',
						__( 'Theme Default', GAMBIT_VC_PARALLAX_BG ) => '',
						__( 'Contain', GAMBIT_VC_PARALLAX_BG ) => 'contain',
						__( 'No Repeat', GAMBIT_VC_PARALLAX_BG ) => 'no-repeat',
						__( 'Repeat', GAMBIT_VC_PARALLAX_BG ) => 'repeat',
					),
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
				'color' => '',
				'background_size' => 'cover',
				'background_position' => 'center',
				'class' => '',
				'id' => '',
			);
			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );

			if ( empty( $atts['image'] ) && empty( $atts['color'] ) ) {
				return '';
			}

			wp_enqueue_script( 'gambit_parallax', plugins_url( 'parallax/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_VC_PARALLAX_BG, true );
			wp_enqueue_style( 'gambit_parallax', plugins_url( 'parallax/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_PARALLAX_BG );

			$attachment_image = wp_get_attachment_image_src( $atts['image'], 'full' );
			$image_url = '';
			if ( ! empty( $attachment_image ) ) {
				$image_url = $attachment_image[0];
			}

			$style = 'display: none;';
			$id = '';
			$class = '';
			$style = '';
			if ( ! empty( $image_url ) ) {
				$style .= 'background-image: url(' . esc_url( $image_url ) . ');';
			}
			if ( ! empty( $atts['color'] ) ) {
				$style .= 'background-color: ' . esc_attr( $atts['color'] ) . ';';
			}
			if ( ! empty( $atts['background_size'] ) ) {
				if ( in_array( $atts['background_size'], array( 'cover', 'contain' ) ) ) {
					$style .= 'background-size: ' . esc_attr( $atts['background_size'] ) . ';';
				} else {
					$style .= 'background-repeat: ' . esc_attr( $atts['background_size'] ) . ';';
				}
			}
			if ( ! empty( $atts['background_position'] ) ) {
				$style .= 'background-position: ' . esc_attr( $atts['background_position'] ) . ';';
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

			return  '<div ' . $id . "class='gambit_background_row" . $class . "' style='{$style}'></div>";
		}
	}

	new GambitVCBackgroundRow();

}
