<?php
/**
 * AJAX class.
 *
 * @since 1.0.0
 *
 * @package Envira_Videos
 * @author  Tim Carr
 */
class Envira_Videos_Ajax {

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

        add_action( 'wp_ajax_envira_videos_is_hosted_video', array( $this, 'is_hosted_video' ) );
        add_action( 'wp_ajax_envira_videos_insert_videos', array( $this, 'insert_videos' ) );
        add_action( 'envira_gallery_ajax_save_meta', array( $this, 'save_meta' ), 10, 4 );
        add_filter( 'envira_gallery_ajax_save_bulk_meta', array( $this, 'save_meta' ), 10, 4 );

    }

    /**
     * Called by the media view when the video URL input is changed
     * Checks if the supplied video URL is a locally hosted video URL or not
     *
     * @since 1.1.1
     *
     * @return json Success or Error
     */
    public function is_hosted_video() {

        // Run a security check first.
        check_ajax_referer( 'envira-videos-media-view-nonce', 'nonce' );

        // Setup vars
        $video_url = ( isset( $_POST['video_url'] ) ? sanitize_text_field( $_POST['video_url'] ) : '' );
        
        // Check a URL was defined
        if ( empty( $video_url ) ) {
            wp_send_json_error( __( 'No video URL was defined', 'envira-videos' ) );
            die();
        }

        // Get video type
        $video_type = Envira_Videos_Common::get_instance()->get_video_type( $video_url, array(), array(), true );
        
        // Depending on the video type, return true or false to determine whether it's a self hosted video
        $is_hosted_video = false;
        switch ( $video_type ) {
            case 'youtube':
            case 'vimeo':
            case 'wistia':
                $is_hosted_video = false;
                break;

            case 'mp4':
            case 'flv':
            case 'ogv':
            case 'webm':
                $is_hosted_video = true;
                break;

            default:
                // Allow addons to define whether the video type is hosted or third party
                $is_hosted_video = apply_filters( 'envira_videos_is_hosted_video', $is_hosted_video, $video_type );
                break;
        }

        // Return
        wp_send_json_success( $is_hosted_video );
        die();

    }

    /**
     * Called by Envira Gallery when inserting media (images or videos).
     * Checks if videos were specified, and if so grabs the plaecholder images and adds them as images to Envira
     *
     * @since 1.0.0
     *
     * @return json Success or Error
     */
    public function insert_videos() {

        // Run a security check first.
        check_ajax_referer( 'envira-videos-media-view-nonce', 'nonce' );

        // Setup vars
        $videos = ( isset( $_POST['videos'] ) ? $_POST['videos'] : '' );
        $post_id = absint( $_POST['post_id'] );

        if ( empty( $videos ) || empty( $videos ) ) {
            wp_send_json_error( __( 'No videos or Gallery ID were specified', 'envira-videos' ) );
            die();
        }

        // Get in gallery and gallery data meta
        $gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );
        $in_gallery = get_post_meta( $post_id, '_eg_in_gallery', true );

        // Get helpers
        $common = Envira_Videos_Common::get_instance();
        $metaboxes = Envira_Gallery_Metaboxes::get_instance();
        
        // Loop through the videos and add them to the gallery.
        foreach ( (array) $videos as $i => $video ) {
            // Pass over if the main items necessary for the video are not set.
            if ( ! isset( $video['link'] ) ) {
                continue;
            }

            // Get video type and ID
            $result = $common->get_video_type( $video['link'], $video, $gallery_data );
            if ( ! $result ) {
                continue;
            }

            // Get the image depending on the video type
            switch ( $result['type'] ) {
                case 'youtube':
                    $video['src'] = $this->get_youtube_thumbnail_url( $result['video_id'] );
                    break;

                case 'vimeo':
                    $video['src'] = $this->get_vimeo_thumbnail_url( $result['video_id'] );
                    break;

                case 'wistia':
                    $video['src'] = $this->get_wistia_thumbnail_url( $video['link'] ); // Deliberate; Wistia doesn't need a video ID
                    break;

                case 'mp4':
                case 'flv':
                case 'ogv':
                case 'webm':
                    $video['src'] = $video['image'];
                    break;

                default:
                    // Allow devs and custom addons to get the thumbnail for their custom video type
                    $video['src'] = apply_filters( 'envira_videos_get_thumbnail_url', '', $result, $video );
                    break;
            }

            // Check $video['src'] now exists - if not, discard this video
            if ( ! isset( $video['src'] ) ) {
                continue;
            }

            // Get remote image into local filesystem
            $stream = Envira_Gallery_Import::get_instance()->import_remote_image( $video['src'], $gallery_data, $video, $post_id, 0, true );
            
            // Check for errors
            if ( is_wp_error( $stream ) ) {
                wp_send_json_error( sprintf( __( 'Video #%s Image Error: %s', 'envira-videos' ), ( $i + 1 ), $stream->get_error_message() ) );
                die();
            }
            if ( ! empty( $stream['error'] ) ) {
                wp_send_json_error( sprintf( __( 'Video #%s Image Error: %s', 'envira-videos' ), ( $i + 1 ), $stream['error'] ) );
                die();
            }

            // Add video to gallery
            $attachment_id = $stream['attachment_id'];
            $gallery_data['gallery'][ $attachment_id ] = array(
                'status'    => 'active',
                'src'       => ( isset( $stream ) ? $stream['url'] : '' ), // Image URL
                'title'     => $video['title'],
                'link'      => $video['link'], // Video URL
                'alt'       => $video['alt'],
                'caption'   => $video['caption'],
                'thumb'     => '',
            );

            // Add gallery ID to video attachment ID
            $has_gallery = get_post_meta( $attachment_id, '_eg_has_gallery', true );
            if ( empty( $has_gallery ) ) {
                $has_gallery = array();
            }
            $has_gallery[] = $post_id;
            update_post_meta( $attachment_id, '_eg_has_gallery', $has_gallery );

            // Add video to in_gallery
            $in_gallery[] = $attachment_id;
        } 

        // Update the gallery data.
        update_post_meta( $post_id, '_eg_in_gallery', $in_gallery );
        update_post_meta( $post_id, '_eg_gallery_data', $gallery_data );

        // Get instances
        $common = Envira_Gallery_Common::get_instance();

        // If the thumbnails option is checked, crop images accordingly.
        if ( isset( $gallery_data['config']['thumbnails'] ) && $gallery_data['config']['thumbnails'] ) {
            $args = array(
                'position' => 'c',
                'width'    => ( isset( $gallery_data['config']['thumbnails_width']) ? $gallery_data['config']['thumbnails_width'] : $common->get_config_default( 'thumbnails_width' ) ),
                'height'   => ( isset( $gallery_data['config']['thumbnails_height']) ? $gallery_data['config']['thumbnails_height'] : $common->get_config_default( 'thumbnails_width' ) ),
                'quality'  => 100,
                'retina'   => false
            );
            $args = apply_filters( 'envira_gallery_crop_image_args', $args );
            Envira_Gallery_Metaboxes::get_instance()->crop_thumbnails( $args, $post_id );
        }

        // Flush the gallery cache.
        $common->flush_gallery_caches( $post_id );

        // Return a HTML string comprising of all gallery images, so the UI can be updated
        $html = '';
        foreach ( (array) $gallery_data['gallery'] as $id => $data ) {
            $html .= $metaboxes->get_gallery_item( $id, $data, $post_id );
        }

        // Return success with gallery grid HTML
        wp_send_json_success( $html );
        die;

    }

    /**
    * Attempts to get a HD thumbnail URL for the given YouTube video ID.
    * If a 120x90 grey placeholder image is returned, the video isn't HD, so
    * the function will return the SD thumbnail URL
    *
    * @since 1.0.0
    *
    * @param string $video_id YouTube Video ID
    * @return string HD or SD Thumbnail URL
    */
    public function get_youtube_thumbnail_url( $video_id ) {
        
        // Determine video URL
        $prefix = is_ssl() ? 'https' : 'http';
        $base_url = $prefix . '://img.youtube.com/vi/' . $video_id . '/';
        $hd_url = $base_url . 'maxresdefault.jpg'; // 1080p or 720p
        $sd_url = $base_url . '0.jpg'; // 480x360
        
        // Get HD image from YouTube
        $image_data = wp_remote_get( $hd_url, array(
            'timeout' => 10,    
        ) );
        
        // Check request worked
        if ( is_wp_error( $image_data ) || !isset( $image_data['body'] ) ) {
            // Failed - fallback to SD Thumbnail
            return $sd_url;  
        }
        
        // Get image size
        if ( ! function_exists( 'getimagesizefromstring' ) ) {
            // PHP 5.3-
            $uri = 'data://application/octet-stream;base64,'  . base64_encode( $image_data['body'] );
            $image_size = getimagesize( $image_data['body'] );
        } else {
            // PHP 5.4+
            $image_size = getimagesizefromstring( $image_data['body'] );
        }

        // Check request worked
        if ( !is_array( $image_size ) ) {
            // Failed - fallback to SD Thumbnail
            return $sd_url;  
        }
        
        // Check image size isn't 120x90
        if ( $image_size[0] == 120 && $image_size[1] == 90) {
            // Failed - fallback to SD Thumbnail
            return $sd_url;
        }
        
        // Image is a valid YouTube HD thumbnail
        return $hd_url;
        
    }

    /**
    * Attempts to get the highest resolution thumbnail URL for the given Vimeo video ID.
    *
    * @since 1.0.0
    *
    * @param string $video_id Vimeo Video ID
    * @return string Best resolution URL
    */
    public function get_vimeo_thumbnail_url( $video_id ) {
        
        // Get existing access token
        $vimeo_access_token = get_option( 'envira_videos_vimeo_access_token' );
        
        // Load Vimeo API
        $vimeo = new Envira_Videos_Vimeo( '5edbf52df73b6834db186409f88d2108df6a3d7f', '54e233c7ec90b22ad7cc77875b9a5a9d3083fa08' );
        $vimeo->setToken( $vimeo_access_token );
        
        // Attempt to get video
        $response = $vimeo->request( '/videos/' . $video_id . '/pictures' );
        
        // Check response
        if ( $response['status'] != 200 ) {
            // May need a new access token
            // Clear old token + request a new one
            $vimeo->setToken( '' );
            $token = $vimeo->clientCredentials();
            $vimeo_access_token = $token['body']['access_token'];
            $vimeo->setToken( $vimeo_access_token );
            
            // Store new token in options data
            update_option( 'envira_videos_vimeo_access_token', $vimeo_access_token );
            
            // Run request again
            $response = $vimeo->request( '/videos/' . $video_id . '/pictures' );
        }
        
        // Check response
        if ( $response['status'] != 200 ) {
            // Really a failure!
            return false;
        }
        
        // If here, we got the video details
        // Check thumbnails are in the response
        if ( !isset( $response['body']['data'] ) || !isset( $response['body']['data'][0] ) || !isset( $response['body']['data'][0]['sizes'] ) ) {
            return false;
        }
        
        // Get last item from the array index, as this is the highest resolution thumbnail
        $thumbnail = end( $response['body']['data'][0]['sizes'] );
        
        // Check thumbnail URL exists
        if ( !isset( $thumbnail['link'] ) ) {
            return false;
        }
        
        // Cleanup
        unset( $vimeo );

        // Remove some args and return
        return strtok( $thumbnail['link'], '?' );
        
    }

    /**
    * Attempts to get the highest resolution thumbnail URL for the given Wistia video link.
    *
    * @since 1.0.0
    *
    * @param string $video_link Wistia Video Link
    * @return string Thumbnail URL
    */
    public function get_wistia_thumbnail_url( $video_link ) {

        $res = wp_remote_get( 'http://fast.wistia.net/oembed?url=' . urlencode( $video_link ) );
        $bod = wp_remote_retrieve_body( $res );
        $api = json_decode( $bod, true );
        if ( ! empty( $api['thumbnail_url'] ) ) {
            return remove_query_arg( 'image_crop_resized', $api['thumbnail_url'] );
        }

        return '';

    }

    /**
     * Saves Video-specific options when editing an existing image within the modal window.
     *
     * @since 1.1.6
     *
     * @param   array   $gallery_data   Gallery Data
     * @param   array   $meta           Meta
     * @param   int     $attach_id      Attachment ID
     * @param   int     $post_id        Post (Gallery) ID
     * @return  array                   Gallery Data
     */
    public function save_meta( $gallery_data, $meta, $attach_id, $post_id ) {
        
        $gallery_data['gallery'][ $attach_id ]['video_aspect_ratio'] = ( isset( $meta['video_aspect_ratio'] ) ? sanitize_text_field( $meta['video_aspect_ratio'] ) : '' );
        $gallery_data['gallery'][ $attach_id ]['video_width'] = ( isset( $meta['video_width'] ) ? sanitize_text_field( $meta['video_width'] ) : '' );
        $gallery_data['gallery'][ $attach_id ]['video_height'] = ( isset( $meta['video_height'] ) ? sanitize_text_field( $meta['video_height'] ) : '' );
        $gallery_data['gallery'][ $attach_id ]['video_in_gallery'] = ( isset( $meta['video_in_gallery'] ) ? absint( $meta['video_in_gallery'] ) : '' );

        return $gallery_data;

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Videos_Ajax object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Videos_Ajax ) ) {
            self::$instance = new Envira_Videos_Ajax();
        }

        return self::$instance;

    }

}

// Load the AJAX class.
$envira_videos_ajax = Envira_Videos_Ajax::get_instance();
