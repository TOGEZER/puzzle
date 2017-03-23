<?php
/**
 * Plugin Name: Envira Gallery - Gallery Themes Addon
 * Plugin URI:  http://enviragallery.com
 * Description: Enables custom themes for the grid display of Envira galleries.
 * Author:      Envira Gallery Team
 * Author URI:  http://enviragallery.com
 * Version:     1.2.5
 * Text Domain: envira-gallery-themes
 * Domain Path: languages
 *
 * Envira Gallery is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Envira Gallery is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Envira Gallery. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define necessary addon constants.
define( 'ENVIRA_GALLERY_THEMES_PLUGIN_NAME', 'Envira Gallery - Gallery Themes Addon' );
define( 'ENVIRA_GALLERY_THEMES_PLUGIN_VERSION', '1.2.5' );
define( 'ENVIRA_GALLERY_THEMES_PLUGIN_SLUG', 'envira-gallery-themes' );

add_action( 'plugins_loaded', 'envira_gallery_themes_plugins_loaded' );
/**
 * Ensures the full Envira Gallery plugin is active before proceeding.
 *
 * @since 1.0.0
 *
 * @return null Return early if Envira Gallery is not active.
 */
function envira_gallery_themes_plugins_loaded() {

    // Bail if the main class does not exist.
    if ( ! class_exists( 'Envira_Gallery' ) ) {
        return;
    }

    // Fire up the addon.
    add_action( 'envira_gallery_init', 'envira_gallery_themes_plugin_init' );

    // Load the plugin textdomain.
    load_plugin_textdomain( ENVIRA_GALLERY_THEMES_PLUGIN_SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

}

/**
 * Loads all of the addon hooks and filters.
 *
 * @since 1.0.0
 */
function envira_gallery_themes_plugin_init() {

    add_action( 'envira_gallery_updater'            , 'envira_gallery_themes_updater' );
    add_filter( 'envira_gallery_gallery_themes'     , 'envira_gallery_themes_gallery_themes' );
    add_filter( 'envira_gallery_justified_gallery_themes', 'envira_gallery_themes_justified_gallery_themes' );
    add_filter( 'envira_gallery_lightbox_themes'    , 'envira_gallery_themes_lightbox_themes' );
    add_filter( 'envira_gallery_output_after_link'  , 'envira_gallery_themes_output', 10, 5 );
    add_filter( 'envira_gallery_get_config'         , 'envira_gallery_themes_gallery_get_config', 10, 2 );
    add_filter( 'envirabox_arrows'                  , 'envira_gallery_themes_arrows', 10, 2 );
    add_filter( 'envirabox_gallery_thumbs_position' , 'envira_gallery_themes_gallery_thumbs_position', 10, 2 );

    add_filter( 'envirabox_dynamic_margin'          , 'envira_gallery_themes_dynamic_margin', 10, 2 );
    add_filter( 'envirabox_dynamic_margin_amount'   , 'envira_gallery_themes_dynamic_margin_amount', 10, 2 );
    add_filter( 'envirabox_margin'                  , 'envira_gallery_themes_margin', 10, 2 );
    add_filter( 'envira_gallery_title_type'         , 'envira_gallery_themes_title_type', 10, 2 );

    add_action( 'envira_gallery_metabox_scripts'    , 'envira_gallery_themes_metabox_scripts' );
    add_filter( 'envira_always_show_title'          , 'envira_gallery_themes_always_show_title', 10, 2 );
    add_filter( 'envirabox_action_divs_root'        , 'envira_gallery_themes_action_divs_root', 10, 2 );
    add_filter( 'envirabox_nav_divs_root'           , 'envira_gallery_themes_nav_divs_root', 10, 2 );

    /* Albums */

    add_filter( 'envira_albums_output_after_link'   , 'envira_gallery_themes_output', 10, 5 );
    add_filter( 'envira_albums_get_config'          , 'envira_gallery_themes_gallery_get_config', 10, 2 );

}

/**
 * Initializes scripts for the metabox admin.
 *
 * @since 1.0.0
 *
 * @param string $key The user license key.
 */
function envira_gallery_themes_metabox_scripts() {
    // Conditional Fields
    wp_register_script( ENVIRA_GALLERY_THEMES_PLUGIN_SLUG . '-conditional-fields-script', plugins_url( 'assets/js/min/conditional-fields-min.js', __FILE__ ), array( 'jquery', Envira_Gallery::get_instance()->plugin_slug . '-conditional-fields-script' ), ENVIRA_GALLERY_THEMES_PLUGIN_VERSION, true );
    wp_enqueue_script( ENVIRA_GALLERY_THEMES_PLUGIN_SLUG . '-conditional-fields-script' );
}

/**
 * Set the title display per theme
 *
 * @since 1.2.3
 *
 * @param string|int this is either a string or an integer and can be set accordingly.
 */
function envira_gallery_themes_always_show_title( $show, $data ) {

    // Get gallery theme
    $instance = Envira_Gallery_Shortcode::get_instance();
    $lightbox_theme = $instance->get_config( 'lightbox_theme', $data );

    switch ( $lightbox_theme ) {

        case 'base_light':
            $show = true;
            break;

    }

    return $show;

}

/**
 * Initializes the addon updater.
 *
 * @since 1.0.0
 *
 * @param string $key The user license key.
 */
function envira_gallery_themes_updater( $key ) {

    $args = array(
        'plugin_name' => ENVIRA_GALLERY_THEMES_PLUGIN_NAME,
        'plugin_slug' => ENVIRA_GALLERY_THEMES_PLUGIN_SLUG,
        'plugin_path' => plugin_basename( __FILE__ ),
        'plugin_url'  => trailingslashit( WP_PLUGIN_URL ) . ENVIRA_GALLERY_THEMES_PLUGIN_SLUG,
        'remote_url'  => 'http://enviragallery.com/',
        'version'     => ENVIRA_GALLERY_THEMES_PLUGIN_VERSION,
        'key'         => $key
    );
    
    $updater = new Envira_Gallery_Updater( $args );

}

/**
 * Adds custom gallery themes to the available list of gallery themes.
 *
 * @since 1.0.0
 *
 * @param array $themes  Array of gallery themes.
 * @return array $themes Amended array of gallery themes.
 */
function envira_gallery_themes_gallery_themes( $themes ) {

    // Add custom themes here.
    $themes[] = array(
        'value' => 'captioned',
        'name'  => __( 'Captioned', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'polaroid',
        'name'  => __( 'Polaroid', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'showcase',
        'name'  => __( 'Showcase', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'sleek',
        'name'  => __( 'Sleek', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'subtle',
        'name'  => __( 'Subtle', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    return $themes;

}

/**
 * Adds custom gallery "themes" to the available list of gallery themes for automatic/justified layout only.
 *
 * @since 1.2
 *
 * @param array $themes  Array of gallery themes.
 * @return array $themes Amended array of gallery themes.
 */
function envira_gallery_themes_justified_gallery_themes( $themes ) {

    $themes[] = array(
        'value' => 'js-blur',
        'name'  => __( 'Blur', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'js-desaturate',
        'name'  => __( 'Desaturate', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'js-threshold',
        'name'  => __( 'Threshold', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'js-vintage',
        'name'  => __( 'Vintage', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );    

    return $themes;

}

/**
 * Adds custom lightbox themes to the available list of lightbox themes.
 *
 * @since 1.0.0
 *
 * @param array $themes  Array of lightbox themes.
 * @return array $themes Amended array of lightbox themes.
 */
function envira_gallery_themes_lightbox_themes( $themes ) {

    // Add custom themes here.
    $themes[] = array(
        'value' => 'base_light',
        'name'  => __( 'Base (Light)', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    /*$themes[] = array(
        'value' => 'modern-dark',
        'name'  => __( 'Modern (Dark)', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'modern-light',
        'name'  => __( 'Modern (Light)', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'space_dark',
        'name'  => __( 'Space (Dark)', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'space_light',
        'name'  => __( 'Space (Light)', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'box_dark',
        'name'  => __( 'Box (Dark)', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'box_light',
        'name'  => __( 'Box (Light)', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'burnt_dark',
        'name'  => __( 'Burnt (Dark)', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'burnt_light',
        'name'  => __( 'Burnt (Light)', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );*/
    
    $themes[] = array(
        'value' => 'captioned',
        'name'  => __( 'Captioned', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'polaroid',
        'name'  => __( 'Polaroid', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'showcase',
        'name'  => __( 'Showcase', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'sleek',
        'name'  => __( 'Sleek', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    $themes[] = array(
        'value' => 'subtle',
        'name'  => __( 'Subtle', 'envira-gallery-themes' ),
        'file'  => __FILE__
    );

    return $themes;

}


/**
 * Adding additional buttons to the 'envirabox-actions' div
 *
 * @since 1.0.0
 *
 * @param string $key The user license key.
 */
add_filter( 'envirabox_actions', 'envira_gallery_themes_output_envirabox_actions', 10, 2 );
function envira_gallery_themes_output_envirabox_actions( $template, $data ) {

    // Get gallery theme
    $instance = Envira_Gallery_Shortcode::get_instance();
    $lightbox_theme = $instance->get_config( 'lightbox_theme', $data );

    switch ( $lightbox_theme ) {

        case 'modern-dark' :
        case 'modern-light' :
            // add the "X" to the envirabox-actions div instead of the -buttons div
            $template .= '<div class="envira-close-button '.$lightbox_theme.'"><a href="javascript:;" class="btnClose" title="Close"></a></div>';
            break;

        case 'base_light':
            // Build Button
            $template .= '<div class="envira-close-button ' . $lightbox_theme . '"><a title="' . __( 'Close', 'envira-gallery' ) . '" class="envirabox-item envira-close" href="#"></a></div>';
            break;

    }
    
    return $template;
}

/**
 * Ensuring Re-Positionged Close Box Closes
 *
 * @since 1.0.0
 *
 * @param string $key The user license key.
 */
add_action( 'envira_gallery_api_after_show', 'envira_gallery_themes_api_after_show', 10, 1 );
function envira_gallery_themes_api_after_show( $data ) { ?>

    $('.envira-close-button').on('click', 'a.btnClose', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.envirabox-actions').remove();
        $.envirabox.close();
    });

<?php 
}

/**
 * Ensuring Re-Positionged Close Box Closes
 *
 * @since 1.0.0
 *
 * @param string $key The user license key.
 */
add_action( 'envira_gallery_api_after_close', 'envira_gallery_themes_api_after_close', 10, 1 );
function envira_gallery_themes_api_after_close( $data ) { ?>

    $('.envirabox-actions').remove();

<?php 
}

/**
 * Set the margin
 *
 * @since 1.0.0
 *
 * @param string|int this is either a string or an integer and can be set accordingly.
 */
function envira_gallery_themes_margin( $margin, $data ) {
    // Get gallery theme
    $instance = Envira_Gallery_Shortcode::get_instance();
    $lightbox_theme = $instance->get_config( 'lightbox_theme', $data );

    switch ( $lightbox_theme ) {

        
        case 'modern-dark':
        case 'modern-light':
            $margin = '[75, 100, 273, 100]';
        case 'space_dark':
        case 'space_light':
        case 'base_light':
            $margin = 35;
            break;
        case 'burnt_dark':
        case 'burnt_light':
            $margin = 10;
            break;
        case 'box_dark':
        case 'box_light':
            $margin = '[0, 10, 198, 10]';
            break;
    }
    
    return $margin;
}

/**
 * Enable the dynamic margin based on the theme
 *
 * @since 1.0.0
 *
 * @param string $margin This must be a string, not a boolean since its going directly into JS.
 */
function envira_gallery_themes_dynamic_margin( $margin, $data ) {
    // Get gallery theme
    $instance = Envira_Gallery_Shortcode::get_instance();
    $lightbox_theme = $instance->get_config( 'lightbox_theme', $data );

    switch ( $lightbox_theme ) {

        case 'base_light':
        case 'modern-dark':
        case 'modern-light':
        case 'space_dark':
        case 'space_light':
        case 'burnt_dark':
        case 'burnt_light':
        case 'box_dark':
        case 'box_light':
            $margin = 'true';
            break;
    }

    return $margin;
}

/**
 * Set the Dynamic Margin amount per theme
 *
 * @since 1.0.0
 *
 * @param int $amount the dynamic margin amount.
 */
function envira_gallery_themes_dynamic_margin_amount( $amount, $data ) {
    // Get gallery theme
    $instance = Envira_Gallery_Shortcode::get_instance();
    $lightbox_theme = $instance->get_config( 'lightbox_theme', $data );

    switch ( $lightbox_theme ) {

        case 'modern-dark':
        case 'modern-light':
            $amount = 40;
            break;

        case 'space_dark':
        case 'space_light':
        case 'burnt_dark':
        case 'burnt_light':
            $amount = 0;
            break;

        case 'box_dark':
        case 'box_light':
            $amount = 30;
            break;
    }

    return $amount;
}

/**
 * Revise thumbnails if the lightbox theme requires it
 *
 * @since 1.0.0
 *
 * @param string $key The user license key.
 */
function envira_gallery_themes_width( $width, $lightbox_theme ) {

    switch ( $lightbox_theme ) {

        case 'modern-dark' :
        case 'modern-light' :
        case 'base_light':
        case 'space_light':
        case 'space_dark':
        case 'box_dark':
        case 'box_light':
        case 'burnt_dark':
        case 'burnt_light':

            $width = 200;

        break;

    }

    return $width;  

}


/**
 * Revise thumbnails if the lightbox theme requires it
 *
 * @since 1.0.0
 *
 * @param string $key The user license key.
 */
function envira_gallery_themes_height( $height, $lightbox_theme ) {

    switch ( $lightbox_theme ) {

        case 'modern-dark' :
        case 'modern-light' :
        case 'base_light':
        case 'space_light':
        case 'space_dark':
        case 'box_dark':
        case 'box_light':
        case 'burnt_dark':
        case 'burnt_light':

            $height = 120;

        break;

    }

    return $height;  

}

/**
 * Set the title display per theme
 *
 * @since 1.0.0
 *
 * @param string|int this is either a string or an integer and can be set accordingly.
 */
function envira_gallery_themes_title_type( $title_display, $data ) {

    // Get gallery theme
    $instance = Envira_Gallery_Shortcode::get_instance();
    $lightbox_theme = $instance->get_config( 'lightbox_theme', $data );

    switch ( $lightbox_theme ) {

        case 'base_light':
            $title_display = 'fixed';
            break;

    }

    return $title_display;

}

/**
 * Revise thumbnails if the lightbox theme requires it
 *
 * @since 1.0.0
 *
 * @param string $key The user license key.
 */
function envira_gallery_themes_output_item_data( $item, $id, $data, $i ) {

    // Determine whether data is for a gallery or album
    $post_type = get_post_type( $data['id'] );

    // If post type is false, we're probably on a dynamic gallery/album
    // Grab the ID from the config
    if ( ! $post_type && isset( $data['config']['id'] ) ) {
        $post_type = get_post_type( $data['config']['id'] );
    }

    switch ( $post_type ) {
        case 'envira':
            $instance = Envira_Gallery_Shortcode::get_instance();
            $gallery_theme = $instance->get_config( 'gallery_theme', $data );
            break;
        case 'envira_album':
            $instance = Envira_Albums_Shortcode::get_instance();
            $gallery_theme = $instance->get_config( 'gallery_theme', $data );
            break;
    }

    $lightbox_theme = $instance->get_config( 'lightbox_theme', $data );

    // Make this a little more readable
    $image_id = $id;

    require_once(ABSPATH . 'wp-admin/includes/image.php');

    switch ( $lightbox_theme ) {

        case 'modern-dark' :
        case 'modern-light' :

            // replace the current thumbs, which are likely 75x50, with a 200x120 thumbnail

            add_image_size( $lightbox_theme . '-thumbnail', 200, 120, true ); // Hard crop left top

            $fullsizepath = get_attached_file($image_id);
            if (false === $fullsizepath || !file_exists($fullsizepath))
                return;

            $custom_size_image = wp_get_attachment_image_src( $image_id, $lightbox_theme . '-thumbnail'); 
            if ( $custom_size_image[3] == false ) {
                // generate thumbnail
                wp_update_attachment_metadata( $image_id, wp_generate_attachment_metadata( $image_id, $fullsizepath ) );

            }

            // ok, so the new thumbnail should be created, so let's swap out the thumb url in $item

            $item['thumb'] = $custom_size_image[0];

        break;

        case 'base_light':
        case 'space_light':
        case 'space_dark':
        case 'box_dark':
        case 'box_light':
        case 'burnt_dark':
        case 'burnt_light':

            // replace the current thumbs, which are likely 75x50, with a 200x120 thumbnail

            add_image_size( $lightbox_theme . '-thumbnail', 200, 120, true ); // Hard crop left top

            $fullsizepath = get_attached_file($image_id);
            if (false === $fullsizepath || !file_exists($fullsizepath))
                return;

            $custom_size_image = wp_get_attachment_image_src( $image_id, $lightbox_theme . '-thumbnail'); 
            if ( $custom_size_image[3] == false ) {
                // generate thumbnail
                wp_update_attachment_metadata( $image_id, wp_generate_attachment_metadata( $image_id, $fullsizepath ) );
            }

            // ok, so the new thumbnail should be created, so let's swap out the thumb url in $item

            $item['thumb'] = $custom_size_image[0];

            $item['caption'] = !empty($item['caption']) ? $item['caption'] . '<div class="envirabox-title-actions">' . apply_filters( 'envirabox_title_actions', '', $data, null ) . '</div>' : $item['caption'];

        break;

    }

    return $item;

}

/**
 * Adds custom HTML output for specific gallery themes.
 *
 * @since 1.0.0
 *
 * @param string $output  String of gallery output.
 * @param mixed $id       The ID of the gallery.
 * @param array $item     Array of data about the image.
 * @param array $data     Array of gallery data.
 * @param int $i          The current index in the gallery.
 * @return string $output Amended string of gallery output.
 */
function envira_gallery_themes_output( $output, $id, $item, $data, $i ) {

    // Determine whether data is for a gallery or album
    $post_type = get_post_type( $data['id'] );

    // If post type is false, we're probably on a dynamic gallery/album
    // Grab the ID from the config
    if ( ! $post_type && isset( $data['config']['id'] ) ) {
        $post_type = get_post_type( $data['config']['id'] );
    }

    $instance = false;

    switch ( $post_type ) {
        case 'envira':
            $instance = Envira_Gallery_Shortcode::get_instance();
            $gallery_theme = $instance->get_config( 'gallery_theme', $data );
            break;
        case 'envira_album':
            $instance = Envira_Albums_Shortcode::get_instance();
            $gallery_theme = $instance->get_config( 'gallery_theme', $data );
            break;
        case 'post':
            $instance = Envira_Gallery_Shortcode::get_instance();
            $gallery_theme = $instance->get_config( 'gallery_theme', $data );
            break;
    }

    // If there's no instance, bail or risk fatal error
    if ( !$instance || ( ! $instance instanceof Envira_Gallery_Shortcode && ! $instance instanceof Envira_Albums_Shortcode ) ) {
       return $output;
    }

    // Check the columns - if it's zero, then it's the automatic layout which means we don't add the HTML because it's a justified gallery
    if ( $instance->get_config( 'columns', $data ) == 0 ) {
        return $output;
    }

    switch ( $gallery_theme ) {

        case 'captioned':
        case 'polaroid':
            if ( empty( $item['title'] ) ) {
                break;
            }

            // Build HTML
            $caption  = '<div class="envira-gallery-captioned-data">';
                $caption .= '<p class="envira-gallery-captioned-text">';
                    $output_caption = ! empty( $item['caption'] ) ? $item['caption'] : $item['title'];
                    // add a <br> if there's a line break
                    $output_caption = str_replace( '
', '<br/>', ( $output_caption ) );
                    $caption .= $output_caption;
                $caption .= '</p>';
            $caption .= '</div>';
            $caption  = apply_filters( 'envira_gallery_themes_captioned_output', $caption, $id, $item, $data, $i );
            
            // Return output
            return $output . $caption;

            break;
    }

    // Return the output.
    return $output;

}

/**
 * Forces supersize on Base Light theme for galleries/albums.
 *
 * @since 1.2.3.1
 *
 */
function envira_gallery_themes_gallery_get_config( $config, $key ) {

    // We need supersize for the base light theme, so we are forcing it here
    if ( $key == 'supersize' && $config['lightbox_theme'] == 'base_light' ) {
        $config[ $key ] = 1;
    }

    if ( $key == 'toolbar' ) {
        switch ( $config['lightbox_theme'] ) {
            case 'base_light':
            case 'space_light':
            case 'space_dark':
            case 'box_dark':
            case 'box_light':
            case 'burnt_dark':
            case 'burnt_light':
                $config[ $key ] = 0;
                break;
        }
    }

    return $config;

}

/**
 * Set the arrows
 *
 * @since 1.0.0
 *
 * @param string|int this is either a string or an integer and can be set accordingly.
 */
function envira_gallery_themes_arrows( $arrows, $data ) {

    // Get gallery theme
    $instance = Envira_Gallery_Shortcode::get_instance();
    $lightbox_theme = $instance->get_config( 'lightbox_theme', $data );

    switch ( $lightbox_theme ) {

        case 'base_light':
        case 'space_light':
        case 'space_dark':
        case 'box_dark':
        case 'box_light':
        case 'burnt_dark':
        case 'burnt_light':
        case 'modern-dark':
        case 'modern-light':
            $arrows = 'true';
            break;

    }

    return $arrows;

}

/**
 * Set the arrows
 *
 * @since 1.0.0
 *
 * @param string|int this is either a string or an integer and can be set accordingly.
 */
function envira_gallery_themes_gallery_thumbs_position( $position, $data ) {

    // Get gallery theme
    $instance = Envira_Gallery_Shortcode::get_instance();
    $lightbox_theme = $instance->get_config( 'lightbox_theme', $data );

    switch ( $lightbox_theme ) {

        case 'base_light':
        case 'space_light':
        case 'space_dark':
        case 'modern-dark':
        case 'modern-light':
            $position = 'bottom';
            break;

    }

    return $position;

}

function envira_gallery_themes_action_divs_root( $root, $data ) {

    // Get gallery theme
    $instance = Envira_Gallery_Shortcode::get_instance();
    $lightbox_theme = $instance->get_config( 'lightbox_theme', $data );

    switch ( $lightbox_theme ) {

        case 'modern-dark':
        case 'modern-light':
            $root = 'true';
            break;

    }

    return $root;

}

function envira_gallery_themes_nav_divs_root( $root, $data ) {

    // Get gallery theme
    $instance = Envira_Gallery_Shortcode::get_instance();
    $lightbox_theme = $instance->get_config( 'lightbox_theme', $data );

    switch ( $lightbox_theme ) {

        case 'modern-dark':
        case 'modern-light':
            $root = 'true';
            break;

    }

    return $root;

}