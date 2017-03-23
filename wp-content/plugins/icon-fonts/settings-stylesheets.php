<?php
/**
 * Stylesheet settings template for "Icon Fonts" Wordpress plugin
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

$settings = get_option( 'icon_fonts_settings' );

// Get framework setting
if( ! empty ( $settings['framework'] ) ) {
	$framework = $settings['framework'];
} else {
	$framework = 'regular';
}

$checked = empty( $settings['minimised'] ) ? '' : "checked='checked'"; 
?>
<p>
	<input id='icon-fonts-minimised-stylesheet' type='checkbox' name='icon_fonts_settings[minimised]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-minimised-stylesheet'><?php _e( 'Minimised', 'icon-fonts' ); ?></label>
	<p class='description'><?php _e( 'Use minimised stylesheets.', 'icon-fonts' ); ?></p>
</p>
<div style='margin-top: 15px;'>
<?php $checked = ( $framework == 'regular' ) ? "checked='checked'" : ''; ?>
	<p>
		<input id='icon-fonts-regular-framework' type='radio' name='icon_fonts_settings[framework]' value='' <?php echo $checked; ?> />
		<label for='icon-fonts-regular-framework'><?php _e( 'No Framework', 'icon-fonts' ); ?></label>
	</p>
<?php $checked = ( $framework == 'bootstrap' ) ? "checked='checked'" : ''; ?>
	<p>
		<input id='icon-fonts-bootstrap-framework' type='radio' name='icon_fonts_settings[framework]' value='bootstrap' <?php echo $checked; ?> />
		<label for='icon-fonts-bootstrap-framework'><?php _e( 'Bootstrap', 'icon-fonts' ); ?></label>
	</p>
<?php $checked = ( $framework == 'foundation' ) ? "checked='checked'" : ''; ?>
	<p>
		<input id='icon-fonts-foundation-framework' type='radio' name='icon_fonts_settings[framework]' value='foundation' <?php echo $checked; ?> />
		<label for='icon-fonts-foundation-framework'><?php _e( 'Foundation', 'icon-fonts' ); ?></label>	
	</p>
	<p class='description'><?php _e( 'Select a framework stylesheet for Open Iconic and OpenWeb.', 'icon-fonts' ); ?></p>
</div>
