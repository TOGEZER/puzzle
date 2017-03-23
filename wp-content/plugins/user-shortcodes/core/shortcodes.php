<?php

/*
	Create userShorcodes Class

*/



if (!class_exists('userShortcodes')) {

class userShortcodes {


	function __construct (){

			/* Simple User Shortcodes */
	
			add_shortcode ("currentuser_username" , array ($this, 'user_shortcode'));
			add_shortcode ("currentuser_useremail" , array ($this, 'email_shortcode'));
			add_shortcode ("currentuser_firstname" , array ($this, 'firstname_shortcode'));
			add_shortcode ("currentuser_lastname" , array ($this, 'lastname_shortcode'));
			add_shortcode ("currentuser_displayname" , array ($this, 'displayname_shortcode'));
			add_shortcode ("currentuser_id" , array ($this, 'id_shortcode'));
			
	
	}
	
	
	function user_shortcode($atts, $content){
	    
	    if (!is_user_logged_in()) return;
	
		 $current_user = wp_get_current_user();
		 return $current_user->user_login;
		 
	}
	
	
	function email_shortcode ($atts, $content){
	    
	    if (!is_user_logged_in()) return;
	
		$current_user = wp_get_current_user();
		return $current_user->user_email;

	
	}
	
	function firstname_shortcode ($atts, $content){
	    
	    if (!is_user_logged_in()) return;
	
		$current_user = wp_get_current_user();
		return $current_user->user_firstname;

	
	}
	
	function lastname_shortcode ($atts, $content){
	    
	    if (!is_user_logged_in()) return;
	
		$current_user = wp_get_current_user();
		return $current_user->user_lastname;

	
	}
	
	function displayname_shortcode ($atts, $content){
	    
	    if (!is_user_logged_in()) return;
	
		$current_user = wp_get_current_user();
		return $current_user->display_name;

	
	}
	
	function id_shortcode ($atts, $content){
	    
	    if (!is_user_logged_in()) return;
	
		$current_user = wp_get_current_user();
		return $current_user->ID;

	
	}


	}


}