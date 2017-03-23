<?php 
/*
Plugin Name: User Shortcodes 
Plugin URI: http://happyplugins.com/user-shortcodes
Description: Display user information using shortcodes
Author: HappyPlugins
Version: 1.0.1
License: GPL version 2 or any later version
Author URI: http://happyplugins.com
Text Domain: user-shortcodes
*/

/*  Copyright 2013  HappyPlugins  (email : freeplugins@happyplugins.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



require_once(dirname(__FILE__) . '/core/plugin_information.php');
require_once(dirname(__FILE__) . '/core/shortcodes.php');


$userShortcodes = new userShortcodes();

// require_once(dirname(__FILE__) . '/core/admin_menus.php'); /* For Future versions */
// $userShortcdoes_menu = new user_shortcodes_menus(); /* For future versions */

?>