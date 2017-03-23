<?php
/**
 * Jetpack Compatibility File
 * See: https://jetpack.me/
 *
 * @package Libre
 */

/**
 * Add theme support for Infinite Scroll.
 * See: https://jetpack.me/support/infinite-scroll/
 */
function libre_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container'      => 'main',
		'render'         => 'libre_infinite_scroll_render',
		'footer'         => 'masthead',
		'footer_widgets' => array( 'footer-1', 'footer-2', 'footer-3' ),
	) );

	add_theme_support( 'jetpack-responsive-videos' );

	add_theme_support( 'post-thumbnails' );
	add_image_size( 'libre-site-logo', '300', '300' );

	add_theme_support( 'site-logo', array( 'size' => 'libre-site-logo' ) );

} // end function libre_jetpack_setup
add_action( 'after_setup_theme', 'libre_jetpack_setup' );

/**
 * Custom render function for Infinite Scroll.
 */
function libre_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content', get_post_format() );
	}
} // end function libre_infinite_scroll_render
