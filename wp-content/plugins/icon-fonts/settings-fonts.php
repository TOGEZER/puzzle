<?php
/**
 * Fonts settings template for "Icon Fonts" Wordpress plugin
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

$settings = get_option( 'icon_fonts_settings' );

?>
<p style="margin-bottom: 15px;">
	<a href='https://developer.wordpress.org/resource/dashicons'><?php _e( 'Dashicons', 'icon-fonts' ); ?></a>
	<?php _e( ' (218 icons) is the official icon font of Wordpress.', 'icon-fonts' ); ?>
</p>
<?php $checked = empty( $settings['elegant'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-elegant' type='checkbox' name='icon_fonts_settings[elegant]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-elegant'><?php _e( 'Elegant (360 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://www.elegantthemes.com/blog/resources/elegant-icon-font'><?php _e( 'Elegant Themes', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['elusive'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-elusive' type='checkbox' name='icon_fonts_settings[elusive]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-elusive'><?php _e( 'Elusive (304 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://elusiveicons.com/'><?php _e( 'Dave Gandy &amp; Team Redux', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['entypo'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-entypo' type='checkbox' name='icon_fonts_settings[entypo]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-entypo'><?php _e( 'Entypo (284 icons) by ', 'icon-fonts' ); ?></label>
	<a href='https://github.com/danielbruce/entypo'><?php _e( 'Daniel Bruce', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['font-awesome'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-awesome' type='checkbox' name='icon_fonts_settings[font-awesome]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-awesome'><?php _e( 'Font Awesome (519 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://fortawesome.github.io/Font-Awesome/'><?php _e( 'Dave Gandy', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['foundation'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-foundation' type='checkbox' name='icon_fonts_settings[foundation]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-foundation'><?php _e( 'Foundation (284 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://zurb.com/playground/foundation-icon-fonts-3'><?php _e( 'ZURB Studios', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['genericons'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-genericons' type='checkbox' name='icon_fonts_settings[genericons]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-genericons'><?php _e( 'Genericons (147 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://genericons.com/'><?php _e( 'Automattic', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['icomoon-free'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-icomoon' type='checkbox' name='icon_fonts_settings[icomoon-free]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-icomoon'><?php _e( 'Icomoon Free (491 icons) by ', 'icon-fonts' ); ?></label>
	<a href='https://icomoon.io/icons-icomoon.html'><?php _e( 'Keyamoon', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['ionicons'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-ionicons' type='checkbox' name='icon_fonts_settings[ionicons]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-ionicons'><?php _e( 'Ionicons (734 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://ionicons.com/'><?php _e( 'Ben Sperry', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['map-icons'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-mapicons' type='checkbox' name='icon_fonts_settings[map-icons]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-mapicons'><?php _e( 'Map (176 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://map-icons.com/'><?php _e( 'Scott de Jonge', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['material-design'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-material' type='checkbox' name='icon_fonts_settings[material-design]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-material'><?php _e( 'Material Design (1062 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://materialdesignicons.com/'><?php _e( 'Google &amp; Austin Andrews', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['mfglabs'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-mfglabs' type='checkbox' name='icon_fonts_settings[mfglabs]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-mfglabs'><?php _e( 'MFG Labs (186 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://mfglabs.github.io/mfglabs-iconset/'><?php _e( 'MFG Labs', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['octicons'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-octicons' type='checkbox' name='icon_fonts_settings[octicons]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-octicons'><?php _e( 'Octicons (179 icons) by ', 'icon-fonts' ); ?></label>
	<a href='https://octicons.github.com/'><?php _e( 'GitHub', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['open-iconic'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-iconic' type='checkbox' name='icon_fonts_settings[open-iconic]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-iconic'><?php _e( 'Open Iconic (229 icons) by ', 'icon-fonts' ); ?></label>
	<a href='https://useiconic.com/open/'><?php _e( 'Iconic', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['openweb'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-openweb' type='checkbox' name='icon_fonts_settings[openweb]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-openweb'><?php _e( 'OpenWeb (118 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://pfefferle.github.io/openwebicons/'><?php _e( 'Matthias Pfefferle', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['sosa'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-sosa' type='checkbox' name='icon_fonts_settings[sosa]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-sosa'><?php _e( 'Sosa (122 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://tenbytwenty.com/?xxxx_posts=sosa'><?php _e( 'Ed Merritt', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['themify'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-themify' type='checkbox' name='icon_fonts_settings[themify]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-themify'><?php _e( 'Themify (352 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://themify.me/themify-icons'><?php _e( 'Themify', 'icon-fonts' ); ?></a>
</p>
<?php $checked = empty( $settings['typicons'] ) ? '' : "checked='checked'"; ?>
<p>
	<input id='icon-fonts-typicons' type='checkbox' name='icon_fonts_settings[typicons]' value='1' <?php echo $checked; ?> />
	<label for='icon-fonts-typicons'><?php _e( 'Typicons (336 icons) by ', 'icon-fonts' ); ?></label>
	<a href='http://typicons.com/'><?php _e( 'Stephen Hutchings', 'icon-fonts' ); ?></a>
</p>