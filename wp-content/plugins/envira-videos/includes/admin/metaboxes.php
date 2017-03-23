<?php
/**
 * Metabox class.
 *
 * @since 1.0.0
 *
 * @package Envira_Videos
 * @author  Tim Carr
 */
class Envira_Videos_Metaboxes {

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

        // Styles and Scripts
        add_action( 'envira_gallery_metabox_styles', array( $this, 'styles' ) );
        add_action( 'envira_gallery_metabox_scripts', array( $this, 'scripts' ) );

        // Gallery
        add_filter( 'envira_gallery_metabox_output_gallery_item_meta', array( $this, 'output_gallery_item_meta' ), 10, 4 );
        add_filter( 'envira_gallery_tab_nav', array( $this, 'tab_nav' ) );
        add_action( 'envira_gallery_tab_videos', array( $this, 'settings_screen' ) );
        add_filter( 'envira_gallery_save_settings', array( $this, 'gallery_settings_save' ), 10, 2 );

        // Albums
        add_filter( 'envira_albums_tab_nav', array( $this, 'tab_nav' ) );
        add_action( 'envira_albums_tab_videos', array( $this, 'settings_screen' ) );
        add_filter( 'envira_albums_save_settings', array( $this, 'album_settings_save' ), 10, 2 );

    }

    /**
     * Enqueues styles used when creating or editing a Gallery
     *
     * @since 1.1.9
    */
    public function styles() {

        wp_enqueue_style( $this->base->plugin_slug . '-metabox-style', plugins_url( 'assets/css/metabox.css', $this->base->file ), array(), $this->base->version );
        
    }

    /**
     * Enqueues the Media Editor script, which is used when editing a gallery image
     * This outputs the Video settings for each individual image
     *
     * @since 1.1.6
    */
    public function scripts() {

        wp_enqueue_script( $this->base->plugin_slug . '-media-edit', plugins_url( 'assets/js/media-edit.js', $this->base->file ), array( 'jquery' ), $this->base->version, true );
        
    }

    /**
     * Adds the item's video type to the gallery item output
     *
     * @since 1.1.9
     *
     * @param string    $output     Meta Output
     * @param array     $item       Gallery Item
     * @param int       $attach_id  Attachment ID
     * @param int       $post_id    Gallery ID
     * @return array                Gallery Item
     */
    public function output_gallery_item_meta( $output, $item, $attach_id, $post_id ) {

        // Determine if the item is a video
        $video_type = Envira_Videos_Common::get_instance()->get_video_type( $item['link'], $item, array(), true );
        if ( ! $video_type ) {
            return $output;
        }

        // Output an element with the video type as the class, so we can style it to display the logo
        $output .= '<span class="envira-video-type ' . $video_type . '">' . $video_type . '</span>';
        return $output;

    }

    /**
     * Adds a new tab for this addon.
     *
     * @since 1.0.0
     *
     * @param array $tabs  Array of default tab values.
     * @return array $tabs Amended array of default tab values.
     */
    function tab_nav( $tabs ) {
    
        $tabs['videos'] = __( 'Videos', 'envira-videos' );
        return $tabs;
    
    }

    /**
     * Adds addon settings ui to the new tab
     *
     * @since 1.0.0
     *
     * @param object $post The current post object.
     */
    function settings_screen( $post ) {
        
        // Get post type so we load the correct metabox instance and define the input field names
        // Input field names vary depending on whether we are editing a Gallery or Album
        $postType = get_post_type( $post );
        switch ( $postType ) {
            /**
            * Gallery
            */
            case 'envira':
                $instance = Envira_Gallery_Metaboxes::get_instance();
                $key = '_envira_gallery';
                break;
            
            /**
            * Album
            */
            case 'envira_album':
                $instance = Envira_Albums_Metaboxes::get_instance();
                $key = '_eg_album_data[config]';
                break;
        }
        ?>
        <div id="envira-videos">
            <p class="envira-intro">
                <?php _e( 'Video Lightbox Settings', 'envira-videos' ); ?>
                <small>
                    <?php _e( 'The settings below adjust the Video options for the Lightbox output.', 'envira-videos' ); ?>
                    <br />
                    <?php _e( 'Need some help?', 'envira-videos' ); ?>
                    <a href="http://enviragallery.com/docs/video-addon/" class="envira-doc" target="_blank">
                        <?php _e( 'Read the Documentation', 'envira-videos' ); ?>
                    </a>
                    or
                    <a href="https://www.youtube.com/embed/fVO7_43iYWk/?rel=0" class="envira-video" target="_blank">
                        <?php _e( 'Watch a Video', 'envira-videos' ); ?>
                    </a>
                </small>
            </p>
            <table class="form-table">
                <tbody>
                    <tr id="envira-config-videos-play-icon-box">
                        <th scope="row">
                            <label for="envira-config-videos-play-icon"><?php _e( 'Display Play Icon over Gallery Image?', 'envira-videos' ); ?></label>
                        </th>
                        <td>
                            <input id="envira-config-videos-play-icon" type="checkbox" name="<?php echo $key; ?>[videos_play_icon]" value="1" <?php checked( $instance->get_config( 'videos_play_icon', $instance->get_config_default( 'videos_play_icon' ) ), 1 ); ?> />
                            <span class="description"><?php _e( 'Display a Play Icon over a Gallery Image which is linked to a Video, to make it clear to the user that it is a video. Setting does not apply if an individual image has the &quot;Display Video in Gallery&quot; option enabled.', 'envira-videos' ); ?></span>
                        </td>
                    </tr>

                    <tr id="envira-config-videos-autoplay-box">
                        <th scope="row">
                            <label for="envira-config-videos-autoplay"><?php _e( 'Autoplay Videos?', 'envira-videos' ); ?></label>
                        </th>
                        <td>
                            <input id="envira-config-videos-autoplay" type="checkbox" name="<?php echo $key; ?>[videos_autoplay]" value="1" <?php checked( $instance->get_config( 'videos_autoplay', $instance->get_config_default( 'videos_autoplay' ) ), 1 ); ?> />
                            <span class="description"><?php _e( '(All): Automatically begins playback of videos when they are displayed in the Lightbox view.', 'envira-videos' ); ?></span>
                        </td>
                    </tr>

                    <tr id="envira-config-videos-playpause-box">
                        <th scope="row">
                            <label for="envira-config-videos-playpause"><?php _e( 'Show Play/Pause Controls?', 'envira-videos' ); ?></label>
                        </th>
                        <td>
                            <input id="envira-config-videos-playpause" type="checkbox" name="<?php echo $key; ?>[videos_playpause]" value="1" <?php checked( $instance->get_config( 'videos_playpause', $instance->get_config_default( 'videos_playpause' ) ), 1 ); ?> />
                            <span class="description"><?php _e( '(YouTube, Wistia, Self Hosted): Display play and pause controls on videos in the Lightbox view.', 'envira-videos' ); ?></span>
                        </td>
                    </tr>

                    <tr id="envira-config-videos-progress-box">
                        <th scope="row">
                            <label for="envira-config-videos-progress"><?php _e( 'Show Progress Bar?', 'envira-videos' ); ?></label>
                        </th>
                        <td>
                            <input id="envira-config-videos-progress" type="checkbox" name="<?php echo $key; ?>[videos_progress]" value="1" <?php checked( $instance->get_config( 'videos_progress', $instance->get_config_default( 'videos_progress' ) ), 1 ); ?> />
                            <span class="description"><?php _e( '(Wistia, Self Hosted): Display the progress bar on videos in the Lightbox view.', 'envira-videos' ); ?></span>
                        </td>
                    </tr>

                    <tr id="envira-config-videos-current-box">
                        <th scope="row">
                            <label for="envira-config-videos-current"><?php _e( 'Show Current Time?', 'envira-videos' ); ?></label>
                        </th>
                        <td>
                            <input id="envira-config-videos-current" type="checkbox" name="<?php echo $key; ?>[videos_current]" value="1" <?php checked( $instance->get_config( 'videos_current', $instance->get_config_default( 'videos_current' ) ), 1 ); ?> />
                            <span class="description"><?php _e( '(Self Hosted): Display the current playback time on videos in the Lightbox view.', 'envira-videos' ); ?></span>
                        </td>
                    </tr>

                    <tr id="envira-config-videos-duration-box">
                        <th scope="row">
                            <label for="envira-config-videos-duration"><?php _e( 'Show Video Length?', 'envira-videos' ); ?></label>
                        </th>
                        <td>
                            <input id="envira-config-videos-duration" type="checkbox" name="<?php echo $key; ?>[videos_duration]" value="1" <?php checked( $instance->get_config( 'videos_duration', $instance->get_config_default( 'videos_duration' ) ), 1 ); ?> />
                            <span class="description"><?php _e( '(Self Hosted): Display the video length on videos in the Lightbox view.', 'envira-videos' ); ?></span>
                        </td>
                    </tr>

                    <tr id="envira-config-videos-volume-box">
                        <th scope="row">
                            <label for="envira-config-videos-volume"><?php _e( 'Enable Volume Controls?', 'envira-videos' ); ?></label>
                        </th>
                        <td>
                            <input id="envira-config-videos-volume" type="checkbox" name="<?php echo $key; ?>[videos_volume]" value="1" <?php checked( $instance->get_config( 'videos_volume', $instance->get_config_default( 'videos_volume' ) ), 1 ); ?> />
                            <span class="description"><?php _e( '(Wistia, Self Hosted): Display the volume controls on videos in the Lightbox view.', 'envira-videos' ); ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    
    }

    /**
     * Saves the addon's settings for Galleries.
     *
     * @since 1.0.0
     *
     * @param array $settings  Array of settings to be saved.
     * @param int $post_id     The current post ID.
     * @return array $settings Amended array of settings to be saved.
     */
    function gallery_settings_save( $settings, $post_id ) {
        
        $settings['config']['videos_play_icon'] = ( isset( $_POST['_envira_gallery']['videos_play_icon'] ) ? 1 : 0 );
        $settings['config']['videos_autoplay']  = ( isset( $_POST['_envira_gallery']['videos_autoplay'] ) ? 1 : 0 );
        $settings['config']['videos_playpause'] = ( isset( $_POST['_envira_gallery']['videos_playpause'] ) ? 1 : 0 );
        $settings['config']['videos_progress']  = ( isset( $_POST['_envira_gallery']['videos_progress'] ) ? 1 : 0 );
        $settings['config']['videos_current']   = ( isset( $_POST['_envira_gallery']['videos_current'] ) ? 1 : 0 );
        $settings['config']['videos_duration']  = ( isset( $_POST['_envira_gallery']['videos_duration'] ) ? 1 : 0 );
        $settings['config']['videos_volume']    = ( isset( $_POST['_envira_gallery']['videos_volume'] ) ? 1 : 0 );
        
        return $settings;
    
    }

    /**
     * Saves the addon's settings for Albums.
     *
     * @since 1.0.0
     *
     * @param array $settings  Array of settings to be saved.
     * @param int $post_id     The current post ID.
     * @return array $settings Amended array of settings to be saved.
     */
    function album_settings_save( $settings, $post_id ) {
        
        $settings['config']['videos_play_icon'] = ( isset( $_POST['_eg_album_data']['config']['videos_play_icon'] ) ? 1 : 0 );
        $settings['config']['videos_autoplay']  = ( isset( $_POST['_eg_album_data']['config']['videos_autoplay'] ) ? 1 : 0 );
        $settings['config']['videos_playpause'] = ( isset( $_POST['_eg_album_data']['config']['videos_playpause'] ) ? 1 : 0 );
        $settings['config']['videos_progress']  = ( isset( $_POST['_eg_album_data']['config']['videos_progress'] ) ? 1 : 0 );
        $settings['config']['videos_current']   = ( isset( $_POST['_eg_album_data']['config']['videos_current'] ) ? 1 : 0 );
        $settings['config']['videos_duration']  = ( isset( $_POST['_eg_album_data']['config']['videos_duration'] ) ? 1 : 0 );
        $settings['config']['videos_volume']    = ( isset( $_POST['_eg_album_data']['config']['videos_volume'] ) ? 1 : 0 );
        
        return $settings;
    
    }
	
    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Pagination_Metaboxes object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Videos_Metaboxes ) ) {
            self::$instance = new Envira_Videos_Metaboxes();
        }

        return self::$instance;

    }

}

// Load the metabox class.
$envira_videos_metaboxes = Envira_Videos_Metaboxes::get_instance();