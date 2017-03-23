<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('plugins_loaded', 'wpsm_team_b_tr');
function wpsm_team_b_tr() {
	load_plugin_textdomain( wpshopmart_team_b_text_domain, FALSE, dirname( plugin_basename(__FILE__)).'/languages/' );
}
function wpsm_team_b_front_script() {
	wp_enqueue_style('wpsm_team_b-font-awesome-front', wpshopmart_team_b_directory_url.'assets/css/font-awesome/css/font-awesome.min.css');
	wp_enqueue_style('wpsm_team_b_bootstrap-front', wpshopmart_team_b_directory_url.'assets/css/bootstrap-front.css');
	wp_enqueue_style('wpsm_team_b_team1', wpshopmart_team_b_directory_url.'assets/css/team1.css');
	wp_enqueue_style('wpsm_team_b_team2', wpshopmart_team_b_directory_url.'assets/css/team2.css');
}

add_action( 'wp_enqueue_scripts', 'wpsm_team_b_front_script' );
add_filter( 'widget_text', 'do_shortcode');

add_action('media_buttons_context', 'wpsm_team_b_editor_popup_content_button');
add_action('admin_footer', 'wpsm_team_b_editor_popup_content');

function wpsm_team_b_editor_popup_content_button($context) {
 $img = wpshopmart_team_b_directory_url.'assets/images/icon.png';
  $container_id = 'TEAM_B';
  $title = 'Select Team Group to insert into post';
  $context .= '<style>.wp_team_b_shortcode_button {
				background: #11CAA5 !important;
				border-color: #11CAA5 #11CAA5 #11CAA5 !important;
				-webkit-box-shadow: 0 1px 0 #11CAA5 !important;
				box-shadow: 0 1px 0 #11CAA5 !important;
				color: #fff;
				text-decoration: none;
				text-shadow: 0 -1px 1px #11CAA5 ,1px 0 1px #11CAA5,0 1px 1px #11CAA5,-1px 0 1px #11CAA5 !important;
			    }</style>
			    <a class="button button-primary wp_team_b_shortcode_button thickbox" title="Select Teams to insert into post"    href="#TB_inline?width=400&inlineId='.$container_id.'">
					<span class="wp-media-buttons-icon" style="background: url('.$img.'); background-repeat: no-repeat; background-position: left bottom;"></span>
				Team Builder Shortcode
				</a>';
  return $context;
}

function wpsm_team_b_editor_popup_content() {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#wpsm_team_b_insert').on('click', function() {
			var id = jQuery('#wpsm_team_b_insertselect option:selected').val();
			window.send_to_editor('<p>[TEAM_B id=' + id + ']</p>');
			tb_remove();
		})
	});
	</script>
<style>
.wp_team_b_shortcode_button {
    background: #11CAA5; !important;
    border-color: #11CAA5; #11CAA5 #11CAA5 !important;
    -webkit-box-shadow: 0 1px 0 #11CAA5 !important;
    box-shadow: 0 1px 0 #11CAA5 !important;
    color: #fff !important;
    text-decoration: none;
    text-shadow: 0 -1px 1px #11CAA5 ,1px 0 1px #11CAA5,0 1px 1px #11CAA5,-1px 0 1px #11CAA5 !important;
}
</style>
	<div id="TEAM_B" style="display:none;">
	  <h3>Select Team To Insert Into Post</h3>
	  <?php 
		
		$all_posts = wp_count_posts( 'team_builder')->publish;
		$args = array('post_type' => 'team_builder', 'posts_per_page' =>$all_posts);
		global $All_rac;
		$All_rac = new WP_Query( $args );			
		if( $All_rac->have_posts() ) { ?>	
			<select id="wpsm_team_b_insertselect" style="width: 100%;margin-bottom: 20px;">
				<?php
				while ( $All_rac->have_posts() ) : $All_rac->the_post(); ?>
				<?php $title = get_the_title(); ?>
				<option value="<?php echo get_the_ID(); ?>"><?php if (strlen($title) == 0) echo 'No Title Found'; else echo $title;   ?></option>
				<?php
				endwhile; 
				?>
			</select>
			<button class='button primary wp_team_b_shortcode_button' id='wpsm_team_b_insert'><?php _e('Insert Teams Shortcode', wpshopmart_team_b_text_domain); ?></button>
			<?php
		} else {
			_e('No Teams Found', wpshopmart_team_b_text_domain);
		}
		?>
	</div>
	<?php
}

add_action( 'admin_notices', 'wpsm_team_b_review' );
function wpsm_team_b_review() {

	// Verify that we can do a check for reviews.
	$review = get_option( 'wpsm_team_b_review' );
	$time	= time();
	$load	= false;
	if ( ! $review ) {
		$review = array(
			'time' 		=> $time,
			'dismissed' => false
		);
		add_option('wpsm_team_b_review', $review);
		//$load = true;
	} else {
		// Check if it has been dismissed or not.
		if ( (isset( $review['dismissed'] ) && ! $review['dismissed']) && (isset( $review['time'] ) && (($review['time'] + (DAY_IN_SECONDS * 3)) <= $time)) ) {
			$load = true;
		}
	}
	// If we cannot load, return early.
	if ( ! $load ) {
		return;
	}

	// We have a candidate! Output a review message.
	?>
	<div class="notice notice-info is-dismissible wpsm-team-b-review-notice">
		<div style="float:left;margin-right:10px;margin-bottom:5px;">
			<img style="width:100%;width: 150px;height: auto;" src="<?php echo wpshopmart_team_b_directory_url.'assets/images/icon-show.png'; ?>" />
		</div>
		<p style="font-size:18px;">'Hi! We saw you have been using <strong>Team Bilder plugin</strong> for a few days and wanted to ask for your help to <strong>make the plugin better</strong>.We just need a minute of your time to rate the plugin. Thank you!</p>
		<p style="font-size:18px;"><strong><?php _e( '~ wpshopmart', '' ); ?></strong></p>
		<p style="font-size:19px;"> 
			<a style="color: #fff;background: #ef4238;padding: 5px 7px 4px 6px;border-radius: 4px;" href="https://wordpress.org/support/plugin/team-builder/reviews/?filter=5#new-post" class="wpsm-team-b-dismiss-review-notice wpsm-team-b-review-out" target="_blank" rel="noopener">Rate the plugin</a>&nbsp; &nbsp;
			<a style="color: #fff;background: #27d63c;padding: 5px 7px 4px 6px;border-radius: 4px;" href="#"  class="wpsm-team-b-dismiss-review-notice wpsm-rate-later" target="_self" rel="noopener"><?php _e( 'Nope, maybe later', '' ); ?></a>&nbsp; &nbsp;
			<a style="color: #fff;background: #31a3dd;padding: 5px 7px 4px 6px;border-radius: 4px;" href="#" class="wpsm-team-b-dismiss-review-notice wpsm-rated" target="_self" rel="noopener"><?php _e( 'I already did', '' ); ?></a>
		</p>
	</div>
	<script type="text/javascript">
		jQuery(document).ready( function($) {
			$(document).on('click', '.wpsm-team-b-dismiss-review-notice, .wpsm-team-b-dismiss-notice .notice-dismiss', function( event ) {
				if ( $(this).hasClass('wpsm-team-b-review-out') ) {
					var wpsm_rate_data_val = "1";
				}
				if ( $(this).hasClass('wpsm-rate-later') ) {
					var wpsm_rate_data_val =  "2";
					event.preventDefault();
				}
				if ( $(this).hasClass('wpsm-rated') ) {
					var wpsm_rate_data_val =  "3";
					event.preventDefault();
				}

				$.post( ajaxurl, {
					action: 'wpsm_team_b_dismiss_review',
					wpsm_rate_data_team_b : wpsm_rate_data_val
				});
				
				$('.wpsm-team-b-review-notice').hide();
				//location.reload();
			});
		});
	</script>
	<?php
}

add_action( 'wp_ajax_wpsm_team_b_dismiss_review', 'wpsm_team_b_dismiss_review' );
function wpsm_team_b_dismiss_review() {
	if ( ! $review ) {
		$review = array();
	}
	
	if($_POST['wpsm_rate_data_team_b']=="1"){
		$review['time'] 	 = time();
		$review['dismissed'] = false;
		
	}
	if($_POST['wpsm_rate_data_team_b']=="2"){
		$review['time'] 	 = time();
		$review['dismissed'] = false;
		
	}
	if($_POST['wpsm_rate_data_team_b']=="3"){
		$review['time'] 	 = time();
		$review['dismissed'] = true;
		
	}
	update_option( 'wpsm_team_b_review', $review );
	die;
}
?>