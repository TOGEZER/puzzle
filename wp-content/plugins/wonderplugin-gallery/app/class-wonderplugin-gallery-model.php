<?php 

require_once 'wonderplugin-gallery-functions.php';

class WonderPlugin_Gallery_Model {

	private $controller;
	
	function __construct($controller) {
		
		$this->controller = $controller;
	}
	
	function get_upload_path() {
		
		$uploads = wp_upload_dir();
		return $uploads['basedir'] . '/wonderplugin-gallery/';
	}
	
	function get_upload_url() {
	
		$uploads = wp_upload_dir();
		return $uploads['baseurl'] . '/wonderplugin-gallery/';
	}
	
	function eacape_html_quotes($str) {
	
		$result = str_replace("\'", "&#39;", $str);
		$result = str_replace('\"', '&quot;', $result);
		$result = str_replace("'", "&#39;", $result);
		$result = str_replace('"', '&quot;', $result);
		return $result;
	}
	
	function generate_schema_code($id, $slides, $itemTime) {
		
		$ret = "\r\n\r\n" . '<div id="wonderplugin-gallery-schema-markup-' . $id . '" class="wonderplugin-gallery-schema-markup" style="display:none;">';
		
		foreach ($slides as $slide)
		{
			$ret .= "\r\n" . '<div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">';
			$ret .= "\r\n" . '<span itemprop="name">' . $slide->title . '</span>';
			$ret .= "\r\n" . '<span itemprop="description">' . $slide->description . '</span>';
			$ret .= "\r\n" . '<meta itemprop="thumbnailUrl" content="' . $slide->image . '" />';
			$ret .= "\r\n" . '<meta itemprop="uploadDate" content="' . $itemTime . '" />';
			
			if ($slide->type == 1)
			{
				$ret .= "\r\n" . '<meta itemprop="contentURL" content="' . $slide->mp4 . '" />';
			}
			else if ($slide->type == 2 || $slide->type == 3 || $slide->type == 4 || $slide->type == 5)
			{
				$ret .= "\r\n" . '<meta itemprop="embedURL" content="' . $slide->video . '" />';
			}
			$ret .= "\r\n" . '</div>';	
		}
		
		$ret .= "\r\n" . "</div>" . "\r\n";
		
		return $ret;
	}
	
	function generate_body_code($id, $has_wrapper) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		if ( !$this->is_db_table_exists() )
		{
			return '<p>The specified gallery does not exist.</p>';
		}
		
		$ret = "";
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$data = json_decode(trim($item_row->data));
			
			if ( isset($data->publish_status) && ($data->publish_status === 0) )
			{
				return '<p>The specified gallery is trashed.</p>';
			}
			
			$itemTime = $item_row->time;
			
			foreach($data as &$value)
			{
				if ( is_string($value) )
					$value = wp_kses_post($value);
			}
			
			if (isset($data->customcss) && strlen($data->customcss) > 0)
			{
				$customcss = str_replace("\r", " ", $data->customcss);
				$customcss = str_replace("\n", " ", $customcss);
				$customcss = str_replace("GALLERYID", $id, $customcss);
				$ret .= '<style type="text/css">' . $customcss . '</style>';
			}
			
			$ret .= '<div class="wonderplugingallery-container" id="wonderplugingallery-container-' . $id . '" style="';
			
			if ( (isset($data->fullwidth) && strtolower($data->fullwidth) === 'true') || (isset($data->responsive) && strtolower($data->responsive) === 'true' && !isset($data->fullwidth)) )
				$ret .= 'max-width:100%;';
			else
				$ret .= 'max-width:' . $data->width . 'px;';
			
			
			if ($has_wrapper)
				$ret .= 'margin:0 auto 180px;';
			else
				$ret .= 'margin:0 auto;';
			
			$ret .= '">';
			
			// div data tag
			$ret .= '<div class="wonderplugingallery" id="wonderplugingallery-' . $id . '"';
			if ( isset($data->specifyid) && strtolower($data->specifyid) === 'true' )
				$ret .= ' data-galleryid="'. $id . '"';
			
			$ret .= ' data-width="' . $data->width . '" data-height="' . $data->height . '" data-skin="' . $data->skin . '"';
			
			if (isset($data->dataoptions) && strlen($data->dataoptions) > 0)
			{
				$ret .= ' ' . stripslashes($data->dataoptions);
			}
			
			$boolOptions = array('random', 'autoslide', 'autoplayvideo', 'schemamarkup', 'hidetitlewhenvideoisplaying', 'disablehovereventontouch', 'autoslideandplayafterfirstplayed', 'html5player', 'responsive', 'fullwidth', 'showtitle', 'showdescription', 'showplaybutton', 'showfullscreenbutton', 'showtimer', 'showcarousel', 'galleryshadow', 'slideshadow', 'thumbshowtitle', 'thumbshadow', 'lightboxshowtitle', 'lightboxshowdescription', 'specifyid', 'donotinit', 'addinitscript', 'triggerresize', 'thumbcolumnsresponsive');
			foreach ( $boolOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . ((strtolower($data->{$key}) === 'true') ? 'true': 'false') .'"';
			}
			
			$boolOptions = array('showimgtitle', 'titlesmallscreen', 'initsocial', 'showsocial', 'showfacebook', 'showtwitter', 'showpinterest', 'socialrotateeffect', 'doshortcodeontext');
			foreach ( $boolOptions as $key )
				$ret .= ' data-' . $key . '="' . ((isset($data->{$key}) && (strtolower($data->{$key}) === 'true')) ? 'true': 'false') .'"';
			
			$valOptions = array('duration', 'slideduration', 'slideshowinterval', 'googleanalyticsaccount', 'resizemode', 'imagetoolboxmode', 'effect', 'padding', 'bgcolor', 'bgimage', 'thumbwidth', 'thumbheight', 'thumbgap', 'thumbrowgap', 'lightboxtextheight', 'lightboxtitlecss', 'lightboxdescriptioncss', 'titlecss', 'descriptioncss', 'titleheight', 'titlesmallscreenwidth', 'titleheightsmallscreen',
					'socialmode', 'socialposition', 'socialpositionlightbox', 'socialdirection', 'socialbuttonsize', 'socialbuttonfontsize',
					'triggerresizedelay', 'thumbmediumsize', 'thumbsmallsize', 'thumbmediumwidth', 'thumbmediumheight', 'thumbsmallwidth', 'thumbsmallheight', 'imgtitle');
			foreach ( $valOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . $data->{$key} . '"';
			}
				
			$ret .= ' data-jsfolder="' . WONDERPLUGIN_GALLERY_URL . 'engine/"'; 
			$ret .= ' style="display:none;" >';
			
			$hasVideo = false;
			
			if (isset($data->slides) && count($data->slides) > 0)
			{
				// process posts
				$items = array();
				foreach ($data->slides as $slide)
				{
					if ($slide->type == 6)
					{
						$items = array_merge($items, $this->get_post_items($slide));
					}
					else
					{
						$items[] = $slide;
					}
				}
				
				foreach ($items as $slide)
				{
					foreach($slide as &$value)
					{
						if ( is_string($value) )
							$value = wp_kses_post($value);
					}
					
					if ($slide->type == 10)
					{
						$ret .= '<a href="#" data-mediatype=13 data-youtubeapikey="' . $slide->youtubeapikey . '" data-youtubeplaylistid="' . $slide->youtubeplaylistid . '"';
						if (isset($slide->youtubeplaylistmaxresults) && $slide->youtubeplaylistmaxresults)
							$ret .= ' data-youtubeplaylistmaxresults="' . $slide->youtubeplaylistmaxresults . '"';
						$ret .= '><img class="html5galleryimg"></a>';
					}
					else
					{
						if ( isset($data->doshortcodeontext) && (strtolower($data->doshortcodeontext) === 'true') )
						{
							if ($slide->title && strlen($slide->title) > 0)
								$slide->title = do_shortcode($slide->title);
						
							if ($slide->description && strlen($slide->description) > 0)
								$slide->description = do_shortcode($slide->description);
						}
						
						$ret .= '<a';
						if ($slide->type == 0)
						{
							$ret .=' href="' . $slide->image . '" data-mediatype=1';
						}
						else if ($slide->type == 1)
						{
							$hasVideo = true;
							
							$ret .=' href="' . $slide->mp4 . '"';
							if (isset($slide->image) && $slide->image)
								$ret .=' data-poster="' . $slide->image . '"';
							if (isset($slide->hdmp4) && $slide->hdmp4)
								$ret .=' data-hd="' . $slide->hdmp4 . '"';
							if (isset($slide->webm) && $slide->webm)
								$ret .=' data-webm="' . $slide->webm . '"';
							if (isset($slide->hdwebm) && $slide->hdwebm)
								$ret .=' data-hdwebm="' . $slide->hdwebm . '"';
						}
						else if ($slide->type == 2 || $slide->type == 3 || $slide->type == 4 || $slide->type == 5)
						{
							$hasVideo = true;
							
							$ret .=' href="' . $slide->video . '"';
							if ($slide->type == 5)
								$ret .= " data-mediatype=11";
							if (isset($slide->image) && $slide->image)
								$ret .=' data-poster="' . $slide->image . '"';
						}
						
						if (isset($slide->weblink) && strlen($slide->weblink) > 0)
						{
							$ret .= ' data-link="' . $slide->weblink . '"';
							if (isset($slide->linktarget) && strlen($slide->linktarget) > 0)
								$ret .= ' data-linktarget="' . $slide->linktarget . '"';
						}
												
						$ret .= '><img class="html5galleryimg" src="' . ((isset($data->showcarousel) && strtolower($data->showcarousel) === 'true') ? $slide->thumbnail : '') . '"';
						
						if ( isset($slide->altusetitle) && (strtolower($slide->altusetitle) === 'false') && isset($slide->alt) )
							$ret .= ' alt="' . $this->eacape_html_quotes($slide->alt) . '" data-title="' . $this->eacape_html_quotes($slide->title) . '"';
						else
							$ret .= ' alt="' . $this->eacape_html_quotes($slide->title) . '"';
						
						if ( isset($data->showimgtitle) && (strtolower($data->showimgtitle) === 'true') && isset($data->imgtitle) )
						{
							if ($data->imgtitle == 'title' && isset($slide->title))
								$ret .= ' title="' . $this->eacape_html_quotes($slide->title) . '"';
							else if ($data->imgtitle == 'description' && isset($slide->description))
								$ret .= ' title="' . $this->eacape_html_quotes($slide->description) . '"';
							else if ($data->imgtitle == 'alt' && isset($slide->alt))
								$ret .= ' title="' . $this->eacape_html_quotes($slide->alt) . '"';
						}
						
						if (isset($slide->description) && strlen($slide->description) > 0)
							$ret .= ' data-description="' . $this->eacape_html_quotes($slide->description) . '"';
						$ret .= '></a>';
					}
				}
			}
			if ('F' == 'F')
				$ret .= '<div class="wonderplugin-engine"><a href="http://www.wonderplugin.com/wordpress-gallery/" title="'. get_option('wonderplugin-gallery-engine')  .'">' . get_option('wonderplugin-gallery-engine') . '</a></div>';
			$ret .= '</div>';
			
			$ret .= '</div>';
			
			if (isset($data->addinitscript) && strtolower($data->addinitscript) === 'true')
			{
				$ret .= '<script>jQuery(document).ready(function(){jQuery(".wonderplugin-engine").css({display:"none"});jQuery(".wonderplugingallery").wonderplugingallery({forceinit:true});});</script>';				
			}
			
			if (isset($data->triggerresize) && strtolower($data->triggerresize) === 'true')
			{
				$ret .= '<script>jQuery(document).ready(function(){';
				if ($data->triggerresizedelay > 0)
					$ret .= 'setTimeout(function(){jQuery(window).trigger("resize");},' . $data->triggerresizedelay . ');';
				else
					$ret .= 'jQuery(window).trigger("resize");';
				$ret .= '});</script>';
			}
			
			if (isset($data->slides) && (count($data->slides) > 0) && $hasVideo && isset($data->schemamarkup) && (strtolower($data->schemamarkup) === 'true'))
			{
				$ret .= $this->generate_schema_code($id, $data->slides, $itemTime);
			}
			
			if (isset($data->customjs) && strlen($data->customjs) > 0)
			{
				$customjs = str_replace("\r", " ", $data->customjs);
				$customjs = str_replace("\n", " ", $customjs);
				$customjs = str_replace("GALLERYID", $id, $customjs);
				$ret .= '<script language="JavaScript">' . $customjs . '</script>';
			}
		}
		else
		{
			$ret = '<p>The specified gallery id does not exist.</p>';
		}
		return $ret;
	}
	
	function get_post_items($options) {
	
		$posts = array();
	
		if ($options->postcategory == -1)
		{
			$posts = wp_get_recent_posts(array(
					'numberposts' 	=> $options->postnumber,
					'post_status' 	=> 'publish'
			));
		}
		else
		{
			$posts = get_posts(array(
					'numberposts' 	=> $options->postnumber,
					'post_status' 	=> 'publish',
					'category'		=> $options->postcategory
			));
		}
	
		$items = array();
	
		foreach($posts as $post)
		{
			if (is_object($post))
				$post = get_object_vars($post);
	
			$thumbnail = '';
			$image = '';
			if ( has_post_thumbnail($post['ID']) )
			{
				$featured_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post['ID']), $options->featuredimagesize);
				$thumbnail = $featured_thumb[0];
	
				$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($post['ID']), 'full');
				$image = $featured_image[0];
			}
	
			$excerpt = $post['post_excerpt'];
			if (empty($excerpt))
			{
				$excerpts = explode( '<!--more-->', $post['post_content'] );
				$excerpt = $excerpts[0];
				$excerpt = strip_tags( str_replace(']]>', ']]&gt;', strip_shortcodes($excerpt)) );
			}
			$excerpt = wonderplugin_gallery_wp_trim_words($excerpt, $options->excerptlength);
	
			$post_item = array(
					'type'			=> 0,
					'image'			=> $image,
					'thumbnail'		=> $thumbnail,
					'title'			=> $post['post_title'],
					'description'	=> $excerpt,
					'weblink'		=> get_permalink($post['ID']),
					'linktarget'	=> $options->postlinktarget
			);
			
			if (isset($options->posttitlelink) && strtolower($options->posttitlelink) === 'true')
			{
				$post_item['title'] = '<a class="html5gallery-posttitle-link" href="' . $post_item['weblink'] . '"';
				if (isset($post_item['linktarget']) && strlen($post_item['linktarget']) > 0)
					$post_item['title'] .= ' target="' . $post_item['linktarget'] . '"';
				$post_item['title'] .= '>' . $post['post_title'] . '</a>';
			}
				
			$items[] = (object) $post_item;
		}
	
		return $items;
	}
	
	function delete_item($id) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		$ret = $wpdb->query( $wpdb->prepare(
				"
				DELETE FROM $table_name WHERE id=%s
				",
				$id
		) );
		
		return $ret;
	}
	
	function trash_item($id) {
	
		return $this->set_item_status($id, 0);
	}
	
	function restore_item($id) {
	
		return $this->set_item_status($id, 1);
	}
	
	function set_item_status($id, $status) {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
	
		$ret = false;
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$data = json_decode($item_row->data, true);
			$data['publish_status'] = $status;
			$data = json_encode($data);
	
			$update_ret = $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET data=%s WHERE id=%d", $data, $id ) );
			if ( $update_ret )
				$ret = true;
		}
	
		return $ret;
	}
	
	function clone_item($id) {
	
		global $wpdb, $user_ID;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		$cloned_id = -1;
		
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$time = current_time('mysql');
			$authorid = $user_ID;
			
			$ret = $wpdb->query( $wpdb->prepare(
					"
					INSERT INTO $table_name (name, data, time, authorid)
					VALUES (%s, %s, %s, %s)
					",
					$item_row->name . " Copy",
					$item_row->data,
					$time,
					$authorid
			) );
				
			if ($ret)
				$cloned_id = $wpdb->insert_id;
		}
	
		return $cloned_id;
	}
	
	function is_db_table_exists() {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
	
		return ( strtolower($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) == strtolower($table_name) );
	}
	
	function is_id_exist($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		$slider_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		return ($slider_row != null);
	}
	
	function create_db_table() {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		$charset = '';
		if ( !empty($wpdb -> charset) )
			$charset = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( !empty($wpdb -> collate) )
			$charset .= " COLLATE $wpdb->collate";
	
		$sql = "CREATE TABLE $table_name (
		id INT(11) NOT NULL AUTO_INCREMENT,
		name tinytext DEFAULT '' NOT NULL,
		data MEDIUMTEXT DEFAULT '' NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		authorid tinytext NOT NULL,
		PRIMARY KEY  (id)
		) $charset;";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	function save_item($item) {
		
		global $wpdb, $user_ID;
		
		if ( !$this->is_db_table_exists() )
		{
			$this->create_db_table();
		
			$create_error = "CREATE DB TABLE - ". $wpdb->last_error;
			if ( !$this->is_db_table_exists() )
			{
				return array(
						"success" => false,
						"id" => -1,
						"message" => $create_error
				);
			}
		}
		
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		$id = $item["id"];
		$name = $item["name"];
		
		unset($item["id"]);
		$data = json_encode($item);
		
		if ( empty($data) )
		{
			$json_error = "json_encode error";
			if ( function_exists('json_last_error_msg') )
				$json_error .= ' - ' . json_last_error_msg();
			else if ( function_exists('json_last_error') )
				$json_error .= 'code - ' . json_last_error();
		
			return array(
					"success" => false,
					"id" => -1,
					"message" => $json_error
			);
		}
		
		$time = current_time('mysql');
		$authorid = $user_ID;
		
		if ( ($id > 0) && $this->is_id_exist($id) )
		{
			$ret = $wpdb->query( $wpdb->prepare(
					"
					UPDATE $table_name
					SET name=%s, data=%s, time=%s, authorid=%s
					WHERE id=%d
					",
					$name,
					$data,
					$time,
					$authorid,
					$id
			) );
			
			if (!$ret)
			{
				return array(
						"success" => false,
						"id" => $id, 
						"message" => "UPDATE - ". $wpdb->last_error
					);
			}
		}
		else
		{
			$ret = $wpdb->query( $wpdb->prepare(
					"
					INSERT INTO $table_name (name, data, time, authorid)
					VALUES (%s, %s, %s, %s)
					",
					$name,
					$data,
					$time,
					$authorid
			) );
			
			if (!$ret)
			{
				return array(
						"success" => false,
						"id" => -1,
						"message" => "INSERT - " . $wpdb->last_error
				);
			}
			
			$id = $wpdb->insert_id;
		}
		
		return array(
				"success" => true,
				"id" => intval($id),
				"message" => "Gallery published!"
		);
	}
	
	function get_list_data() {
		
		if ( !$this->is_db_table_exists() )
			$this->create_db_table();
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
		
		$rows = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A);
		
		$ret = array();
		
		if ( $rows )
		{
			foreach ( $rows as $row )
			{
				$ret[] = array(
							"id" => $row['id'],
							'name' => $row['name'],
							'data' => $row['data'],
							'time' => $row['time'],
							'author' => $row['authorid']
						);
			}
		}
	
		return $ret;
	}
	
	function get_item_data($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gallery";
	
		$ret = "";
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$ret = $item_row->data;
		}

		return $ret;
	}
	
	function get_settings() {
		
		$userrole = get_option( 'wonderplugin_gallery_userrole' );
		if ( $userrole == false )
		{
			update_option( 'wonderplugin_gallery_userrole', 'manage_options' );
			$userrole = 'manage_options';
		}
		
		$keepdata = get_option( 'wonderplugin_gallery_keepdata', 1 );
		$disableupdate = get_option( 'wonderplugin_gallery_disableupdate', 0 );
		
		$supportwidget = get_option( 'wonderplugin_gallery_supportwidget', 1 );
		$addjstofooter = get_option( 'wonderplugin_gallery_addjstofooter', 0 );
		
		$jsonstripcslash = get_option( 'wonderplugin_gallery_jsonstripcslash', 1 );
		
		$settings = array(
			"userrole" => $userrole,
			"keepdata" => $keepdata,
			"disableupdate" => $disableupdate,
			"supportwidget" => $supportwidget,
			"addjstofooter" => $addjstofooter,
			"jsonstripcslash" => $jsonstripcslash
		);
		
		return $settings;
	}
	
	function save_settings($options) {
		
		if (!isset($options) || !isset($options['userrole']))
			$userrole = 'manage_options';
		else if ( $options['userrole'] == "Editor") 
			$userrole = 'moderate_comments';
		else if ( $options['userrole'] == "Author")
			$userrole = 'upload_files';
		else
			$userrole = 'manage_options';
		update_option( 'wonderplugin_gallery_userrole', $userrole );
		
		if (!isset($options) || !isset($options['keepdata']))
			$keepdata = 0;
		else
			$keepdata = 1;
		update_option( 'wonderplugin_gallery_keepdata', $keepdata );
		
		if (!isset($options) || !isset($options['disableupdate']))
			$disableupdate = 0;
		else
			$disableupdate = 1;
		update_option( 'wonderplugin_gallery_disableupdate', $disableupdate );
		
		if (!isset($options) || !isset($options['supportwidget']))
			$supportwidget = 0;
		else
			$supportwidget = 1;
		update_option( 'wonderplugin_gallery_supportwidget', $supportwidget );
		
		if (!isset($options) || !isset($options['addjstofooter']))
			$addjstofooter = 0;
		else
			$addjstofooter = 1;
		update_option( 'wonderplugin_gallery_addjstofooter', $addjstofooter );
		
		if (!isset($options) || !isset($options['jsonstripcslash']))
			$jsonstripcslash = 0;
		else
			$jsonstripcslash = 1;
		update_option( 'wonderplugin_gallery_jsonstripcslash', $jsonstripcslash );
	}
		
	function get_plugin_info() {
		
		$info = get_option('wonderplugin_gallery_information');
		if ($info === false)
			return false;
		
		return unserialize($info);
	}
	
	function save_plugin_info($info) {
		
		update_option( 'wonderplugin_gallery_information', serialize($info) );
	}
	
	function check_license($options) {
		
		$ret = array(
			"status" => "empty"
		);
				
		if ( !isset($options) || empty($options['wonderplugin-gallery-key']) )
		{
			return $ret;
		}
		
		$key = sanitize_text_field( $options['wonderplugin-gallery-key'] );
		if ( empty($key) )
			return $ret;
		
		$update_data = $this->controller->get_update_data('register', $key);
		if( $update_data === false )
		{
			$ret['status'] = 'timeout';
			return $ret;
		}
		
		if ( isset($update_data->key_status) )						
			$ret['status'] = $update_data->key_status;
		
		return $ret;
	}
	
	function deregister_license($options) {
		
		$ret = array(
			"status" => "empty"
		);
		
		if ( !isset($options) || empty($options['wonderplugin-gallery-key']) )
			return $ret;
		
		$key = sanitize_text_field( $options['wonderplugin-gallery-key'] );
		if ( empty($key) )
			return $ret;
		
		$info = $this->get_plugin_info();
		$info->key = '';
		$info->key_status = 'empty';
		$info->key_expire = 0;
		$this->save_plugin_info($info);
		
		$update_data = $this->controller->get_update_data('deregister', $key);
		if ($update_data === false)
		{
			$ret['status'] = 'timeout';
			return $ret;
		}
		
		$ret['status'] = 'success';	
		
		return $ret;
	}

}
