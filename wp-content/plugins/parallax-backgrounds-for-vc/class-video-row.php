<?php
/**
 * Video Row routines.
 *
 * @version 1.1
 * @package Parallax Backgrounds for VC
 */

// Initializes the Video Row functionality.
if ( ! class_exists( 'GambitVCVideoRow' ) ) {

	/**
	 * This is where all the Video Row functionality happens.
	 */
	class GambitVCVideoRow {
		/**
		 * Uniquely identifies rendered videos.
		 *
		 * @var string $video_id - The Video ID used.
		 */
		public static $video_id = 0;


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
			add_shortcode( 'video_row', array( $this, 'create_shortcode' ) );

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
				'name' => __( 'Video Row Background', GAMBIT_VC_PARALLAX_BG ),
				'base' => 'video_row',
				'icon' => plugins_url( 'parallax/images/vc-video.png', __FILE__ ),
				'description' => __( 'Add a video bg to your row.', GAMBIT_VC_PARALLAX_BG ),
				'category' => __( 'Row Adjustments', GAMBIT_VC_PARALLAX_BG ),
				'params' => array(
				array(
					'type' => 'textfield',
					'class' => '',
					'heading' => __( 'YouTube or Vimeo URL or Video ID', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'video',
					'value' => '',
					'description' => __( "Enter the URL to the video or the video ID of your YouTube or Vimeo video you want to use as your background. If your URL isn't showing a video, try inputting the video ID instead. <em>Ads will show up in the video if it has them.</em> <strong>Tip: newly uploaded videos may not display right away and might show an error message</strong><br><br><strong>Videos will not show up in mobile devices because they handle videos differently. In those cases, please put in a background image the normal way (in the <em>Design Options</em> tab in the row background) and that will be shown instead.</strong><br /><br />Only videos set as public or unlisted can be used, private videos will not work.", GAMBIT_VC_PARALLAX_BG ),
				),
				array(
					'type' => 'checkbox',
					'class' => '',
					'heading' => __( 'Mute Video', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'mute',
					'value' => array( __( 'Mute the video.', GAMBIT_VC_PARALLAX_BG ) => 'mute' ),
				),
				array(
					'type' => 'checkbox',
					'class' => '',
					'heading' => __( 'YouTube force HD', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'force_hd',
					'value' => array( __( "Force YouTube video to load in HD. Depending on the video uploaded, it may range between 720p and 1080p, whichever is the highest possible determined by YouTube over the viewer's current connection. Vimeo plus or PRO can force HD loading via their video's settings.", GAMBIT_VC_PARALLAX_BG ) => 'forcehd' ),
				),
				array(
					'type' => 'textfield',
					'class' => '',
					'heading' => __( 'Video Aspect Ratio', GAMBIT_VC_PARALLAX_BG ),
					'param_name' => 'aspect_ratio',
					'value' => '16:9',
					'description' => __( 'The video will be resized to maintain this aspect ratio, this is to prevent the video from showing any black bars. Enter an aspect ratio here such as: &quot;16:9&quot;, &quot;4:3&quot; or &quot;16:10&quot;. The default is &quot;16:9&quot;', GAMBIT_VC_PARALLAX_BG ),
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
				'video' => '',
				'mute' => '',
				'force_hd' => '',
				'aspect_ratio' => '16:9',
				'opacity' => '100',
				'class' => '',
				'id' => '',
			);
			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );
			$id = '';
			$class = '';

			if ( empty( $atts['video'] ) ) {
				return '';
			}

			wp_enqueue_script( 'gambit_parallax', plugins_url( 'parallax/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_VC_PARALLAX_BG, true );
			wp_enqueue_style( 'gambit_parallax', plugins_url( 'parallax/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_PARALLAX_BG );

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

			self::$video_id++;

			$video_meta = self::get_video_provider( $atts['video'] );
			if ( 'youtube' == $video_meta['type'] ) {
				$video_div = "<div class='click-overrider'></div><div style='visibility: hidden' id='video-" . self::$video_id . "' data-youtube-video-id='" . esc_attr( $video_meta['id'] ) . "' data-force-hd='" . ( 'forcehd' == $atts['force_hd'] ? 'true' : 'false' ) . "' data-mute='" . ( 'mute' == $atts['mute'] ? 'true' : 'false' ) . "' data-video-aspect-ratio='" . esc_attr( $atts['aspect_ratio'] ) . "'><div id='video-" . self::$video_id . "-inner'></div></div>";
			} else {
				// Need to include "webkitallowfullscreen mozallowfullscreen allowfullscreen" below or else video will NOT loop in Firefox.
				// $video_div = '<script src="//f.vimeocdn.com/js/froogaloop2.min.js"></script><div class="click-overrider"></div><div id="video-' .
				$video_div = '<div class="click-overrider"></div><div id="video-' . self::$video_id . '" data-vimeo-video-id="' . esc_attr( $video_meta['id'] ) . '" data-mute="' . ( 'mute' == $atts['mute'] ? 'true' : 'false' ) . '" data-video-aspect-ratio="' . esc_attr( $atts['aspect_ratio'] ) . '"><iframe id="video-iframe-' . self::$video_id . '" src="//player.vimeo.com/video/' . $video_meta['id'] . '?api=1&player_id=video-iframe-' . self::$video_id . '&html5=1&autopause=0&autoplay=1&badge=0&byline=0&loop=1&title=0" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
			}

			return  '<div ' . $id . "class='gambit_video_row" . $class . "' " .
	        "data-mute='" . esc_attr( $atts['mute'] ) . "' " .
	        "data-opacity='" . esc_attr( $atts['opacity'] ) . "' " .
			"style='display: none'>" .
			$video_div .
			'</div>';
		}


		/**
		 * Gets the Video ID & Provider from a video URL or ID.
		 *
		 * @param string $video_string - The URL or ID of a video.
		 * @return	array - The container whether the video is a YouTube video or a Vimeo video along with the video ID.
		 * @since	3.0
		 */
		protected static function get_video_provider( $video_string ) {

			$video_string = trim( $video_string );

			/*
			 * Check for YouTube.
			 */
			$video_id = false;
			if ( preg_match( '/youtube\.com\/watch\?v=([^\&\?\/]+)/', $video_string, $id ) ) {
				if ( count( $id > 1 ) ) {
					$video_id = $id[1];
				}
			} else if ( preg_match( '/youtube\.com\/embed\/([^\&\?\/]+)/', $video_string, $id ) ) {
				if ( count( $id > 1 ) ) {
					$video_id = $id[1];
				}
			} else if ( preg_match( '/youtube\.com\/v\/([^\&\?\/]+)/', $video_string, $id ) ) {
				if ( count( $id > 1 ) ) {
					$video_id = $id[1];
				}
			} else if ( preg_match( '/youtu\.be\/([^\&\?\/]+)/', $video_string, $id ) ) {
				if ( count( $id > 1 ) ) {
					$video_id = $id[1];
				}
			}

			if ( ! empty( $video_id ) ) {
				return array(
				'type' => 'youtube',
				'id' => $video_id,
				);
			}

			/*
			 * Check for Vimeo.
			 */
			if ( preg_match( '/vimeo\.com\/(\w*\/)*(\d+)/', $video_string, $id ) ) {
				if ( count( $id > 1 ) ) {
					$video_id = $id[ count( $id ) - 1 ];
				}
			}

			if ( ! empty( $video_id ) ) {
				return array(
				'type' => 'vimeo',
				'id' => $video_id,
				);
			}

			/*
			 * Non-URL form.
			 */
			if ( preg_match( '/^\d+$/', $video_string ) ) {
				return array(
				'type' => 'vimeo',
				'id' => $video_string,
				);
			}

			return array(
			'type' => 'youtube',
			'id' => $video_string,
			);
		}
	}

	new GambitVCVideoRow();

}
