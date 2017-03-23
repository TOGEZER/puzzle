<?php
/**
 * Settings template for "Icon Fonts" Wordpress plugin
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
 
?>
<div id='icon-fonts-settings' class='wrap'>
	<div class='icon32' id='icon-fonts-icon'><br></div>
	<h2><?php _e( 'Icon Fonts Settings', 'icon-fonts' ); ?></h2>
	<form method='post' action='options.php'>
		<?php settings_fields( 'icon_fonts_settings_group' ); ?>
		<?php do_settings_sections( 'icon_fonts_settings_page' ); ?>
		<p class='submit'>
			<input id='submit' type='submit' name='submit' class='button-primary' value='<?php esc_attr_e( 'Save Changes' ); ?>' />
		</p>
	</form>
</div>