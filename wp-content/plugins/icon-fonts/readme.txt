=== Icon Fonts ===
Contributors: sydcode
Donate link: http://
Tags: dashicons, font, font-awesome, foundation, genericons, icon, icon font, webfont
Requires at least: 3.3.0
Tested up to: 4.2.2
Stable tag: 1.0.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin adds support for 18 free icon fonts (over 6000 icons).

== Description ==

This plugin adds support for 18 free icon fonts \(over 6000 icons\). 

[Dashicons](https://developer.wordpress.org/resource/dashicons/) \(218 icons\)    
[Elegant](http://www.elegantthemes.com/blog/resources/elegant-icon-font) \(360 icons\)    
[Elusive](http://elusiveicons.com/) \(304 icons\)   
[Entypo](https://github.com/danielbruce/entypo) \(284 icons\)   
[Font Awesome](http://fortawesome.github.io/Font-Awesome/) \(519 icons\)   
[Foundation](http://zurb.com/playground/foundation-icon-fonts-3) \(284 icons\)   
[Genericons](http://genericons.com/) \(147 icons\)   
[IcoMoon Free](https://icomoon.io/icons-icomoon.html) \(491 icons\)   
[Ionicons](http://ionicons.com/) \(734 icnons\)   
[Map Icons](http://map-icons.com/) \(176 icons\)   
[Material Design](http://materialdesignicons.com/) \(1062 icons\)   
[MFG Labs](http://mfglabs.github.io/mfglabs-iconset/) \(186 icons\)   
[Octicons](https://octicons.github.com/) \(179 icons\)   
[Open Iconic](https://useiconic.com/open/) \(229 icons\)   
[OpenWeb](http://pfefferle.github.io/openwebicons/) \(118 icons\)   
[Sosa](http://tenbytwenty.com/?xxxx_posts=sosa) \(122 icons\)   
[Themify](http://themify.me/themify-icons) \(352 icons\)   
[Typicons](http://typicons.com/) \(336 icons\)   

Each font is freely distributed under an open source licence.    
Contact the author of a font if you have questions about using it.

Credits:   
[Jaime Caballero](http://jaicab.com/Sosa-Icon-Font-CSS/) for his Sosa stylesheet.    
[pnull](https://gist.github.com/pnull/4510484) for his Entypo stylesheet.   

== Installation ==

1. Upload the "icon-fonts" directory to the "/wp-content/plugins/" directory.
2. Activate the plugin via the "Plugins" menu on the Wordpress dashboard.
3. Activate the required icon fonts in the settings panel.
4. Use the star button in the visual editor to insert icons.

== Screenshots ==

1. Settings Panel

2. Editor Dialog

== Frequently Asked Questions ==

= 1. Are these fonts really free to use? =
Yes. Some have different licenses but all are open source and free to use. 

= 2. Why is there a soft hyphen inside each icon element? =
An element needs content to avoid being removed by the editor. A soft hyphen is used because a non-breaking space causes alignment issues, and a comment seems to make the editor crash.

= 3. Why are icons not showing? =
Unfortunately, some fonts use the same class name which causes problems. Starting at the top of the list, deactivate fonts until the missing icons appear. 

= 4. Have you changed the fonts in any way? =
No. The original fonts and stylesheets have not been changed. Non-essential files from the downloads have been removed, licenses were added when missing, and some minimized stylesheets were created with [Refresh SF](http://refresh-sf.com/).

= 5. Will more fonts be added later? =
Certainly! Please post a request in the forum if you want a particular font.

= 6. Which fonts have a stylesheet for the Bootstrap framework? =
Openweb and Open Iconic.

= 7. Which fonts have a stylesheet for the Foundation framework? =
Open Iconic.

= 8. Are icons hidden from assistive technology? =
Yes. Each icon has an `aria-hidden="true"` and `role="presentation"` attribute.

= 9. Does the plugin change the editor? =
Yes. The plugin adds a star button to the toolbar, and it allows elements to use the above attributes.

= 10. Will icons still appear without the plugin? =
No. The plugin must be active to load the font stylesheets, and it stops the editor from removing the above attributes when a post is edited.

= 11. Can icons be added to widgets or menus? =
No. An update will add icon buttons for widgets and menus.


== Upgrade Notice ==

No upgrade notices

== Changelog ==

= 1.0.0 =
* First release