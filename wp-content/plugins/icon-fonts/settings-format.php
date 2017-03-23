<?php
/**
 * Format settings template for "Icon Fonts" Wordpress plugin
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

$settings = get_option( 'icon_fonts_settings' );

// Get color setting
if( ! empty ( $settings['color'] ) ) {
	$color = $settings['color'];
} else {
	$color = '';
}

// Get size setting
if( ! empty ( $settings['size'] ) ) {
	$size = $settings['size'];
} else {
	$size = '';
}

// Get classes setting
if( ! empty ( $settings['classes'] ) ) {
	$classes = $settings['classes'];
} else {
	$classes = '';
}

// Get styles setting
if( ! empty ( $settings['styles'] ) ) {
	$styles = $settings['styles'];
} else {
	$styles = '';
}

?>

<div>
	<div style='margin-top: 15px;'>
		<input id='icon-fonts-color-picker' type='text' name='icon_fonts_settings[color]' value='<?php echo $color; ?>' data-default-color='#000000' />	
		<p class='description'><?php _e( 'Select a color for new icons, or leave blank to use text color.', 'icon-fonts' ); ?></p>
	</div>
	<div style='margin-top: 15px;'>
		<input id='icon-fonts-font-size' type='number' name='icon_fonts_settings[size]' class='small-text' value='<?php echo $size; ?>' min='0' />	
		<label for='icon-fonts-font-size'><?php _e( 'Pixels', 'icon-fonts' ); ?></label>
		<p class='description'><?php _e( 'Select a font size for new icons, or select zero to use text size.', 'icon-fonts' ); ?></p>
	</div>	
	<div style='margin-top: 15px;'>
		<input id='icon-fonts-classes' type='text' name='icon_fonts_settings[classes]' class='regular-text code' value='<?php echo $classes; ?>' />	
		<p class='description'><?php _e( 'Enter additional classes for new icons, separated by spaces.', 'icon-fonts' ); ?></p>
	</div>
	<div style='margin-top: 15px;'>
		<input id='icon-fonts-styles' type='text' name='icon_fonts_settings[styles]' class='regular-text code' value='<?php echo $styles; ?>' />	
		<p class='description'><?php _e( 'Enter additional styles for new icons, separated by semi-colons.', 'icon-fonts' ); ?></p>
	</div>		
</div>