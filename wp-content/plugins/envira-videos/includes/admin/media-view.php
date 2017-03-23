<?php
/**
 * Media View class.
 *
 * @since 1.1.0
 *
 * @package Envira_Videos
 * @author  Tim Carr
 */
class Envira_Videos_Media_View {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Base
        $this->base = Envira_Videos::get_instance();

		// Scripts
        add_action( 'envira_gallery_metabox_styles', array( $this, 'styles' ) );
        add_action( 'envira_gallery_metabox_scripts', array( $this, 'scripts' ) );

        // Modals
        add_filter( 'envira_gallery_media_view_strings', array( $this, 'media_view_strings' ) );
        add_action( 'print_media_templates', array( $this, 'print_media_templates' ), 10, 3 );

    }

    /**
     * Enqueues CSS for this Addon
     *
     * @since 1.0.0
     */
    public function styles() {

        wp_register_style( $this->base->plugin_slug . 'media-view-style', plugins_url( 'assets/css/media-view.css', $this->base->file ), array(), $this->base->version );
        wp_enqueue_style( $this->base->plugin_slug . 'media-view-style' );

    }

    /**
     * Enqueues JS for this Addon
     *
     * @since 1.0.0
     */
    public function scripts() {

        // Get Gallery ID
        global $id, $post;
        $post_id = isset( $post->ID ) ? $post->ID : (int) $id;

        wp_register_script( $this->base->plugin_slug . '-media-script', plugins_url( 'assets/js/media-view.js', $this->base->file ), array( 'jquery' ), $this->base->version, true );
        wp_enqueue_script( $this->base->plugin_slug . '-media-script' );
        wp_localize_script(
            $this->base->plugin_slug . '-media-script',
            'envira_videos_media_view',
            array(
                'nonce'         => wp_create_nonce( 'envira-videos-media-view-nonce' ),
                'post_id'       => $post_id,
            )
        );

    }

    /**
    * Adds media view (modal) strings for this addon
    *
    * @since 1.0.3
    *
    * @param    array   $strings    Media View Strings
    * @return   array               Media View Strings
    */
    public function media_view_strings( $strings ) {

        $strings['enviraVideosTitle']             = __( 'Insert Videos', 'envira-videos' );
        $strings['enviraVideosValidationError']   = __( 'Please ensure all required fields are specified for each video you want to add to the Gallery.', 'envira-videos' );
        return $strings;

    }

    /**
    * Outputs backbone.js wp.media compatible templates, which are loaded into the modal
    * view
    *
    * @since 1.0.3
    *
    * @param    int $post_id    Post ID
    */
    public function print_media_templates( $post_id ) {

        // Router Bar
        // Use: wp.media.template( 'envira-videos-router' )
        ?>
        <script type="text/html" id="tmpl-envira-videos-router">
            <div class="media-toolbar">
                <div class="media-toolbar-secondary">
                    <span class="spinner"></span>
                </div>
                <div class="media-toolbar-primary search-form">
                    <button class="envira-videos-add button button-primary"><?php _e( 'Add Video', 'envira-videos' ); ?></button>
                </div>
            </div>
        </script>

        <?php
        // Side Bar
        // Use: wp.media.template( 'envira-videos-side-bar' )
        ?>
        <script type="text/html" id="tmpl-envira-videos-side-bar">
            <div class="media-sidebar">
                <div class="envira-gallery-meta-sidebar">
                    <h3><?php _e( 'Helpful Tips', 'envira-videos' ); ?></h3>
                    <strong><?php _e( 'Creating Video Items', 'envira-videos' ); ?></strong>
                    <p><?php _e( 'The image for each video is automatically created from the video link you supply. Video links can be from either YouTube, Vimeo, Wistia or locally hosted video files. They <strong>must</strong> follow one of the formats listed below:', 'envira-videos' ) ?></p>

                    <div class="envira-gallery-accepted-urls">
                        <span><strong><?php _e( 'YouTube URLs', 'envira-videos' ); ?></strong></span>
                        <span>https://youtube.com/v/{vidid}</span>
                        <span>https://youtube.com/vi/{vidid}</span>
                        <span>https://youtube.com/?v={vidid}</span>
                        <span>https://youtube.com/?vi={vidid}</span>
                        <span>https://youtube.com/watch?v={vidid}</span>
                        <span>https://youtube.com/watch?vi={vidid}</span>
                        <span>https://youtu.be/{vidid}</span><br />

                        <span><strong><?php _e( 'Vimeo URLs', 'envira-videos' ); ?></strong></span>
                        <span>https://vimeo.com/{vidid}</span>
                        <span>https://vimeo.com/groups/tvc/videos/{vidid}</span>
                        <span>https://player.vimeo.com/video/{vidid}</span><br />

                        <span><strong><?php _e( 'Wistia URLs', 'envira-videos' ); ?></strong></span>
                        <span>https://wistia.com/medias/*</span>
                        <span>https://wistia.com/embed/*</span>
                        <span>https://wi.st/medias/*</span>
                        <span>https://wi.st/embed/*</span><br />

                        <span><strong><?php _e( 'Local URLs', 'soliloquy' ); ?></strong></span>
                        <span><?php bloginfo('url'); ?>/path/to/video.mp4</span>
                        <span><?php bloginfo('url'); ?>/path/to/video.ogv</span>
                        <span><?php bloginfo('url'); ?>/path/to/video.webm</span>
                        <span><?php bloginfo('url'); ?>/path/to/video.3gp</span>
                    </div>
                 </div>
            </div>
        </script>

        <?php
        // Error Message
        // Use: wp.media.template( 'envira-videos-error' )
        ?>
        <script type="text/html" id="tmpl-envira-videos-error">
            <p>
                {{ data.error }}
            </p>
        </script>

        <?php
        // Collection of Videos
        // Use: wp.media.template( 'envira-videos-items' )
        // wp.media.template( 'envira-videos-item' ) is used to inject <li> items into this template
        ?>
        <script type="text/html" id="tmpl-envira-videos-items">
            <ul class="attachments envira-videos-attachments"></ul>
        </script>
        <?php

        // Single Video
        // Use: wp.media.template( 'envira-videos-item' )
        ?>
        <script type="text/html" id="tmpl-envira-videos-item">
            <div class="envira-videos-item">
                <a href="#" class="button button-secondary envira-videos-delete" title="<?php _e( 'Remove', 'envira-videos' ); ?>"><?php _e( 'Remove', 'envira-videos' ); ?></a>

                <!-- Title -->
                <div>
                    <label>
                        <strong><?php _e( 'Title *', 'envira-videos' ); ?></strong>
                        <input type="text" name="title" />
                    </label>
                </div>

                <!-- Video URL -->
                <div>
                    <label>
                        <strong><?php _e( 'Video URL *', 'envira-videos' ); ?></strong>
                        <input type="text" name="link" />
                    </label>
                </div>

                <!-- Image -->
                <div class="image">
                    <label>
                        <strong><?php _e( 'Image URL *', 'envira-videos' ); ?></strong>
                        <input type="text" name="image" />
                    </label>
                    <p class="description"><?php _e( 'Required if specifying a local video URL.', 'envira-videos' ); ?></p>
                </div>

                <!-- Caption -->
                <div>
                    <label>
                        <strong><?php _e( 'Caption', 'envira-videos' ); ?></strong>
                        <input type="text" name="caption" />
                    </label>
                </div>

                <!-- Alt Text -->
                <div>
                    <label>
                        <strong><?php _e( 'Alt Text', 'envira-videos' ); ?></strong>
                        <input type="text" name="alt" />
                    </label>
                </div>
            </div>
        </script>

        <?php
        // Edit Metadata
        // Use: wp.media.template( 'envira-meta-editor-video' )
        ?>
        <script type="text/html" id="tmpl-envira-meta-editor-video">

            <label class="setting">
                <span class="name"><?php _e( 'Is 16:9 Video?', 'envira-videos' ); ?></span>
                <span class="description">
                    <input type="checkbox" name="video_aspect_ratio" value="16:9"<# if ( data.video_aspect_ratio == '16:9' ) { #> checked <# } #> />
                    <?php _e( 'If this video is in 16:9 aspect ratio, check this option to ensure the video displays without black bars in the Lightbox view.', 'envira-videos' ); ?>
                </span>
            </label>

            <label class="setting">
                <span class="name"><?php _e( 'Video Width', 'envira-videos' ); ?></span>
                <span class="description">
                    <input type="text" name="video_width" value="{{ data.video_width }}" />
                    <?php _e( 'For videos that don\'t display the dimensions properly, you can set the width here.', 'envira-videos' ); ?>
                </span>
            </label>
            <label class="setting">
                <span class="name"><?php _e( 'Video Height', 'envira-videos' ); ?></span>
                <span class="description">
                    <input type="text" name="video_height" value="{{ data.video_height }}" />
                    <?php _e( 'For videos that don\'t display the dimensions properly, you can set the height here.', 'envira-videos' ); ?>
                </span>
            </label>

            <label class="setting">
                <span class="name"><?php _e( 'Display Video in Gallery?', 'envira-videos' ); ?></span>
                <span class="description">
                    <input type="checkbox" name="video_in_gallery" value="1"<# if ( data.video_in_gallery == '1' ) { #> checked <# } #> />
                    <?php _e( 'If this media item\'s URL is a self-hosted, Youtube, Vimeo or Wistia video, you can check this option to display the video in the gallery grid, instead of displaying the placeholder image.', 'envira-videos' ); ?>
                </span>
            </label>
        </script>

        <?php

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Videos_Media_View object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Videos_Media_View ) ) {
            self::$instance = new Envira_Videos_Media_View();
        }

        return self::$instance;

    }

}

// Load the media class.
$envira_videos_media_view = Envira_Videos_Media_View::get_instance();