<?php
/*
Plugin Name: Amazon S3 Video And Audio Streaming
Plugin URI: https://s3bubble.com
Description: Offers secure, Media Streaming from Amazon S3 to WordPress. 
Version: 5.1
Author: S3Bubble
Author URI: https://s3bubble.com
License: GPL2
*/ 
 
/*  Copyright YEAR  Samuel East  (email : mail@samueleast.co.uk)

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


if (!class_exists("s3bubble_audio")) {
	class s3bubble_audio {

		/*
		 * Class properties
		 * @author sameast
		 * @params noen
		 */ 
        public  $s3audible_username = '';
		public  $s3audible_email = '';
		public  $s3bubble_uploader_access_key = '';
		public  $s3bubble_uploader_secret_key = '';
		public  $s3bubble_uploader_email = '';
		public  $bucket          = '';
		public  $folder          = '';
		public  $colour          = '#1abc98';
		public  $width           = '100%';
		public  $autoplay        = 'yes';
		public  $jtoggle		 = 'true';
		public  $loggedin        = 'false';
		public  $s3bubble_force_download = 'false';
		public  $search          = 'false';
		public  $thumbs          = 'false';
		public  $resume          = 'false';
		public  $s3bubble_capability = 'manage_options';
		public  $responsive      = 'responsive';
		public  $theme           = 's3bubble_clean';
		public  $stream          = 'm4v';
		public  $version         =  60;
		public  $s3bubble_video_all_bar_colours = '#adadad';
		public  $s3bubble_video_all_bar_seeks   = '#53bbb4';
		public  $s3bubble_video_all_controls_bg = '#384049';
		public  $s3bubble_video_all_icons       = '#FFFFFF';
		private $endpoint       = 'https://s3api.com/v3/';
		
		/*
		 * Constructor method to intiat the class
		 * @author sameast
		 * @params none
		 */
		public function  __construct(){ 
			
			/*
			 * Add default option to database
			 * @author sameast
			 * @params none
			 */ 
			add_option("s3-s3audible_username", $this->s3audible_username);
			add_option("s3-s3audible_email", $this->s3audible_email);
			add_option("s3-bucket", $this->bucket);
			add_option("s3-folder", $this->folder); 
			add_option("s3-colour", $this->colour);
			add_option("s3-width", $this->width);
			add_option("s3-autoplay", $this->autoplay);
			add_option("s3-jtoggle", $this->jtoggle);
			add_option("s3-loggedin", $this->loggedin);
			add_option("s3bubble_force_download", $this->s3bubble_force_download);
			add_option("s3-search", $this->search);
			add_option("s3-thumbs", $this->thumbs);
			add_option("s3-resume", $this->resume);
			add_option("s3-s3bubble_capability", $this->s3bubble_capability);
			add_option("s3-responsive", $this->responsive);
			add_option("s3-theme", $this->theme);
			add_option("s3-stream", $this->stream);
			add_option("s3-endpoint", $this->endpoint);
			add_option("s3bubble_video_all_bar_colours", $this->s3bubble_video_all_bar_colours);
			add_option("s3bubble_video_all_bar_seeks", $this->s3bubble_video_all_bar_seeks);
			add_option("s3bubble_video_all_controls_bg", $this->s3bubble_video_all_controls_bg);
			add_option("s3bubble_video_all_icons", $this->s3bubble_video_all_icons);


			/*
			 * Run the add admin menu class
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'admin_menu', array( $this, 's3bubble_audio_admin_menu' ));

			/*
			 * Add some extras to run after theme support add image sizes etc...
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'after_setup_theme', array( $this, 's3bubble_wordpress_theme_setup' ) );
			
			/*
			 * Add css to the header of the document
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'wp_enqueue_scripts', array( $this, 's3bubble_audio_css' ), 12 );
			add_action( 'wp_enqueue_scripts', array( $this, 's3bubble_audio_javascript' ), 12 );
			
			/*
			 * Add javascript to the frontend footer connects to wp_footer
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'admin_enqueue_scripts', array( $this, 's3bubble_audio_admin_scripts' ) );
			
			/*
			 * Setup shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleVideo', array( $this, 's3bubble_jplayer_video_playlist_progressive' ) );
			add_shortcode( 's3bubbleVideoSingle', array( $this, 's3bubble_jplayer_video_progressive' ) );
			add_shortcode( 's3bubbleLightboxVideoSingle', array( $this, 's3bubble_jplayer_video_lightbox_progressive' ) );
			add_shortcode( 's3bubbleRtmpVideoDefault', array( $this, 's3bubble_videojs_video_rtmp' ) );
			add_shortcode( 's3bubbleAudioSingle', array( $this, 's3bubble_jplayer_audio_progressive' ) );
			add_shortcode( 's3bubbleRtmpAudioDefault', array( $this, 's3bubble_jplayer_audio_rtmp' ) );
			add_shortcode( 's3bubbleAudio', array( $this, 's3bubble_jplayer_audio_playlist_progressive' ) );

			/*
			 * Video JS for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleVideoSingleJs', array( $this, 's3bubble_videojs_video_progressive' ) );
			add_shortcode( 's3bubbleHlsVideoJs', array( $this, 's3bubble_videojs_video_hls' ) );
			add_shortcode( 's3bubbleRtmpVideoJs', array( $this, 's3bubble_videojs_video_rtmp' ) );
			add_shortcode( 's3bubbleVideoJsPlaylist', array( $this, 's3bubble_videojs_video_playlist' ) );
			add_shortcode( 's3bubbleLiveStream', array( $this, 's3bubble_videojs_video_broadcasting' ) );
			add_shortcode( 's3bubbleHlsAudioDefault', array( $this, 's3bubble_videojs_audio_hls' ) );
			add_shortcode( 's3bubbleAudioSingleVideoJs', array( $this, 's3bubble_videojs_audio_progressive' ) );

			/*
			 * Media Element shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleMediaElementVideo', array( $this, 's3bubble_mediajs_video_progressive' ) );
			add_shortcode( 's3bubbleHlsVideo', array( $this, 's3bubble_mediajs_video_hls' ) );
			add_shortcode( 's3bubbleRtmpVideo', array( $this, 's3bubble_mediajs_video_rtmp' ) );
			add_shortcode( 's3bubbleLiveStreamMedia', array( $this, 's3bubble_mediajs_video_broadcaster' ) );
			add_shortcode( 's3bubbleMediaElementAudio', array( $this, 's3bubble_mediajs_audio_progressive' ) );
			add_shortcode( 's3bubbleMobileAppBroadcast', array( $this, 's3bubble_mediajs_video_broadcaster_mobile_app' ) );
			
			/*
			 * Legacy shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3audible', array( $this, 's3bubble_jplayer_audio_playlist_progressive' ) );
			add_shortcode( 's3audibleSingle', array( $this, 's3bubble_jplayer_audio_progressive' ) );	
			add_shortcode( 's3video', array( $this, 's3bubble_jplayer_video_playlist_progressive' ) );	
			add_shortcode( 's3videoSingle', array( $this, 's3bubble_jplayer_video_progressive' ) );
            
            /*
			 * Setup shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */
            add_shortcode( 's3bubbleLightboxVideoOld', array( $this, 's3bubble_lightbox_video_old' ) );
			
			/*
			 * Setup shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleWaveform', array( $this, 's3bubble_waveform_playlist_player' ) );	
			add_shortcode( 's3bubbleWaveformSingle', array( $this, 's3bubble_waveform_single_player' ) );

			/*
			 * Iframe codes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleVideoSingleIframe', array( $this, 's3bubble_jplayer_video_progressive_iframe' ) );
			add_shortcode( 's3bubbleAudioSingleIframe', array( $this, 's3bubble_jplayer_audio_progressive_iframe' ) );

			/*
			 * Outputs the s3bubble advertiser
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleAdvertiser', array( $this, 's3bubble_advertiser' ) );
			
			/*
			 * Tiny mce button for the plugin
			 * @author sameast
			 * @params none
			 */
			add_action( 'init', array( $this, 's3bubble_buttons' ) );
			add_action( 'wp_ajax_s3bubble_audio_playlist_ajax', array( $this, 's3bubble_audio_playlist_ajax' ) );
			add_action( 'wp_ajax_s3bubble_video_playlist_ajax', array( $this, 's3bubble_video_playlist_ajax' ) );
			add_action( 'wp_ajax_s3bubble_audio_single_ajax', array( $this, 's3bubble_audio_single_ajax' ) );
			add_action( 'wp_ajax_s3bubble_video_single_ajax', array( $this, 's3bubble_video_single_ajax' ) ); 
			add_action( 'wp_ajax_s3bubble_live_stream_ajax', array( $this, 's3bubble_live_stream_ajax' ) );
			add_action( 'wp_ajax_s3bubble_live_stream_mobile_ajax', array( $this, 's3bubble_live_stream_mobile_ajax' ) );

			/*
			 * Admin dismiss message
			 */
			add_action('admin_notices', array( $this, 's3bubble_admin_notice' ) );
			add_action('admin_init', array( $this, 's3bubble_nag_ignore' ) );
			add_action('admin_notices', array( $this, 's3bubble_admin_notice_please_upgrade' ) );
			add_action('admin_init', array( $this, 's3bubble_nag_ignore_please_upgrade' ) );

			/*
			 * Heartbeat fix
			 */
			add_action( 'init', array( $this, 's3bubble_stop_heartbeat' ), 1 );


		}

		/*
		* Fix for poor hosts
		* @author sameast
		* @none
		*/ 
		function s3bubble_stop_heartbeat() {
		  	global $pagenow;
		  	if ( $pagenow != 'edit.php' )
		  	wp_deregister_script('heartbeat');
		}

		/*
		* Run after theme support image sizes etc...
		* @author sameast
		* @none
		*/ 
		function s3bubble_wordpress_theme_setup() {
		  	/* Configure WP 2.9+ Thumbnails ---------------------------------------------*/
    		add_theme_support('post-thumbnails');
        	add_image_size( 's3bubble-single-video-poster', 960, 540, true ); // (cropped)

		}

		/*
		* Sets up a admin alert notice
		* @author sameast
		* @none
		*/ 
		function s3bubble_admin_notice() {
			global $current_user;
		    $user_id = $current_user->ID;
		    $params = array_merge($_GET, array("s3bubble_nag_ignore" => 0));
			$new_query_string = http_build_query($params); 
		    /* Check that the user hasn't already clicked to ignore the message */
			if ( ! get_user_meta($user_id, 's3bubble_nag_ignore') ) {
		        echo '<div class="updated"><p>'; 
		        echo 'Thankyou for upgrading your S3Bubble media streaming plugin. Any issues please contact us at <a href="mailto:support@s3bubble.com">support@s3bubble.com</a> if you are stuck you can always roll back within the S3Bubble WP admin download and re-install the old plugin. | <a href="' . $_SERVER['PHP_SELF'] . "?" . $new_query_string . '" class="pull-right">Hide Notice</a>';
		        echo "</p></div>";
			}
		}

		/*
		* Allows users to ignore the message
		* @author sameast
		* @none
		*/ 
		function s3bubble_nag_ignore() {
			global $current_user;
		        $user_id = $current_user->ID;
		        /* If user clicks to ignore the notice, add that to their user meta */
		        if ( isset($_GET['s3bubble_nag_ignore']) && '0' == $_GET['s3bubble_nag_ignore'] ) {
		             add_user_meta($user_id, 's3bubble_nag_ignore', 'true', true);
			}
		}

		/*
		* Sets up a admin alert notice
		* @author sameast
		* @none
		*/ 
		function s3bubble_admin_notice_please_upgrade() {
			global $current_user;
		    $user_id = $current_user->ID;
		    $params = array_merge($_GET, array("s3bubble_nag_ignore_please_upgrade" => 0));
			$new_query_string = http_build_query($params); 
		    /* Check that the user hasn't already clicked to ignore the message */
			if ( ! get_user_meta($user_id, 's3bubble_nag_ignore_please_upgrade') ) {
		        echo '<div class="updated"><p>'; 
		        echo '!Important, we highly suggest you start upgrading RTMP videos to our new HLS Adaptive Bitrate streaming setup using iframes for security. Google has started blocking all flash content please find out more information <a href="https://www.engadget.com/2016/08/09/google-chrome-blocking-flash/">here</a>. Please start using our Oembed WordPress Plugin you can download <a href="https://en-gb.wordpress.org/plugins/s3bubble-amazon-web-services-oembed-media-streaming-support/">here</a> | <a href="' . $_SERVER['PHP_SELF'] . "?" . $new_query_string . '" class="pull-right">Hide Notice</a>';
		        echo "</p></div>";
			}
		}

		/*
		* Allows users to ignore the message
		* @author sameast
		* @none
		*/ 
		function s3bubble_nag_ignore_please_upgrade() {
			global $current_user;
		        $user_id = $current_user->ID;
		        /* If user clicks to ignore the notice, add that to their user meta */
		        if ( isset($_GET['s3bubble_nag_ignore_please_upgrade']) && '0' == $_GET['s3bubble_nag_ignore_please_upgrade'] ) {
		             add_user_meta($user_id, 's3bubble_nag_ignore_please_upgrade', 'true', true);
			}
		}

		/*
		* Adds the menu item to the wordpress admin
		* @author sameast
		* @none
		*/ 
        function s3bubble_audio_admin_menu(){

            $s3bubble_capability = get_option("s3-s3bubble_capability");
            $capability = 'manage_options';
            if(isset($s3bubble_capability) && !empty($s3bubble_capability)){
                $capability = $s3bubble_capability;
            }
			add_menu_page( 's3bubble_audio', 'S3Bubble Media', $capability, 's3bubble_audio', array($this, 's3bubble_audio_admin'), plugins_url('admin/images/s3bubblelogo.png',__FILE__ ) );

    	}

    	/*
		* Add css to wordpress admin to run colourpicker
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_admin_scripts(){
			
			$endpoint = get_option("s3-endpoint");
			//wp_enqueue_script( 's3bubble.video.all.tinymce', plugins_url( 'admin/js/s3bubble.video.all.tinymce.js', __FILE__ ), array( ), false, true ); 
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 's3bubble.video.all.colour', plugins_url( 'admin/js/s3bubble.video.all.colour.min.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
			wp_enqueue_style( 's3bubble-backup-stupidtable-js', plugins_url('admin/js/stupidtable.min.js', __FILE__) );
			wp_enqueue_script( 's3bubble-admin', plugins_url( 'admin/js/admin.js', __FILE__ ), array( ), false, true ); 
			wp_localize_script('s3bubble-admin', 's3bubble_all_object', array(
				's3appid' => get_option("s3-s3audible_username"),
				's3api' => $endpoint,
				'serveraddress' => $_SERVER['REMOTE_ADDR'],
				'ajax_url' => admin_url( 'admin-ajax.php' )
			));

			// Admin css
			wp_enqueue_style( 's3bubble-video.all.admin', plugins_url('admin/css/s3bubble.video.all.admin.css', __FILE__), array(), $this->version );
			wp_enqueue_style( 's3bubble-video.all.plugin', plugins_url('admin/css/s3bubble.video.all.plugin.css', __FILE__), array(), $this->version );

		}
		
		/*
		* Add css ties into wp_head() function
		* @author sameast
		* @params none
        */ 
		function s3bubble_audio_css(){
			
			$progress	= stripcslashes(get_option("s3bubble_video_all_bar_colours"));
			$background	= stripcslashes(get_option("s3bubble_video_all_controls_bg"));
			$seek	    = stripcslashes(get_option("s3bubble_video_all_bar_seeks"));
			$icons	    = stripcslashes(get_option("s3bubble_video_all_icons"));

			wp_enqueue_style('wp-mediaelement');

			wp_enqueue_style( 's3bubble-amazon-s3-audio-streaming-css', plugins_url('dist/css/s3bubble-amazon-s3-audio-streaming.min.css', __FILE__), array(), $this->version );
			$custom_css = " 
	                .s3bubble-media-main-progress, .s3bubble-media-main-volume-bar, .vjs-play-progress, .vjs-volume-level {background-color: {$progress} !important;}
					.s3bubble-media-main-play-bar, .s3bubble-media-main-volume-bar-value {background-color: {$seek} !important; float:left;}
					.s3bubble-media-main-interface, .s3bubble-media-main-video-play, .s3bubble-media-main-video-skip, .s3bubble-media-main-preview-over {background-color: {$background} !important;color: {$icons} !important;}
					.s3bubble-media-main-video-loading {color: {$icons} !important;}
					.s3bubble-media-main-interface  > * a, .s3bubble-media-main-interface  > * a:hover, .s3bubble-media-main-interface  > * i, .s3bubble-media-main-interface  > * i:hover, .s3bubble-media-main-current-time, .s3bubble-media-main-duration, .time-sep, .s3icon-cloud-download {color: {$icons} !important;text-decoration: none !important;font-style: normal !important;}
					.s3bubble-media-main-video-skip h2, .s3bubble-media-main-preview-over-container h2 {color: {$icons} !important;}
					.s3bubble-media-main-playlist-current {color: {$seek} !important;}
					.mejs-controls {background-color: {$background} !important;}
					.mejs-time-current, .mejs-horizontal-volume-current {background-color: {$seek} !important;}
					.vjs-control-bar {background-color: {$background} !important;}
					.s3bubble-audiojs-hls-video .vjs-control-bar {background-color: transparent !important;}
					.s3bubble-audiojs-hls-video .vjs-progress-control .vjs-slider { display: none; }
					.s3bubble-audiojs-hls-video  .vjs-volume-level {background-color: {$seek} !important;}
					.s3bubble-audiojs-hls-video .vjs-fullscreen-control, .s3bubble-audiojs-hls-video .vjs-mute-control, .s3bubble-audiojs-hls-video .vjs-play-control, .s3bubble-audiojs-hls-video .vjs-volume-menu-button {color: {$background}}
					.s3bubble-audiojs-hls-video .me-plugin, .s3bubble-audiojs-hls-video .mejs-poster, .s3bubble-audiojs-hls-video .video-js, .s3bubble-audiojs-hls-video .vjs-no-js {background-color: transparent;}
					.s3bubble-audiojs-hls-video .vjs-live-control, .s3bubble-audiojs-hls-video .vjs-time-control {color: {$background};}
					.audioplayer.skin-wave .ap-controls .con-playpause .pausebtn, .audioplayer.skin-wave .ap-controls .con-playpause .playbtn {background-color: {$background} !important;}
					.audioplayer.skin-wave .next-btn, .audioplayer.skin-wave .prev-btn {background-color: {$background} !important;}
					.audioplayer.skin-wave .ap-controls .scrubbar .scrubBox-hover {background-color: {$background} !important;}
					.audioplayer.skin-wave .btn-menu-state {background-color: {$background} !important;}";
	        wp_add_inline_style( 's3bubble-amazon-s3-audio-streaming-css', $custom_css );
            
	        // Important CDN fixes
			wp_enqueue_style('s3bubble-s3bubble.helpers', ("//s3.amazonaws.com/s3bubble.assets/plugin.css/style.css"), array(),  $this->version );

		}
		 
		/*
		* Add javascript to the footer connect to wp_footer()
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_javascript(){
			
			if (!is_admin()) {

				wp_enqueue_script( 'jquery-migrate' );
				wp_enqueue_script( 'wp-mediaelement' );
				wp_deregister_script( 'jplayer' );
				wp_enqueue_script( 'plupload' );

				wp_enqueue_script( 's3bubble-amazon-s3-audio-streaming-js', plugins_url('dist/js/s3bubble-amazon-s3-audio-streaming.min.js',__FILE__ ), array( 'jquery'),  $this->version, true );
                wp_localize_script('s3bubble-amazon-s3-audio-streaming-js', 's3bubble_all_object', array(
					's3appid' => get_option("s3-s3audible_username"),
					's3api' => get_option("s3-endpoint"),
					'serveraddress' => $_SERVER['REMOTE_ADDR'],
					'ajax_url' => admin_url( 'admin-ajax.php' )
				));
				
            }
		}

    	/*
		* Add javascript to the footer connect to wp_footer()
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_admin(){	

			// defaults
			$alert = '';

			if ( isset($_POST['submit']) ) {
				$nonce = $_REQUEST['_wpnonce'];
				if (! wp_verify_nonce($nonce, 's3bubble-media') ) die('Security check failed'); 
				if (!current_user_can('manage_options')) die(__('You cannot edit the s3bubble media options.'));
				check_admin_referer('s3bubble-media');	
				// Get our new option values
				$s3audible_username	     = $this->s3bubble_clean_options($_POST['s3audible_username']);
				$s3audible_email	     = $this->s3bubble_clean_options($_POST['s3audible_email']);
				$loggedin			     = $this->s3bubble_clean_options($_POST['loggedin']);
				$thumbs			         = $this->s3bubble_clean_options($_POST['thumbs']);

				$s3bubble_capability     = sanitize_key($_POST['s3bubble_capability']);
				$s3bubble_force_download = $this->s3bubble_clean_options($_POST['s3bubble_force_download']);
				$endpoint			     = $this->s3bubble_clean_options($_POST['endpoint']);

				// new
				$s3bubble_video_all_bar_colours	= $this->s3bubble_clean_options($_POST['s3bubble_video_all_bar_colours']);
				$s3bubble_video_all_bar_seeks	= $this->s3bubble_clean_options($_POST['s3bubble_video_all_bar_seeks']);
				$s3bubble_video_all_controls_bg	= $this->s3bubble_clean_options($_POST['s3bubble_video_all_controls_bg']);
				$s3bubble_video_all_icons	    = $this->s3bubble_clean_options($_POST['s3bubble_video_all_icons']);

			    // Update the DB with the new option values
				update_option("s3-s3audible_username", $s3audible_username);
				update_option("s3-s3audible_email", $s3audible_email);
				update_option("s3-loggedin", $loggedin);
				update_option("s3-thumbs", $thumbs);
				update_option("s3-s3bubble_capability", $s3bubble_capability);

				update_option("s3bubble_force_download", $s3bubble_force_download);
				update_option("s3-endpoint", $endpoint);
				
				// new
				update_option("s3bubble_video_all_bar_colours", $s3bubble_video_all_bar_colours);
				update_option("s3bubble_video_all_bar_seeks", $s3bubble_video_all_bar_seeks);
				update_option("s3bubble_video_all_controls_bg", $s3bubble_video_all_controls_bg);
				update_option("s3bubble_video_all_icons", $s3bubble_video_all_icons);

				//set POST variables
				$url = $this->endpoint . 'main_plugin/auth';
				$response = wp_remote_post( $url, array(
					'method' => 'POST',
					'sslverify' => false,
					'timeout' => 10,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(),
					'body' => array(
						'AccessKey' => $s3audible_username
					),
					'cookies' => array()
				    )
				);

				if ( is_wp_error( $response ) ) {

				   $error_message = $response->get_error_message();
				   $alert = '<div class="error"><p>' . $error_message . '</p></div>';

				} else {

					$data = json_decode($response['body']);
					if($data->error){
						$alert = '<div class="error"><p>' . $data->message . '</p></div>';
					}else{
						$alert = '<div class="updated"><p>' . $data->message . '</p></div>';
					}
				}

			}
			
			$s3audible_username	     = get_option("s3-s3audible_username");
			$s3audible_email	     = get_option("s3-s3audible_email");
			$loggedin			     = get_option("s3-loggedin");
			$thumbs			         = get_option("s3-thumbs");
			$s3bubble_capability     = get_option("s3-s3bubble_capability");

			$s3bubble_force_download = get_option("s3bubble_force_download");
			$endpoint			     = get_option("s3-endpoint");			

			// new
			$s3bubble_video_all_bar_colours	= get_option("s3bubble_video_all_bar_colours");
			$s3bubble_video_all_bar_seeks	= get_option("s3bubble_video_all_bar_seeks");
			$s3bubble_video_all_controls_bg	= get_option("s3bubble_video_all_controls_bg");
			$s3bubble_video_all_icons	    = get_option("s3bubble_video_all_icons");

		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2>S3Bubble Amazon S3 Media Cloud Media Streaming</h2>
			<div id="message" class="updated fade"><p>Please sign up for a S3Bubble account at <a href="https://s3bubble.com" target="_blank">https://s3bubble.com</a></p></div>
			<?php echo $alert; ?>
			<div class="metabox-holder has-right-sidebar">
				<div class="inner-sidebar" style="width:40%">
					<div class="postbox">
						<h3 class="hndle">PLEASE USE WYSIWYG EDITOR BUTTONS</h3>
						<div class="inside">
							<img style="width: 100%;" src="<?php echo plugins_url('/admin/images/wp_editor.png',__FILE__); ?>" />
						</div> 
					</div>
				</div>
				<div id="post-body">
					<div id="post-body-content" style="margin-right: 41%;">
						<div class="postbox">
							<h3 class="hndle">Fill in details below.</h3>
							<div class="inside">
								<form action="" method="post" class="s3bubble-video-popup-form" autocomplete="off">
								    <table class="form-table">
								      <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('s3bubble-media'); } ?>
								       <tr style="position: relative;">
								        <th scope="row" valign="top"><label for="S3Bubble_username">App Access Key:</label></th>
								        <td><input type="text" name="s3audible_username" id="s3audible_username" class="regular-text" value="<?php echo empty($s3audible_username) ? 'Enter App Key' : $s3audible_username; ?>"/>
								        	<br />
								       <span class="description">App Access Key can be found at S3Bubble.com <a href="https://s3bubble.com/video_tutorials/s3bubble-lets-get-you-up-and-running-tutorial/" target="_blank">Watch Video</a></span>	
								        </td>
								      </tr> 
								       <tr>
								        <th scope="row" valign="top"><label for="s3audible_email">App Secret Key:</label></th>
								        <td><input type="password" name="s3audible_email" id="s3audible_email" class="regular-text" value="<?php echo empty($s3audible_email) ? 'Enter App Secret Key' : $s3audible_email; ?>"/>
								        	<br />
								        	<span class="description">App Secret Key can be found at S3Bubble.com <a href="https://s3bubble.com/video_tutorials/s3bubble-lets-get-you-up-and-running-tutorial/" target="_blank">Watch Video</a></span>
								        </td>
								      </tr>
								      <tr>
								        <th scope="row" valign="top"><label for="endpoint">Api endpoint:</label></th>
								        <td><select name="endpoint" id="endpoint">
								            <option value="<?php echo $endpoint; ?>"><?php echo $endpoint; ?></option>
								            <option value="https://s3api.com/v3/">https://s3api.com/v3/</option>
								            <option value="https://api.s3bubble.com/v3/">https://api.s3bubble.com/v3/</option>
								          </select>
								      </tr>
								      <tr valign="top">
								      	<th scope="row" valign="top"><label for="endpoint">Minimum Level to use this plugin:</label></th>
                                        <td>
                                            <select id="role" name="s3bubble_capability">
                                                <option value="read" <?php if (isset($s3bubble_capability) && $s3bubble_capability == "read") echo 'selected="selected"'?>>Subscriber</option>
                                                <option value="edit_posts" <?php if (isset($s3bubble_capability) && $s3bubble_capability == "edit_posts") echo 'selected="selected"'?>>Contributor</option>
                                                <option value="publish_posts" <?php if (isset($s3bubble_capability) && $s3bubble_capability == "publish_posts") echo 'selected="selected"'?>>Author</option>
                                                <option value="publish_pages" <?php if (isset($s3bubble_capability) && $s3bubble_capability == "publish_pages") echo 'selected="selected"'?>>Editor</option>
                                                <option value="manage_options" <?php if (!isset($s3bubble_capability) || empty($s3bubble_capability) || (isset($s3bubble_capability) && $s3bubble_capability == "manage_options")) echo 'selected="selected"'?>>Administrator</option>
                                            </select>
                                        </td>
                                    </tr> 
								       <tr>
								        <th scope="row" valign="top"><label for="thumbs">Use post thumbnails for video poster:</label></th>
								        <td><select name="thumbs" id="thumbs">
								            <option value="<?php echo $thumbs; ?>"><?php echo $thumbs; ?></option>
								            <option value="true">true</option>
								            <option value="false">false</option>
								          </select>
								      </tr> 
								       <tr>
								        <th scope="row" valign="top"><label for="loggedin">Download option logged in:</label></th>
								        <td><select name="loggedin" id="loggedin">
								            <option value="<?php echo $loggedin; ?>"><?php echo $loggedin; ?></option>
								            <option value="true">true</option>
								            <option value="false">false</option>
								          </select>
								          <br />
								          <span class="description">Only allow download link for logged in users.</p></td>
								      </tr>
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_force_download">Force download option for all players:</label></th>
								        <td><select name="s3bubble_force_download" id="s3bubble_force_download">
								            <option value="<?php echo $s3bubble_force_download; ?>"><?php echo $s3bubble_force_download; ?></option>
								            <option value="true">true</option>
								            <option value="false">false</option>
								          </select>
								          <br />
								          <span class="description">!important this will force the download to show on (All) players.</p></td>
								      </tr>
								      <!-- new -->
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_all_bar_colours">Player Bar Colours:</label></th>
								        <td> <input type="text" name="s3bubble_video_all_bar_colours" id="s3bubble_video_all_bar_colours" value="<?php echo $s3bubble_video_all_bar_colours; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the progress bar and volume bar colour</span>
								        </td>
								      </tr>
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_all_bar_seeks">Seek Bar Colours:</label></th>
								        <td> <input type="text" name="s3bubble_video_all_bar_seeks" id="s3bubble_video_all_bar_seeks" value="<?php echo $s3bubble_video_all_bar_seeks; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the progress bar and volume bar seek bar colours</span>
								        </td>
								      </tr>
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_all_controls_bg">Player Controls Colour:</label></th>
								        <td> <input type="text" name="s3bubble_video_all_controls_bg" id="s3bubble_video_all_controls_bg" value="<?php echo $s3bubble_video_all_controls_bg; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the controls background colour</span>
								        </td>
								      </tr> 
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_all_icons">Player Icon Colours:</label></th>
								        <td> <input type="text" name="s3bubble_video_all_icons" id="s3bubble_video_all_icons" value="<?php echo $s3bubble_video_all_icons; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the player icons colours</span>
								        </td>
								      </tr>  
								      <!-- end new -->
								    </table>
								    <br/>
								    <span class="submit" style="border: 0;">
								    	<input type="submit" name="submit" class="button button-s3bubble button-hero" value="Save Settings" />
								    </span>
								  </form>
							</div><!-- .inside -->
						</div>
					</div> <!-- #post-body-content -->
				</div> <!-- #post-body -->
			</div> <!-- .metabox-holder -->
		</div> <!-- .wrap -->
		<?php	
        }

		/*
		* S3Bubble generates a randow string for stream
		* @author sameast
		* @none
		*/ 
		function s3BubbleGenerateRandomString($length = 3) {
		    $characters = 'abcdefghijklmnopqrstuvwxyz';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
		    return $randomString;
		}

		/*
		* Outputs some memory information
		* @author sameast
		* @none
		*/ 
		function s3bubble_convert_memory(){

		    $mem_usage = memory_get_usage(true); 
	        $unit=array('b','kb','mb','gb','tb','pb');
	        $mem_out = @round($mem_usage/pow(1024,($i=floor(log($mem_usage,1024)))),2).' '.$unit[$i];
            return "Memory usage: " . $mem_out . ". Max execution time: " . ini_get('max_execution_time');

		}

        /*
		* Audio playlist button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_playlist_ajax(){
		    // echo the form
		    $s3bubble_access_key = get_option("s3-s3audible_username");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	$('#TB_ajaxContent').css({
                    	'width' : 'auto',
                    	'height' : 'auto',
                    	'padding' : '0'
                    });
			        var sendData = {
						App: s3bubble_all_object.s3appid
					};
					$.post(s3bubble_all_object.s3api + "main_plugin/live_buckets/", sendData, function(response) {
						if(response.error){
							$(".s3bubble-video-main-form-alerts").html("<p>Oh Snap! " + response.message + ". If you do not understand this error please contact support@s3bubble.com</p>");
						}else{
							$(".s3bubble-video-main-form-alerts").html("<p>Awesome! " + response.message + ".</p>");
							var isSingle = response.data.Single;
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
						    $.each(response.data.Buckets, function (i, item) {
						    	var bucket = item.Name;
						    	if(isSingle === true){
						    		html += '<option value="s3bubble.users">' + bucket + '</option>';
						    	}else{
						    		html += '<option value="' + bucket + '">' + bucket + '</option>';	
						    	}
							});
							html += '</select>';
							$('#s3bubble-buckets-shortcode').html(html);
						}
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/admin/images/ajax-loader.gif',__FILE__); ?>"/> loading folders');
						   var bucket = $(this).val();
						   if(isSingle === true){
						   		bucket = $("#s3bucket option:selected").text();
						   }			   
						   var data = {
								App: s3bubble_all_object.s3appid,
								Bucket: bucket
							};
							$.post(s3bubble_all_object.s3api + "main_plugin/folders/", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose folder</option><option value="">Root</option>';
								if(isSingle === true){
							   		html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder">';
							    }	
							    $.each(response, function (i, item) {
							    	var folder = item;
							    	if(isSingle === true){
										html += '<option value="' + folder + '">' + ((i === 0) ? 'root' : folder.split('/').reverse()[0]) + '</option>';
									}else{
										html += '<option value="' + folder + '">' + folder + '</option>';
									}
								});
								html += '</select>';
								$('#s3bubble-folders-shortcode').html(html);
						   },'json');
						});				
					},'json');
					setTimeout(function(){
						$(".s3bubble-lightbox-wrap").height($("#TB_window").height());
					},500);
			        $('#s3bubble-mce-submit').click(function(){
			        	var bucket     = $('#s3bucket').val();
			        	var folder     = $('#s3folder').val();
			        	var cloudfront = $('#s3cloudfront').val();
			        	var height     = $('#s3height').val();
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						if($("#s3playlist").is(':checked')){
						    var playlist = 'hidden';
						}else{
						    var playlist = 'show';
						}
			        	var order      = $('#s3order').val();
			        	if($("#s3order").is(':checked')){
						    var order = 'order="desc"';
						}
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
						if($("#s3preload").is(':checked')){
						    var preload = 'none';
						}else{
						    var preload = 'auto';
						}
	        	        var shortcode = '[s3bubbleAudio bucket="' + bucket + '" folder="' + folder + '"  height="' + height + '"  autoplay="' + autoplay + '" playlist="' + playlist + '" ' + order + ' download="' + download + '"  preload="' + preload + '"/]';
	                    if($("#s3soundcloud").is(':checked')){
						    shortcode = '[s3bubbleWaveform bucket="' + bucket + '" folder="' + folder + '" autoplay="' + autoplay + '" download="' + download + '" preload="' + preload + '" brand="s3bubble.com" comments="facebook" /]';
							
						}  
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <div class="s3bubble-lightbox-wrap">
			    <form class="s3bubble-form-general">
			    	<div class="s3bubble-video-main-form-alerts"></div>
			    	<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Your S3Bubble Buckets/Folders:</label>
				    		<span id="s3bubble-buckets-shortcode">loading buckets...</span>
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Your S3Bubble Files:</label>
				    		<span id="s3bubble-folders-shortcode">Select bucket/folder...</span>
				    	</div>
					</span>
					<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Set A Playlist Height: <i>(Do Not Add PX)</i></label>
				    		<input type="text" class="s3bubble-form-input" name="height" id="s3height">
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<input type="hidden" class="s3bubble-form-input" name="cloudfront" id="s3cloudfront">
				    	</div>
					</span>
					<span>
						<input class="s3bubble-checkbox" type="checkbox" name="soundcloud" id="s3soundcloud">BETA* Use Soundcloud Audio Player <i><a href="https://s3bubble.com/wp_plugins/s3bubble-soundcloud-wordpress-waveform-plugin-beta/" target="_blank">(Please watch video)</a></i><br />
					</span>
					<blockquote class="bs-callout-s3bubble"><strong>Extra options</strong> please just select any extra options from the list below and S3Bubble will automatically add it to the shortcode.</blockquote><br />
					<span>
						<input type="checkbox" name="autoplay" id="s3autoplay">Autoplay <i>(Start Audio On Page Load)</i><br />
						<input type="checkbox" name="playlist" id="s3playlist" value="hidden">Hide Playlist <i>(Hide Playlist On Page Load)</i><br />
						<input type="checkbox" name="order" id="s3order" value="desc">Reverse Order <i>(Reverse The Playlist Order)</i><br />
						<input class="s3bubble-checkbox" type="checkbox" name="s3preload" id="s3preload" value="true">Preload Off <i>(Prevent Tracks From Preloading)</i><br />
						<input type="checkbox" name="download" id="s3download" value="true">Show Download Links <i>(Adds A Download Button To The Tracks)</i>
					</span>
					<span>
						<a href="#"  id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
				</form>
			</div>
        	<?php
        	wp_die();
		}
		
		/*
		* Video playlist button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_playlist_ajax(){
			// echo the form
		    $s3bubble_access_key = get_option("s3-s3audible_username");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	$('#TB_ajaxContent').css({
                    	'width' : 'auto',
                    	'height' : 'auto',
                    	'padding' : '0'
                    });
			        var sendData = {
						App: s3bubble_all_object.s3appid
					};
					$.post(s3bubble_all_object.s3api + "main_plugin/live_buckets/", sendData, function(response) {	
						if(response.error){
							$(".s3bubble-video-main-form-alerts").html("<p>Oh Snap! " + response.message + ". If you do not understand this error please contact support@s3bubble.com</p>");
						}else{
							$(".s3bubble-video-main-form-alerts").html("<p>Awesome! " + response.message + ".</p>");
							var isSingle = response.data.Single;
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
						    $.each(response.data.Buckets, function (i, item) {
						    	var bucket = item.Name;
						    	if(isSingle === true){
						    		html += '<option value="s3bubble.users">' + bucket + '</option>';
						    	}else{
						    		html += '<option value="' + bucket + '">' + bucket + '</option>';	
						    	}
							});
							html += '</select>';
							$('#s3bubble-buckets-shortcode').html(html);
						}
						// Get Cloudfront ids if they are present
						var data = {
							App: s3bubble_all_object.s3appid
						};
						$.post(s3bubble_all_object.s3api + "main_plugin/list_cloudfront_distributions/", data, function(response) {
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bubble-cloudfrontid" id="s3bubble-cloudfrontid">';	
							if(response.error){
								html += '<option value="">-- No Cloudfront Distributions --</option>';
							}else{
								if(response.data.Items){
									html += '<option value="">-- Cloudfront ID --</option>';
								    $.each(response.data.Items, function (i, item) {
								    	var Cloudfront = item;
								    	html += '<option value="' + Cloudfront.Id + '">' + Cloudfront.Id + ' - ' + Cloudfront.S3Origin.DomainName + ' - Enabled: ' + Cloudfront.Enabled + '</option>';
									});
								}else{
									html += '<option value="">-- No Cloudfront Distributions --</option>';
								}
							}
							html += '</select>';
							$('#s3bubble-cloudfrontid-container').html(html);
					   	},'json');
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/admin/images/ajax-loader.gif',__FILE__); ?>"/> loading folders');
						   var bucket = $(this).val();
						   if(isSingle === true){
						   		bucket = $("#s3bucket option:selected").text();
						   }	
						   var data = {
								App: s3bubble_all_object.s3appid,
								Bucket: bucket
							};
							$.post(s3bubble_all_object.s3api + "main_plugin/folders/", data, function(response) {
							    var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose folder</option><option value="">Root</option>';
								if(isSingle === true){
							   		html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder">';
							    }	
							    $.each(response, function (i, item) {
							    	var folder = item;
							    	if(isSingle === true){
										html += '<option value="' + folder + '">' + ((i === 0) ? 'root' : folder.split('/').reverse()[0]) + '</option>';
									}else{
										html += '<option value="' + folder + '">' + folder + '</option>';
									}
								});
								html += '</select>';
								$('#s3bubble-folders-shortcode').html(html);
						   },'json');
						});				
					},'json');
					setTimeout(function(){
						$(".s3bubble-lightbox-wrap").height($("#TB_window").height());
					},500);
			        $('#s3bubble-mce-submit').click(function(){
			        	var bucket       = $('#s3bucket').val();
			        	var folder       = $('#s3folder').val();
			        	var height       = $('#s3height').val();
			        	var cloudfrontid = $('#s3bubble-cloudfrontid').val();
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						if($("#s3playlist").is(':checked')){
						    var playlist = 'hidden';
						}else{
						    var playlist = 'show';
						}
			        	var order      = $('#s3order').val();
			        	if($("#s3order").is(':checked')){
						    var order = 'order="desc"';
						}
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
						var aspect = '16:9';
						if($('#s3aspect').val() != ''){
						    aspect = $('#s3aspect').val();
						}
	        	        var shortcode = '[s3bubbleVideo bucket="' + bucket + '" folder="' + folder + '" aspect="' + aspect + '"  height="' + height + '"  autoplay="' + autoplay + '" playlist="' + playlist + '" ' + order + ' download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <div class="s3bubble-lightbox-wrap">
			    <form class="s3bubble-form-general">
			    	<div class="s3bubble-video-main-form-alerts"></div>
			    	<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Your S3Bubble Buckets/Folders:</label>
				    		<span id="s3bubble-buckets-shortcode">loading buckets...</span>
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Your S3Bubble Files:</label>
				    		<span id="s3bubble-folders-shortcode">Select bucket/folder...</span>
				    	</div>
					</span>
					<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Aspect Ratio: (Example: 16:9 / 4:3 Default: 16:9)</label>
				    		<input type="text" class="s3bubble-form-input" name="aspect" id="s3aspect">
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Set A Playlist Height:</label>
				    		<input type="text" class="s3bubble-form-input" name="height" id="s3height">
				    	</div>
					</span>
					<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Set Cloudfront Distribution Id:</label>
				    		<span id="s3bubble-cloudfrontid-container">Select Cloudfront...</span>
				    	</div>
					</span>
					<blockquote class="bs-callout-s3bubble"><strong>Extra options</strong> please just select any extra options from the list below and S3Bubble will automatically add it to the shortcode.</blockquote>
					<span>
						<input type="checkbox" name="autoplay" id="s3autoplay">Autoplay <i>(Start Video On Page Load)</i><br />
						<input type="checkbox" name="playlist" id="s3playlist" value="hidden">Hide Playlist <i>(Hide Playlist On Page Load)</i><br />
						<input type="checkbox" name="order" id="s3order" value="desc">Reverse Order <i>(Reverse The Playlist Order)</i><br />
						<input type="checkbox" name="download" id="s3download" value="true">Show Download Links <i>(Adds A Download Button To The Videos)</i>
					</span>
				    <span>
						<a href="#"  id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
				</form>
			</div>
        	<?php
        	wp_die();
		}
        
		/*
		* Single video button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_single_ajax(){
		    // echo the form
		    $s3bubble_access_key = get_option("s3-s3audible_username");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {

		        	$('.s3bubble-tinymce-video-select').click(function(){
						$('.s3bubble-tinymce-video-select').removeClass("s3bubble-tinymce-video-selected");
						$(this).addClass('s3bubble-tinymce-video-selected')
						return false;
					});

		        	// Setup vars
		        	var StreamingType  = 'progressive';
		        	var FileExtension  = 'mp4';

		        	$('#TB_ajaxContent').css({
                    	'width' : 'auto',
                    	'height' : 'auto',
                    	'padding' : '0'
                    });
			        var sendData = {
						App: s3bubble_all_object.s3appid
					};
					$.post(s3bubble_all_object.s3api + "main_plugin/live_buckets/", sendData, function(response) {
						if(response.error){
							$(".s3bubble-video-main-form-alerts").html("<p>Oh Snap! " + response.message + ". If you do not understand this error please contact support@s3bubble.com</p>");
						}else{
							$(".s3bubble-video-main-form-alerts").html("<p>Awesome! " + response.message + ".</p>");
							var isSingle = response.data.Single;
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
						    $.each(response.data.Buckets, function (i, item) {
						    	var bucket = item.Name;
						    	if(isSingle === true){
						    		html += '<option value="s3bubble.users">' + bucket + '</option>';
						    	}else{
						    		html += '<option value="' + bucket + '">' + bucket + '</option>';	
						    	}
							});
							html += '</select>';
							$('#s3bubble-buckets-shortcode').html(html);
						}
						// Get Cloudfront ids if they are present
						var data = {
							App: s3bubble_all_object.s3appid
						};
						$.post(s3bubble_all_object.s3api + "main_plugin/list_cloudfront_distributions/", data, function(response) {
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bubble-cloudfrontid" id="s3bubble-cloudfrontid">';	
							if(response.error){
								html += '<option value="">-- No Cloudfront Distributions --</option>';
							}else{
								html += '<option value="">-- Cloudfront ID --</option>';
								if(response.data.Items){
								    $.each(response.data.Items, function (i, item) {
								    	var Cloudfront = item;
								    	html += '<option value="' + Cloudfront.Id + '">' + Cloudfront.Id + ' - ' + Cloudfront.S3Origin.DomainName + ' - Enabled: ' + Cloudfront.Enabled + '</option>';
									});
								}else{
									html += '<option value="">-- No Cloudfront Distributions --</option>';
								}
							}
							html += '</select>';
							$('#s3bubble-cloudfrontid-container').html(html);
					   	},'json');

						// Runs when a bucket is selected
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/admin/images/ajax-loader.gif',__FILE__); ?>"/> loading videos files');
						   var bucket = $(this).val();
						   if(isSingle === true){
						   		bucket = $("#s3bucket option:selected").text();
						   }
							var data = {
								App: s3bubble_all_object.s3appid,
								Bucket: bucket
							};
							$.post(s3bubble_all_object.s3api + "main_plugin/video_files/", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose video</option>';
							    $.each(response, function (i, item) {
							    	if(isSingle === true){
										html += '<option value="' + item + '">' + item + '</option>';
									}else{
								    	var folder = item.Key;
								    	var ext    = folder.split('.').pop();
								    	if(ext == 'mp4' || ext === 'm4v' || ext === 'm3u8'){
								    		html += '<option value="' + folder + '" data-ext="' + ext + '">' + folder + '</option>';
								    	}
								    }
								});
								html += '</select>';
								$('#s3bubble-folders-shortcode').html(html);
						   },'json');
						});				
					},'json');
					$( "#s3bubble-streaming-type" ).change(function() {
						StreamingType = $(this).val();
					});
					setTimeout(function(){
						$(".s3bubble-lightbox-wrap").height($("#TB_window").height());
					},500);

					$('#s3bubble-mce-submit-iframe').click(function(){

						// Setup vars
			        	var bucket       = $('#s3bucket').val();
			        	var folder       = $('#s3folder').val();
			        	var cloudfrontid = ($('#s3bubble-cloudfrontid').val() != undefined) ? $('#s3bubble-cloudfrontid').val() : "";
			        	var extension    = $('#s3folder').find(':selected').data('ext');
			        	//Set extra options
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
						var aspect = '16:9';
						if($('#s3aspect').val() != ''){
						    aspect = $('#s3aspect').val();
						}

						var data = {
							AccessKey: s3bubble_all_object.s3appid,
							bucket: bucket,
							key: folder,
							Distribution : cloudfrontid,
							StreamingType : StreamingType
						};

						$.post(s3bubble_all_object.s3api + "iframe/get_code/", data, function(response) {

							if(response.error){

								alert(response.message);

							}else{
								
								var code = response.data;
						   		shortcode = '[s3bubbleVideoSingleIframe code="' + code + '" supplied="' + StreamingType + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
								tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
		                    	tb_remove();
		                    }

                    	},'json');

					});

			        $('#s3bubble-mce-submit').click(function(){

			        	// Setup vars
			        	var bucket       = $('#s3bucket').val();
			        	var folder       = $('#s3folder').val();
			        	var cloudfrontid = $('#s3bubble-cloudfrontid').val();
			        	var extension    = $('#s3folder').find(':selected').data('ext');
			        	var lightboxtext = $("#lightbox-text").val();
			        	var type         = $('.s3bubble-tinymce-video-selected').data("type");

			        	if(bucket === '' || folder === ''){

			        		alert("You must set a bucket and video to insert shortcode.");
			        	
			        	}else{

				        	//Set extra options
				        	if($("#s3autoplay").is(':checked')){
							    var autoplay = true;
							}else{
							    var autoplay = false;
							}
							if($("#s3download").is(':checked')){
							    var download = true;
							}else{
							    var download = false;
							}
							var aspect = '16:9';
							if($('#s3aspect').val() != ''){
							    aspect = $('#s3aspect').val();
							}

							var start = false;
							if($('#s3bubble-preview-starttime').val() != ''){
							    start = $('#s3bubble-preview-starttime').val();
							}

							var finish = false;
							if($('#s3bubble-preview-finishtime').val() != ''){
							    finish = $('#s3bubble-preview-finishtime').val();
							}

							var data = {
								AccessKey: s3bubble_all_object.s3appid,
								bucket: bucket,
								key: folder,
								Distribution : cloudfrontid
							};
							var shortcode = '';
							if(StreamingType === 'progressive'){
								shortcode = '[s3bubbleVideoSingle bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '" start="' + start + '" finish="' + finish + '" advert_link="https://s3bubble.com" disable_skip="false" /]';
								if(lightboxtext !== ""){
									shortcode = '[s3bubbleLightboxVideoSingle text="' + lightboxtext + '" bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '" advert_link="https://s3bubble.com" disable_skip="false" /]';
								}else if(type === "mediajs"){
								    shortcode = '[s3bubbleMediaElementVideo bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"/]';
								}else if(type === "videojs"){
									shortcode = '[s3bubbleVideoSingleJs  bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '" advert_link="https://s3bubble.com" disable_skip="false" /]';
								}
								tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
		                    	tb_remove();
							}
							if(StreamingType === 'hls'){
								if(extension !== 'm3u8'){
									alert('To use HLS streaming your file extension needs to be .m3u8');
								}else{
									shortcode = '[s3bubbleHlsVideo bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" advert_link="https://s3bubble.com" disable_skip="false" /]';
									if(type === "videojs"){
										shortcode = '[s3bubbleHlsVideoJs bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" advert_link="https://s3bubble.com" disable_skip="false" /]';
									}
									tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
		                    		tb_remove();
								}
							}
							if(StreamingType === 'rtmp'){
								if(cloudfrontid === ''){
									alert('To use RTMP streaming you need to specify a Cloudfront Distribution ID');
								}else{
									shortcode = '[s3bubbleRtmpVideoDefault bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '" start="' + start + '" finish="' + finish + '" advert_link="https://s3bubble.com" disable_skip="false" /]';
									if(type === "mediajs"){
										shortcode = '[s3bubbleRtmpVideo bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" cloudfront="' + cloudfrontid + '"/]';
									}else if(type === "videojs"){
										shortcode = '[s3bubbleRtmpVideoJs bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" cloudfront="' + cloudfrontid + '" fallback="true" advert_link="https://s3bubble.com" disable_skip="false" /]';
									}
									tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			                   	 	tb_remove();
			                   	}
							}
						}
			        });
		        })
		    </script>
		    <div class="s3bubble-lightbox-wrap">
			    <form class="s3bubble-form-general">
	                <div class="s3bubble-video-main-form-alerts"></div>
			    	<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Your S3Bubble Buckets/Folders:</label>
				    		<span id="s3bubble-buckets-shortcode">loading buckets...</span>
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Your S3Bubble Files:</label>
				    		<span id="s3bubble-folders-shortcode">Select bucket/folder...</span>
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
							<label for="fname">Select Streaming Type:</label>
				    		<select class="form-control input-lg" tabindex="1" name="s3bubble-streaming-type" id="s3bubble-streaming-type">
				    			<option value="progressive">Progressive</option>
				    			<option value="rtmp">Rtmp</option>
				    			<option value="hls">HLS</option>
				    		</select>
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Cloudfront Distribution Id: (RTMP Only)</label>
				    		<span id="s3bubble-cloudfrontid-container">Select Cloudfront...</span>
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="aspect">Aspect Ratio: (Example: 16:9 / 4:3 / full Default: 16:9)</label>
				    		<input type="text" class="s3bubble-form-input" name="aspect" id="s3aspect">
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="lightbox-text">Lightbox link text: <a class="s3bubble-pull-right" href="https://s3bubble.com/s3bubble-video-lightbox/" target="_blank">Watch Video</a></label>
				    		<input type="text" class="s3bubble-form-input" name="lightbox-text" id="lightbox-text">
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="s3bubble-preview-starttime">Start time percent for preview: (leave blank to ignore)</label>
				    		<input type="text" class="s3bubble-form-input" name="s3bubble-preview-starttime" id="s3bubble-preview-starttime">
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="s3bubble-preview-finishtime">End time percent for preview: (leave blank to ignore)<a class="s3bubble-pull-right" href="https://s3bubble.com/s3bubble-video-preview-example/" target="_blank">Watch Video</a></label>
				    		<input type="text" class="s3bubble-form-input" name="s3bubble-preview-finishtime" id="s3bubble-preview-finishtime">
				    	</div>
					</span>
					<span>
						<label for="fname">Player Selection: (Only the default player supports adverts)</label>
					</span> 
					<span>
						<div data-type="videojs" class="s3bubble-tinymce-video-select s3bubble-tinymce-video-selected">
							<div>
								<h4>Video JS</h4>
								<em>Recomended</em>
							</div> 
						</div>
						<div data-type="jplayer" class="s3bubble-tinymce-video-select">
							<div>
								<h4>jPlayer</h4>
								<em>Good choice</em>
							</div> 
						</div>
						<div data-type="mediajs" class="s3bubble-tinymce-video-select">
							<div>
								<h4>Media Element</h4>
								<em>Default Wordpress</em>
							</div> 
						</div>
					</span>
	                <blockquote class="bs-callout-s3bubble"><strong>Extra options:</strong> please just select any extra options from the list below, and S3Bubble will automatically add it to the shortcode.</blockquote>
					<span>
						<input type="checkbox" name="autoplay" id="s3autoplay">Autoplay: <i>(Start Video On Page Load)</i><br />
						<input type="checkbox" name="download" id="s3download" value="true">Show Download Links <i>(Add A Download Button To The Video) - Not available for VideoJS or Media Element Players</i><br />
					</span>
					<span>
						<a href="#" id="s3bubble-mce-submit-iframe" class="s3bubble-pull-left button media-button button-primary button-large media-button-gallery">Insert Iframe</a>
						<a href="#" id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
				</form>
		    </div>
        	<?php
        	wp_die();
		}

        /*
		* Single audio button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_audio_single_ajax(){

		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	
		        	// Setup vars
		        	var StreamingType  = 'progressive';

                    $('#TB_ajaxContent').css({
                    	'width' : 'auto',
                    	'height' : 'auto',
                    	'padding' : '0'
                    }); 
			        var sendData = {
						App: s3bubble_all_object.s3appid
					};
					$.post(s3bubble_all_object.s3api + "main_plugin/live_buckets/", sendData, function(response) {
						if(response.error){
							$(".s3bubble-video-main-form-alerts").html("<p>Oh Snap! " + response.message + ". If you do not understand this error please contact support@s3bubble.com</p>");
						}else{
							$(".s3bubble-video-main-form-alerts").html("<p>Awesome! " + response.message + ".</p>");
							var isSingle = response.data.Single;
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
						    $.each(response.data.Buckets, function (i, item) {
						    	var bucket = item.Name;
						    	if(isSingle === true){
						    		html += '<option value="s3bubble.users">' + bucket + '</option>';
						    	}else{
						    		html += '<option value="' + bucket + '">' + bucket + '</option>';	
						    	}
							});
							html += '</select>';
							$('#s3bubble-buckets-shortcode').html(html);
						}
						// Get Cloudfront ids if they are present
						var data = {
							App: s3bubble_all_object.s3appid
						};
						$.post(s3bubble_all_object.s3api + "main_plugin/list_cloudfront_distributions/", data, function(response) {
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bubble-cloudfrontid" id="s3bubble-cloudfrontid">';	
							if(response.error){
								html += '<option value="">-- No Cloudfront Distributions --</option>';
							}else{
								html += '<option value="">-- Cloudfront ID --</option>';
								if(response.data.Items){
								    $.each(response.data.Items, function (i, item) {
								    	var Cloudfront = item;
								    	html += '<option value="' + Cloudfront.Id + '">' + Cloudfront.Id + ' - ' + Cloudfront.S3Origin.DomainName + ' - Enabled: ' + Cloudfront.Enabled + '</option>';
									});
								}else{
									html += '<option value="">-- No Cloudfront Distributions --</option>';
								}
							}
							html += '</select>';
							$('#s3bubble-cloudfrontid-container').html(html);
					   	},'json');
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/admin/images/ajax-loader.gif',__FILE__); ?>"/> loading audio files');
						   var bucket = $(this).val();
						   if(isSingle === true){
						   		bucket = $("#s3bucket option:selected").text();
						   }
						   var data = {
								App: s3bubble_all_object.s3appid,
								Bucket: bucket
							};
							$.post(s3bubble_all_object.s3api + "main_plugin/audio_files/", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose audio</option>';
							    $.each(response, function (i, item) {
							    	if(isSingle === true){
										html += '<option value="' + item + '">' + item + '</option>';
									}else{
										var folder = item.Key;
								    	var ext    = folder.split('.').pop();
								    	if(ext == 'mp3' || ext === 'm4a' || ext === 'wav' || ext === 'm3u8'){
								    		html += '<option value="' + folder + '">' + folder + '</option>';
								    	}
									}
								});
								html += '</select>';
								$('#s3bubble-folders-shortcode').html(html);
						   },'json');
						});				
					},'json');
					$( "#s3bubble-streaming-type" ).change(function() {
						StreamingType = $(this).val();
					});	
					setTimeout(function(){
						$(".s3bubble-lightbox-wrap").height($("#TB_window").height());
					},500);
					$('#s3bubble-mce-submit-iframe').click(function(){

						// Setup vars
			        	var bucket       = $('#s3bucket').val();
			        	var folder       = $('#s3folder').val();
			        	var cloudfrontid = $('#s3bubble-cloudfrontid').val();
			        	if(bucket === '' || folder === ''){
			        		alert('Please select a bucket and track');
			        		return false;
			        	}
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
						if($("#s3style").is(':checked')){
						    var style = 'plain';
						}else{
						    var style = 'bar';
						}
						if($("#s3preload").is(':checked')){
						    var preload = 'none';
						}else{
						    var preload = 'auto';
						}

						var data = {
							AccessKey: s3bubble_all_object.s3appid,
							bucket: bucket,
							key: folder,
							Distribution : cloudfrontid,
							StreamingType : StreamingType
						};

						$.post(s3bubble_all_object.s3api + "iframe/get_code/", data, function(response) {

							if(response.error){

								alert(response.message);

							}else{
								
								var code = response.data;
						   		shortcode = '[s3bubbleAudioSingleIframe code="' + code + '" supplied="' + StreamingType + '" autoplay="' + autoplay + '" download="' + download + '" style="' + style + '" preload="' + preload + '"/]';
								tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
		                    	tb_remove();
		                    }

                    	},'json');

					});

			        $('#s3bubble-mce-submit').click(function(){
			        	var bucket       = $('#s3bucket').val();
			        	var folder       = $('#s3folder').val();
			        	var cloudfrontid = $('#s3bubble-cloudfrontid').val();
			        	if(bucket === '' || folder === ''){
			        		alert('Please select a bucket and track');
			        		return false;
			        	}
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						if($("#s3download").is(':checked')){
						    var download = true;
						}else{
						    var download = false;
						}
						if($("#s3style").is(':checked')){
						    var style = 'plain';
						}else{
						    var style = 'bar';
						}
						if($("#s3preload").is(':checked')){
						    var preload = 'none';
						}else{
						    var preload = 'auto';
						}
						var shortcode = '[s3bubbleAudioSingle bucket="' + bucket + '" track="' + folder + '" autoplay="' + autoplay + '" download="' + download + '" style="' + style + '" preload="' + preload + '"/]';
						if($("#s3mediaelement").is(':checked')){
						    shortcode = '[s3bubbleMediaElementAudio bucket="' + bucket + '" track="' + folder + '" autoplay="' + autoplay + '" download="' + download + '" style="' + style + '" preload="' + preload + '"/]';
							
						}
						if($("#s3soundcloud").is(':checked')){
						    shortcode = '[s3bubbleWaveformSingle bucket="' + bucket + '" track="' + folder + '" autoplay="' + autoplay + '" download="' + download + '" style="' + style + '" preload="' + preload + '" brand="s3bubble.com" comments="facebook" /]';
							
						}  
						if(StreamingType === 'rtmp'){
							if(cloudfrontid === ''){
								alert('To use RTMP streaming you need to specify a Cloudfront Distribution ID');
							}else{
								shortcode = '[s3bubbleRtmpAudioDefault bucket="' + bucket + '" track="' + folder + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"  style="' + style + '" preload="' + preload + '"/]';
		                   	}
						}
						if(StreamingType === 'hls'){
							shortcode = '[s3bubbleHlsAudioDefault bucket="' + bucket + '" track="' + folder + '" autoplay="' + autoplay + '" download="' + download + '" cloudfront="' + cloudfrontid + '"  style="' + style + '" preload="' + preload + '"/]';
						}  
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <div class="s3bubble-lightbox-wrap">
			    <form class="s3bubble-form-general">
			    	<div class="s3bubble-video-main-form-alerts"></div>
			    	<span>
				    	<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="fname">Your S3Bubble Buckets/Folders:</label>
				    		<span id="s3bubble-buckets-shortcode">loading buckets...</span>
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Your S3Bubble Files:</label>
				    		<span id="s3bubble-folders-shortcode">Select bucket/folder...</span>
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
							<label for="fname">Select Streaming Type:</label>
				    		<select class="form-control input-lg" tabindex="1" name="s3bubble-streaming-type" id="s3bubble-streaming-type">
				    			<option value="progressive">Progressive</option>
				    			<option value="hls">HLS Adaptive Bitrate</option>
				    			<option value="rtmp">Rtmp</option>
				    		</select>
				    	</div>
				    	<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="fname">Set Cloudfront Distribution Id:</label>
				    		<span id="s3bubble-cloudfrontid-container">Select Cloudfront...</span>
				    	</div>
					</span>
					<span>
						<input class="s3bubble-checkbox" type="checkbox" name="soundcloud" id="s3soundcloud">BETA* Use Soundcloud Audio Player <i><a href="https://s3bubble.com/wp_plugins/s3bubble-soundcloud-wordpress-waveform-plugin-beta/" target="_blank">(Please watch video)</a></i><br />
					</span>
					<input type="hidden" class="s3bubble-form-input" name="cloudfront" id="s3cloudfront">
					<blockquote class="bs-callout-s3bubble"><strong>Extra options:</strong> please just select any extra options from the list below, and S3Bubble will automatically add it to the shortcode.</blockquote>
					<span>
						<input class="s3bubble-checkbox" type="checkbox" name="autoplay" id="s3autoplay">Autoplay <i>(Start Audio On Page Load)</i><br />
						<input class="s3bubble-checkbox" type="checkbox" name="style" id="s3style" value="true">Remove Bar <i>(Remove The Info Bar Under Player)</i><br />
						<input class="s3bubble-checkbox" type="checkbox" name="preload" id="s3preload" value="true">Preload Off <i>(Prevent Track From Preloading)</i><br />
						<input class="s3bubble-checkbox" type="checkbox" name="download" id="s3download" value="true">Show Download Links <i>(Add A Download Button To The Track)</i><br />
						<input type="checkbox" name="mediaelement" id="s3mediaelement" value="true">Use Media Elements JS <i>(Changes the player from default to media element js player)</i>
					</span>
					<span>
						<a href="#" id="s3bubble-mce-submit-iframe" class="s3bubble-pull-left button media-button button-primary button-large media-button-gallery">Insert Iframe</a>
						<a href="#" id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
				</form>
			</div>
        	<?php
        	wp_die();
		}

		/*
		* Single audio button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_live_stream_ajax(){

		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        		
                    $('#TB_ajaxContent').css({
                    	'width' : 'auto',
                    	'height' : 'auto',
                    	'padding' : '0'
                    });
                    setTimeout(function(){
						$(".s3bubble-lightbox-wrap").height($("#TB_window").height());
					},500); 
			        $('#s3bubble-mce-submit').click(function(){

			        	var rtmp   = $('#s3bubble-live-stream-rtmp-url').val();
			        	var stream = $('#s3bubble-live-stream-url').val();
			        	var aspect = '16:9';
						if($('#s3aspect').val() != ''){
						    aspect = $('#s3aspect').val();
						}
						if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						var comments = $('#s3comments').val();
						if($("#s3fblike").is(':checked')){
						    var fblike = true;
						}else{
						    var fblike = false;
						}
						var shortcode = '[s3bubbleLiveStreamMedia rtmp="' + rtmp + '" stream="' + stream + '" aspect="' + aspect + '" autoplay="' + autoplay + '" comments="' + comments + '" fblike="' + fblike + '" /]';
	                    if($("#select-videojs").is(':checked')){
						    shortcode = '[s3bubbleLiveStream rtmp="' + rtmp + '" stream="' + stream + '" aspect="' + aspect + '" autoplay="' + autoplay + '" comments="' + comments + '" fblike="' + fblike + '" /]';
						} 
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <div class="s3bubble-lightbox-wrap">
			    <form class="s3bubble-form-general">
			    	<span>
				    	<div>
				    		<label for="s3bubble-live-stream-rtmp-url">RTMP Flash Stream Url (Only works on Desktop | supply a url below for fallback): <a class="s3bubble-pull-right" href="https://s3bubble.com/s3bubble-live-broadcasting-app/" target="_blank">Watch Tutorial</a></label>
				    		<input type="text" class="s3bubble-form-input" placeholder="rtmp://54.152.190.21/live/( your s3bubble username )" name="s3bubble-live-stream-rtmp-url" id="s3bubble-live-stream-rtmp-url">
				    		<small>When streaming in RTMP protocol the stream will have hardly any delay.</small>
				    	</div>
					</span>
					<span>
				    	<div>
				    		<label for="s3bubble-live-stream-url">HLS Live Stream (Works Mobile and Desktop):</label>
				    		<input type="text" class="s3bubble-form-input" placeholder="http://54.152.190.21/hls/( your s3bubble username ).m3u8" name="s3bubble-live-stream-url" id="s3bubble-live-stream-url">
				    		<small>When streaming in HLS protocol the stream will be a slight delay.</small>
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="aspect">Aspect Ratio: (Example: 16:9 / 4:3 Default: 16:9)</label>
				    		<input type="text" class="s3bubble-form-input" name="aspect" id="s3aspect">
				    	</div>
						<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="aspect">Add Facebook comments to your stream?: (Example: facebook)</label>
				    		<input type="text" class="s3bubble-form-input" name="comments" id="s3comments">
				    	</div>
					</span>
					<span>
						<input class="s3bubble-checkbox" type="checkbox" name="select-videojs" id="select-videojs">Use Video Js limited browser support<br />
					</span>
					<span>
						<input type="checkbox" name="fblike" id="s3fblike">Add a FaceBook like button?<br />
						<input type="checkbox" name="autoplay" id="s3autoplay">Autoplay: <i>(Start Stream On Page Load)</i><br />
					</span> 
					<span>
						<div class="s3bubble-video-main-form-alerts">
							<p>
					    		LIVE STREAM DIRECTLY TO THIS POST! For more information on setting up a Live Stream directly from a mobile app to this post please open this link.
					    		<a href="https://s3bubble.com/s3bubble-live-broadcasting-app/" target="_blank">LIVE STREAMING TUTORIAL</a>
					    		<h3>You can use this url as a test url, paste in above</h3>
					    		<pre>http://vevoplaylist-live.hls.adaptive.level3.net/vevo/ch1/appleman.m3u8</pre>
					    	</p>
				    	</div>
					</span>
					<span>
						<a href="https://itunes.apple.com/us/app/s3bubble-broadcaster/id1025065771?mt=8" target="_blank" class="s3bubble-pull-left s3bubble-large-button-shortcode button media-button button-primary button-large media-button-gallery">Broadcasting App</a>
						<a href="#" id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
					<span>
						<div class="s3bubble-video-main-form-alerts">
							<p>
					    		Download the S3Bubble broadcast app and stream directly to this post.
					    	</p>
				    	</div>
					</span>
				</form>
			</div>
        	<?php
        	wp_die();
		}


		/*
		* Single audio button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_live_stream_mobile_ajax(){

		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        		
                    $('#TB_ajaxContent').css({
                    	'width' : 'auto',
                    	'height' : 'auto',
                    	'padding' : '0'
                    });
                    setTimeout(function(){
						$(".s3bubble-lightbox-wrap").height($("#TB_window").height());
					},500);
					var sendData = {
						App: s3bubble_all_object.s3appid
					};
					$.post(s3bubble_all_object.s3api + "live/user_stream", sendData, function(response) {
						if(response.error){
							$(".s3bubble-video-main-form-alerts").html("<p>Oh Snap! " + response.message + ". If you do not understand this error please contact support@s3bubble.com</p>");
						}else{
							$("#s3stream").val(response.stream);
						}
					},"json"); 
			        $('#s3bubble-mce-submit').click(function(){

			        	var ip = $('#s3streamip').val();
			        	var stream = $('#s3stream').val();
			        	var aspect = '16:9';
						if($('#s3aspect').val() != ''){
						    aspect = $('#s3aspect').val();
						}
						if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						var comments = $('#s3comments').val();
						if($("#s3fblike").is(':checked')){
						    var fblike = true;
						}else{
						    var fblike = false;
						}
			        	
						var shortcode = '[s3bubbleMobileAppBroadcast ip="' + ip + '" stream="' + stream + '" aspect="' + aspect + '" autoplay="' + autoplay + '" comments="' + comments + '" fblike="' + fblike + '" /]';
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <div class="s3bubble-lightbox-wrap">
			    <form class="s3bubble-form-general">
			    	<div class="s3bubble-video-main-form-alerts"></div>
			    	<span>
			    		<div class="s3bubble-pull-left s3bubble-width-left">
							<label for="s3streamip">Your Live Stream IP: <a class="s3bubble-pull-right" href="https://www.youtube.com/watch?v=m3H7k1DsW8A" target="_blank">Watch Tutorial</a></label>
				    		<select class="form-control input-lg" tabindex="1" name="s3streamip" id="s3streamip" placeholder="Select stream ip">
				    			<option value="s3bubblelive.com">s3bubblelive.com</option>
				    			<option value="54.152.190.21">54.152.190.21</option>
				    		</select>
				    	</div>
						<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="aspect">Enter your live stream username example (s3bubble)</label>
				    		<input type="text" class="s3bubble-form-input" name="stream" id="s3stream">
				    	</div>
					</span>
					<span>
						<div class="s3bubble-pull-left s3bubble-width-left">
				    		<label for="aspect">Aspect Ratio: (Example: 16:9 / 4:3 Default: 16:9)</label>
				    		<input type="text" class="s3bubble-form-input" name="aspect" id="s3aspect">
				    	</div>
						<div class="s3bubble-pull-right s3bubble-width-right">
				    		<label for="aspect">Add Facebook comments to your stream?: (Example: facebook)</label>
				    		<input type="text" class="s3bubble-form-input" name="comments" id="s3comments">
				    	</div>
					</span>
					<span>
						<input type="checkbox" name="fblike" id="s3fblike">Add a FaceBook like button?<br />
						<input type="checkbox" name="autoplay" id="s3autoplay">Autoplay: <i>(Start Stream On Page Load)</i><br />
					</span> 
					<span>
						<div class="s3bubble-video-main-form-alerts">
							<p>
					    		LIVE STREAM DIRECTLY TO THIS POST! For more information on setting up a Live Stream directly from a mobile app to this post please open this link.
					    		<a href="https://www.youtube.com/watch?v=m3H7k1DsW8A" target="_blank">LIVE STREAMING TUTORIAL</a>
					    	</p>
				    	</div>
					</span>
					<span>
						<a href="https://itunes.apple.com/us/app/s3bubble-broadcaster/id1025065771?mt=8" target="_blank" class="s3bubble-pull-left s3bubble-large-button-shortcode button media-button button-primary button-large media-button-gallery">Broadcasting App</a>
						<a href="#" id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
					</span>
					<span>
						<div class="s3bubble-video-main-form-alerts">
							<p>
					    		Download the S3Bubble broadcast app and stream directly to this post.
					    	</p>
				    	</div>
					</span>
				</form>
			</div>
        	<?php
        	wp_die();
		}
        
		/*
		* Sets up tiny mce plugins
		* @author sameast
		* @none
		*/ 
		function s3bubble_buttons() {
			if ( current_user_can( 'manage_options' ) )  {
				add_filter( 'mce_external_plugins', array( $this, 's3bubble_add_buttons' ) ); 
				add_filter( 'mce_buttons', array( $this, 's3bubble_register_buttons' ) );
			} 
		}
		
		/*
		* Adds the menu item to the tiny mce
		* @author sameast
		* @none
		*/ 
		function s3bubble_add_buttons( $plugin_array ) {
		    $plugin_array['s3bubble'] = plugins_url('/admin/js/s3bubble.video.all.tinymce.min.js',__FILE__);
		    return $plugin_array;
		}
		
		/*
		* Registers the amount of buttons
		* @author sameast
		* @none
		*/ 
		function s3bubble_register_buttons( $buttons ) {
		    array_push( $buttons, 's3bubble_live_stream_mobile_shortcode', 's3bubble_live_stream_shortcode', 's3bubble_audio_single_shortcode', 's3bubble_audio_playlist_shortcode', 's3bubble_video_single_shortcode', 's3bubble_video_playlist_shortcode' ); 
		    return $buttons;
		}

		/*
		* Cleans the options removing white space etc...
		* @author sameast
		* @none
		*/ 
		function s3bubble_clean_options( $val ) {
		    return trim(stripslashes(wp_filter_post_kses(addslashes($val))));
		}

		/*
		* Gets the domain name without ip
		* @author sameast
		* @none
		*/ 
		function get_domain($url){

		  $pieces = parse_url($url);
		  $domain = isset($pieces['host']) ? $pieces['host'] : '';
		  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
		    return $regs['domain'];
		  }
		  return false;

		}

        function check_user_agent ( $type = NULL ) {
		        $user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
		        if ( $type == 'bot' ) {
		                // matches popular bots
		                if ( preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent ) ) {
		                        return true;
		                        // watchmouse|pingdom\.com are "uptime services"
		                }
		        } else if ( $type == 'browser' ) {
		                // matches core browser types
		                if ( preg_match ( "/mozilla\/|opera\//", $user_agent ) ) {
		                        return true;
		                }
		        } else if ( $type == 'mobile' ) {
		                // matches popular mobile devices that have small screens and/or touch inputs
		                // mobile devices have regional trends; some of these will have varying popularity in Europe, Asia, and America
		                // detailed demographics are unknown, and South America, the Pacific Islands, and Africa trends might not be represented, here
		                if ( preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ) {
		                        // these are the most common
		                        return true;
		                } else if ( preg_match ( "/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent ) ) {
		                        // these are less common, and might not be worth checking
		                        return true;
		                }
		        }
		        return false;
		}

		/*
		* Outputs the s3bubble analytics
		* @author sameast
		* @none
		*/ 
		function s3bubble_advertiser($atts){

			extract( shortcode_atts( array(
				'url'          =>  "https://media.s3bubble.com/embed/hls/id/mLQScWNHp",
	            'width'        =>  360, // player width
	            'height'       =>  203, // player height
	            'top'          =>  "", // pixels from bottom
	            'bottom'       =>  "", // pixels from right
	            'left'         =>  "", // pixels from right
	            'right'        =>  "", // pixels from right
	            'controls'     =>  "shown", // shown|hidden
	            'autoplay'     =>  "false", // true|false
	            'audio'        =>  "on", // true|false
	            'advert_link'  =>  "false", // true|false
	            'advert_url'   =>  "s3bubble.com" // any url without http://
			), $atts, 's3bubbleAdvertiser' ) );
            
            $params = json_encode(array(
						'url'         =>  $url,
						'width'       =>  $width,
						'height'      =>  $height,
						'top'         =>  $top,
						'bottom'      =>  $bottom,
						'left'        =>  $left,
						'right'       =>  $right,
						'controls'    =>  $controls,
						'autoplay'    =>  $autoplay,
						'audio'       =>  $audio,
						'advert_link' =>  $advert_link,
						'advert_url'  =>  $advert_url
					));

			return "<div class='s3bubble-advertiser' data-params='" . $params . "'></div>";

		}

		// -------------------------- IFRAME PLAYERS SETUPS BELOW ------------------------------ //

		/*
		* Run the s3bubble single player iframe code
		* @author sameast
		* @none
		*/ 
		function s3bubble_jplayer_video_progressive_iframe($atts){

			extract( shortcode_atts( array(
				'aspect'     => '16:9',
				'autoplay'   => 'false',
				'code'       => '',
				'supplied'   => 'video'
			), $atts, 's3bubbleVideoSingleIframe' ) );

			$code      = ((empty($code)) ? false : $code);
			$supplied  = ((empty($supplied) || $supplied == 'progressive') ? 'progressive' : $supplied);

			return '<div style="position:relative;padding-bottom:56.25%;"><iframe style="position:absolute;top:0;left:0;width:100%;height:100%;" src="//media.s3bubble.com/embed/' . $supplied . '/id/' . $code . '" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" allowtransparency="true" webkitAllowFullScreen="true" mozallowfullscreen="true" allowFullScreen="true"></iframe></div>';

		}

		/*
		* Run the s3bubble single player iframe code
		* @author sameast
		* @none
		*/ 
		function s3bubble_jplayer_audio_progressive_iframe($atts){

			extract( shortcode_atts( array(
				'aspect'     => '16:9',
				'autoplay'   => 'false',
				'code'       => '',
				'supplied'   => 'audio',
				'style'      => 'bar'
			), $atts, 's3bubbleAudioSingleIframe' ) );

            $autoplay  = ((empty($autoplay) || $autoplay == 'false') ? 'no' : 'autoplay');
			$code      = ((empty($code)) ? false : $code);
			$aspect    = ((empty($aspect)) ? false : $aspect);
			$supplied  = ((empty($supplied) || $supplied == 'progressive') ? 'audio' : $supplied);
			$style     = ((empty($style) || $style == 'bar') ? 75 : 35);

			return '<iframe style="width:100%;height:' . $style . 'px;" src="//media.s3bubble.com/' . $supplied . '/' . $code . ':' . $autoplay . '" frameborder="0" marginheight="0" marginwidth="0" frameborder="0" allowtransparency="true" webkitAllowFullScreen="true" mozallowfullscreen="true" allowFullScreen="true"></iframe>';

		}

		// -------------------------- END IFRAME PLAYERS SETUPS BELOW ------------------------------ //


       // ------------------------------ VIDEO JS PLAYERS BELOW --------------------------- //

       /*
		* Run the media element video supports VIDEO JS streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_videojs_video_progressive($atts){

	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
	        	'playbackrates' => 'false',
	        	'floatit' => 'false',
	        	'buttons'    => 'false',
	        	'fullscreen' => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => '',
				'comments'   => '',
				'advert_link'   => 'https://s3bubble.com',
				'disable_skip'  => 'false',
				'vpaid_url'  => 'false'
			), $atts, 's3bubbleVideoSingleJs' ) );
            
            // Set video globals and calls
	        $player_id = uniqid();
	        $post_id   = get_the_ID();
 			$aspect    = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay  = ((empty($autoplay)) ? 'false' : $autoplay);
			$buttons   = ((empty($buttons)) ? 'false' : $buttons);
			$playbackrates  = ((empty($playbackrates)) ? 'false' : $playbackrates);
			$floatit  = ((empty($floatit)) ? 'false' : $floatit);
			$advert_link = ((empty($advert_link)) ? 'https://s3bubble.com' : $advert_link);
			$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
			$poster = "false";
			if(get_option("s3-thumbs") === "true"){
				if(has_post_thumbnail()){
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 's3bubble-single-video-poster' );
					$poster = $thumbnail[0];
				}
			}
			$resume = false;
			$commentsOutput = '';
			if($comments == 'facebook'){
				$commentsOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=803844463017959";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-comments" data-href="' . get_permalink( $post_id ) . '" data-num-posts="5"></div>';
			}

			// Set the vpaid url
			$vpswf = plugins_url('dist/tools/VPAIDFlash.swf',__FILE__ );

			$params = json_encode(array(
						'Poster' =>        $poster,
						'Pid' =>		   $player_id,
						'PostID' =>        $post_id,
						'Bucket' =>		   $bucket,
						'Key' =>		   $track,
						'Cloudfront' =>	   $cloudfront,
						'AutoPlay' =>	   $autoplay,
						'PlaybackRates' => $playbackrates,
						'FloatIt'       => $floatit,
						'Buttons' =>       "[" . $buttons . "]",
						'Aspect' =>	       $aspect,
						'Resume' =>	       $resume,
						'AdvertLink' =>    $advert_link,
						'DisableSkip' =>   $disable_skip,
						'VPaid' => $vpaid_url,
						'VPAIDFlash' => $vpswf
					));

			return "<div class='s3bubble-videojs-progressive-video' id='s3bubble-videojs-progressive-{$player_id}' data-params='" . $params . "'></div>{$commentsOutput}";			
			
       }

       /*
		* Run the media element video supports HLS streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_videojs_video_hls($atts){
	   	
	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
	        	'playbackrates' => 'false',
	        	'floatit' => 'false',
	        	'buttons'    => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => '',
				'comments'   => '',
				'advert_link'   => 'https://s3bubble.com',
				'disable_skip'  => 'false',
				'vpaid_url'  => 'false'
			), $atts, 's3bubbleHlsVideo' ) );

			// Set video globals and calls
	        $player_id = uniqid();
	        $post_id   = get_the_ID();
			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$buttons   = ((empty($buttons)) ? 'false' : $buttons);
			$playbackrates  = ((empty($playbackrates)) ? 'false' : $playbackrates);
			$floatit  = ((empty($floatit)) ? 'false' : $floatit);
			$advert_link = ((empty($advert_link)) ? 'https://s3bubble.com' : $advert_link);
			$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
			$vpaid_url = ((empty($vpaid_url)) ? 'false' : $vpaid_url);
			$poster = "false";
			if(get_option("s3-thumbs") === "true"){
				if(has_post_thumbnail()){
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 's3bubble-single-video-poster' );
					$poster = $thumbnail[0];
				}
			}
			$resume = true;
			$commentsOutput = '';
			if($comments == 'facebook'){
				$commentsOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=803844463017959";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-comments" data-href="' . get_permalink( $post_id ) . '" data-num-posts="5"></div>';
			}

			$vpswf = plugins_url('dist/tools/VPAIDFlash.swf',__FILE__ );

			$params = json_encode(array(
						'Poster' =>      $poster,
						'Pid' =>		 $player_id,
						'PostID' =>      $post_id,
						'Bucket' =>		 $bucket,
						'Key' =>		 $track,
						'Cloudfront' =>	 $cloudfront,
						'AutoPlay' =>	 $autoplay,
						'PlaybackRates' => $playbackrates,
						'FloatIt'       => $floatit,
						'Buttons' =>     "[" . $buttons . "]",
						'Aspect' =>	     $aspect,
						'Resume' =>	     $resume,
						'AdvertLink' =>  $advert_link,
						'DisableSkip' => $disable_skip,
						'VPaid' => $vpaid_url,
						'VPAIDFlash' => $vpswf
					));

			return "<div class='s3bubble-videojs-hls-video' id='single-videojs-hls-{$player_id}' data-params='" . $params . "'></div>{$commentsOutput}";		
			
       }


       /*
		* Run the VIDEO JS video supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_videojs_video_rtmp($atts){
	   	
	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
	        	'floatit' => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => '',
				'comments'   => '',
				'fallback'   => 'true',
				'advert_link'   => 'https://s3bubble.com',
				'disable_skip'  => 'false'
			), $atts, 's3bubbleRtmpVideoJs' ) );

			// Set video globals and calls
	        $player_id = uniqid();
	        $post_id   = get_the_ID();
			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$floatit  = ((empty($floatit)) ? 'false' : $floatit);
			$fallback = ((empty($fallback)) ? 'true' : $fallback);
			$advert_link = ((empty($advert_link)) ? 'https://s3bubble.com' : $advert_link);
			$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
			$poster = "false";
			if(get_option("s3-thumbs") === "true"){
				if(has_post_thumbnail()){
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 's3bubble-single-video-poster' );
					$poster = $thumbnail[0];
				}
			}
			$resume = false;

			$swf = plugins_url('dist/tools/video-js.swf',__FILE__ );

			$commentsOutput = '';
			if($comments == 'facebook'){
				$commentsOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=803844463017959";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-comments" data-href="' . get_permalink( $post_id ) . '" data-num-posts="5"></div>';
			}

			$params = json_encode(array(
						'Poster' =>      $poster,
						'Pid' => 		 $player_id,
						'PostID' =>      $post_id ,
						'Bucket' => 	 $bucket,
						'Key' => 		 $track,
						'Cloudfront' =>  $cloudfront,
						'AutoPlay' => 	 $autoplay,
						'FloatIt'       => $floatit,
						'Aspect' => 	 $aspect,
						'Resume' => 	 $resume,
						'Fallback' => 	 $fallback,
						'AdvertLink' =>  $advert_link,
						'DisableSkip' => $disable_skip,
						'Flash'    =>    $swf
					));

			return "<div class='s3bubble-videojs-rtmp-video' id='single-videojs-rtmp-{$player_id}' data-params='" . $params . "'></div>{$commentsOutput}";				
			
       }

       /*
		* Run the video js supports Live Streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_videojs_video_broadcasting($atts){

	        extract( shortcode_atts( array(
				'aspect'     => '16:9',
				'autoplay'   => 'false',
				'comments' => 'false',
				'poster' => '',
				'rtmp'      => '',
				'stream'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleLiveStream' ) );

			$player_id = uniqid();
			$post_id   = get_the_ID();
			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);

			if(has_post_thumbnail()){
				$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 's3bubble-single-video-poster' );
				$poster = $thumbnail[0];
			}else{
				$poster = 'https://s3.amazonaws.com/s3bubble.assets/video.player/placeholder.png';
			}

			$commentsOutput = '';
			if($comments == 'facebook'){
				$commentsOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=803844463017959";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-comments" data-href="' . get_permalink( $post_id ) . '" data-num-posts="5"></div>';
			}

			if(empty($stream)){
				echo "No live steam url has been set";
			}else{
                
                $params = json_encode(array(
						'Poster' =>     $poster,
						'Pid' =>		$player_id,
						'Rtmp' =>		$rtmp,
						'Stream' =>		$stream,
						'AutoPlay' =>	$autoplay,
						'Aspect' =>	    $aspect
					));

				return "<div class='s3bubble-videojs-broadcasting-video' id='single-videojs-broadcasting-{$player_id}' data-params='" . $params . "'></div>{$commentsOutput}";

			} 
			
        }

        /*
		* Run the VIDEO JS video playlist
		* @author sameast
		* @none
		*/ 
	   function s3bubble_videojs_video_playlist($atts){
	   	
	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
	        	'floatit' => 'false',
				'playlist'   => '',
				'height'     => '',
				'count'      => '4',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => '',
				'comments'   => '',
				'fallback'   => 'true',
				'advert_link'   => 'https://s3bubble.com',
				'disable_skip'  => 'false'
			), $atts, 's3bubbleVideoJsPlaylist' ) );

			// Set video globals and calls
	        $player_id = uniqid();
	        $post_id   = get_the_ID();
			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$floatit  = ((empty($floatit)) ? 'false' : $floatit);
			$fallback = ((empty($fallback)) ? 'true' : $fallback);
			$count   = ((empty($count)) ? '4' : $count);
			$advert_link = ((empty($advert_link)) ? 'https://s3bubble.com' : $advert_link);
			$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
			$poster = "false";
			if(get_option("s3-thumbs") === "true"){
				if(has_post_thumbnail()){
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 's3bubble-single-video-poster' );
					$poster = $thumbnail[0];
				}
			}
			$resume = false;
			$swf = plugins_url('dist/tools/video-js.swf',__FILE__ );

			$commentsOutput = '';
			if($comments == 'facebook'){
				$commentsOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=803844463017959";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-comments" data-href="' . get_permalink( $post_id ) . '" data-num-posts="5"></div>';
			}

			$params = json_encode(array(
						'Poster' =>      $poster,
						'Pid' => 		 $player_id,
						'PostID' =>      $post_id ,
						'Bucket' => 	 $bucket,
						'Folder' => 	 $folder,
						'Count' => 	     $count,
						'Key' => 		 $track,
						'Cloudfront' =>  $cloudfront,
						'AutoPlay' => 	 $autoplay,
						'FloatIt'     => $floatit,
						'Aspect' => 	 $aspect,
						'Resume' => 	 $resume,
						'Fallback' => 	 $fallback,
						'AdvertLink' =>  $advert_link,
						'DisableSkip' => $disable_skip,
						'Flash'    =>    $swf
					));

			return "<div class='s3bubble-videojs-playlist-video' id='playlist-videojs-video-{$player_id}' data-params='" . $params . "'></div>{$commentsOutput}";				
			
       }

        /*
		* Run the media element video supports HLS streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_videojs_audio_hls($atts){

	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => '',
				'comments'   => '',
				'advert_link'   => 'https://s3bubble.com',
				'disable_skip'  => 'false'
			), $atts, 's3bubbleHlsAudioDefault' ) );

			// Set video globals and calls
	        $player_id = uniqid();
			$background	= stripcslashes(get_option("s3bubble_video_all_bar_seeks"));
	        $post_id   = get_the_ID();
			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$advert_link = ((empty($advert_link)) ? 'https://s3bubble.com' : $advert_link);
			$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
			$poster = "false";
			if(get_option("s3-thumbs") === "true"){
				if(has_post_thumbnail()){
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 's3bubble-single-video-poster' );
					$poster = $thumbnail[0];
				}
			}
			$resume = false;

			$commentsOutput = '';
			if($comments == 'facebook'){
				$commentsOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=803844463017959";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-comments" data-href="' . get_permalink( $post_id ) . '" data-num-posts="5"></div>';
			}

			$params = json_encode(array(
						'Poster' =>      $poster,
						'Pid' =>		 $player_id,
						'PostID' =>      $post_id,
						'Bucket' =>		 $bucket,
						'Key' =>		 $track,
						'Cloudfront' =>	 $cloudfront,
						'AutoPlay' =>	 $autoplay,
						'Aspect' =>	     $aspect,
						'Resume' =>	     $resume,
						'AdvertLink' =>  $advert_link,
						'DisableSkip' => $disable_skip,
						'WaveformColour' =>  $background
					));

			return "<div class='s3bubble-audiojs-hls-video' id='single-audiojs-hls-{$player_id}' data-params='" . $params . "'></div>{$commentsOutput}";		
			
       }

        /*
		* Run the video js single audio
		* @author sameast
		* @none
		*/ 
	   function s3bubble_videojs_audio_progressive($atts){

	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => '',
				'comments'   => '',
				'advert_link'   => 'https://s3bubble.com',
				'disable_skip'  => 'false'
			), $atts, 's3bubbleAudioSingleVideoJs' ) );

			// Set video globals and calls
	        $player_id = uniqid();
			$background	= stripcslashes(get_option("s3bubble_video_all_bar_seeks"));
	        $post_id   = get_the_ID();
			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$advert_link = ((empty($advert_link)) ? 'https://s3bubble.com' : $advert_link);
			$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
			$poster = "false";
			if(get_option("s3-thumbs") === "true"){
				if(has_post_thumbnail()){
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 's3bubble-single-video-poster' );
					$poster = $thumbnail[0];
				}
			}
			$resume = false;

			$commentsOutput = '';
			if($comments == 'facebook'){
				$commentsOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=803844463017959";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-comments" data-href="' . get_permalink( $post_id ) . '" data-num-posts="5"></div>';
			}

			$params = json_encode(array(
						'Poster' =>      $poster,
						'Pid' =>		 $player_id,
						'PostID' =>      $post_id,
						'Bucket' =>		 $bucket,
						'Key' =>		 $track,
						'Cloudfront' =>	 $cloudfront,
						'AutoPlay' =>	 $autoplay,
						'Aspect' =>	     $aspect,
						'Resume' =>	     $resume,
						'AdvertLink' =>  $advert_link,
						'DisableSkip' => $disable_skip,
						'WaveformColour' =>  $background
					));

			return "<div class='s3bubble-audiojs-progressive-video' id='single-audiojs-progressive-{$player_id}' data-params='" . $params . "'></div>{$commentsOutput}";		
			
       }
       

		// ------------------------------ JPLAYER PLAYERS BELOW --------------------------- //

		/*
		* Run the s3bubble jplayer single video function
		* @author sameast
		* @none
		*/ 
		function s3bubble_jplayer_video_progressive($atts){
			
			//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$responsive          = get_option("s3-responsive");
			$stream              = get_option("s3-stream");
			$s3bubble_force_download = get_option("s3bubble_force_download");

	        extract( shortcode_atts( array(
	        	'download'   => 'false',
	        	'twitter' => 'false',
	        	'twitter_handler' => '@s3bubble',
	        	'twitter_text' => 'Shared via s3bubble.com media streaming',
				'aspect'     => '16:9',
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'advert_link'       => 'https://s3bubble.com',
				'disable_skip' => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3bubbleVideoSingle' ) );
			extract( shortcode_atts( array(
				'download'   => 'false',
				'twitter' => 'false',
				'twitter_handler' => 's3bubble',
	        	'twitter_text' => 'Shared via s3bubble.com media streaming',
				'aspect'     => '16:9',
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'advert_link'   => 'https://s3bubble.com',
				'disable_skip'  => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3videoSingle' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$twitter   = ((empty($twitter)) ? 'false' : $twitter);
			$advert_link = ((empty($advert_link)) ? 'https://s3bubble.com' : $advert_link);
			$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$start = ((empty($start)) ? 'false' : $start);
			$finish = ((empty($finish)) ? 'false' : $finish);
			
			// Check download
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 1;
				}else{
					if($download == 'true'){
						$download = 1;
					}else{
						$download = 0;
					}
				}
			}

			// Force download
			if($s3bubble_force_download == 'true'){
				$download = 1;
			}
			
            $player_id = uniqid();

            $params = json_encode(array(
						'Flash' =>      "https://s3.amazonaws.com/s3bubble.assets/flash/s3bubble.rtmp.swf",
						'Supplied' =>   "m4v",
						'Pid' =>		$player_id,
						'Bucket' =>		$bucket,
						'Key' =>		$track,
						'Cloudfront' =>	$cloudfront,
						'Security' =>	$ajax_nonce,
						'AutoPlay' =>	$autoplay,
						'Download' =>	$download,
						'Aspect' =>	    $aspect,
						'AdvertLink' =>$advert_link,
						'DisableSkip' =>$disable_skip,
						'Twitter' =>    $twitter,
						'TwitterText' =>    $twitter_text,
						'TwitterHandler' =>	$twitter_handler,
						'Start' =>      $start,
						'Finish' =>	    $finish
					));

			return "<div class='s3bubble-jplayer-progressive-video' id='single-jplayer-progressive-{$player_id}' data-params='" . $params . "'></div>";				

		}

		/*
		* Run the s3bubble jplayer single video function
		* @author sameast
		* @none
		*/ 
		function s3bubble_jplayer_video_lightbox_progressive($atts, $content = null){
				
				//Run a S3Bubble security check
				$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );
				$loggedin            = get_option("s3-loggedin");
				$search              = get_option("s3-search");
				$responsive          = get_option("s3-responsive");
				$stream              = get_option("s3-stream");
				$s3bubble_force_download = get_option("s3bubble_force_download");

		        extract( shortcode_atts( array(
		        	'download'   => 'false',
		        	'twitter' => 'false',
		        	'twitter_handler' => '@s3bubble',
		        	'twitter_text' => 'Shared via s3bubble.com media streaming',
					'aspect'     => '16:9',
					'text'     => 'S3Bubble Video',
					'responsive' => $responsive,
					'autoplay'   => 'false',
					'start'      => 'false',
					'finish'     => 'false',
					'disable_skip'     => 'false',
					'playlist'   => '',
					'height'     => '',
					'track'      => '',
					'bucket'     => '',
					'folder'     => '',
					'cloudfront' => ''
				), $atts, 's3bubbleVideoSingle' ) );
				extract( shortcode_atts( array(
					'download'   => 'false',
					'twitter' => 'false',
					'twitter_handler' => 's3bubble',
		        	'twitter_text' => 'Shared via s3bubble.com media streaming',
					'aspect'     => '16:9',
					'text'     => 'S3Bubble Video',
					'responsive' => $responsive,
					'autoplay'   => 'false',
					'start'      => 'false',
					'finish'     => 'false',
					'disable_skip'     => 'false',
					'playlist'   => '',
					'height'     => '',
					'track'      => '',
					'bucket'     => '',
					'folder'     => '',
					'cloudfront' => ''
				), $atts, 's3videoSingle' ) );

				$aspect       = ((empty($aspect)) ? '16:9' : $aspect);
				$twitter   = ((empty($twitter)) ? 'false' : $twitter);
				$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
				$link_text    = ((empty($text)) ? 'S3Bubble Video' : $text);
				$download     = ((empty($download)) ? 'false' : $download);
				$autoplay     = ((empty($autoplay)) ? 'false' : $autoplay);
				$start        = ((empty($start)) ? 'false' : $start);
				$finish       = ((empty($finish)) ? 'false' : $finish);
				$resume       = false;
				
				// Check download
				if($loggedin == 'true'){
					if ( is_user_logged_in() ) {
						$download = 1;
					}else{
						if($download == 'true'){
							$download = 1;
						}else{
							$download = 0;
						}
					}
				}

				// Force download
				if($s3bubble_force_download == 'true'){
					$download = 1;
				}
				
	            $player_id = uniqid();

	            if(empty($content)){
	            	$content = $link_text;
                }
			
	            return "<a class='s3bubble-popup-link-{$player_id}' href='#s3bubble-popup-{$player_id}'>{$content}</a>
	            <script type='text/javascript'>
					jQuery(document).ready(function($) {
						var fireOnce = true;
						$('.s3bubble-popup-link-{$player_id}').magnificPopup({
							items: {
     							src: '<div class=\"s3bubble-popup-styles\"><div id=\"s3bubble-videojs-progressive-{$player_id}\"><p class=\"s3bubble-lightbox-loading\">Loading video...</p></div></div>',
						    	type: 'inline'
  							},
  							callbacks: {
					            elementParse: function(item){
					            	$('#s3bubble-videojs-progressive-{$player_id}').singleVideoJs({
										Poster:     '{$poster}',
										Pid:		'{$player_id}',
										PostID:     '{$post_id}',
										Bucket:		'{$bucket}',
										Key:		'{$track}',
										Cloudfront:	'{$cloudfront}',
										AutoPlay:	{$autoplay},
										Aspect:	    '{$aspect}',
										Resume:	    '{$resume}'
									},function(){
										
									});
					            },
							    close: function() {
							        var oldPlayer = document.getElementById('video-{$player_id}');
									videojs(oldPlayer).dispose();
							    }
					        }
						});
					});
					jQuery( window ).on('beforeunload',function() {
						addListener(window.s3bubbleAnalytics);
					});
				</script>";

		}

		/*
		* Run the s3bubble jplayer single video function
		* @author sameast
		* @none
		*/ 
		function s3bubble_lightbox_video_old($atts){
				
			//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );

			// get option from database	
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$responsive          = get_option("s3-responsive");
			$stream              = get_option("s3-stream");
			$s3bubble_force_download = get_option("s3bubble_force_download");

	        extract( shortcode_atts( array(
	        	'download'   => 'false',
	        	'twitter' => 'false',
	        	'twitter_handler' => '@s3bubble',
	        	'twitter_text' => 'Shared via s3bubble.com media streaming',
				'aspect'     => '16:9',
				'text'     => 'S3Bubble Video',
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'disable_skip'     => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3bubbleVideoSingle' ) );
			extract( shortcode_atts( array(
				'download'   => 'false',
				'twitter' => 'false',
				'twitter_handler' => 's3bubble',
	        	'twitter_text' => 'Shared via s3bubble.com media streaming',
				'aspect'     => '16:9',
				'text'     => 'S3Bubble Video',
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'disable_skip'     => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3videoSingle' ) );

			$aspect       = ((empty($aspect)) ? '16:9' : $aspect);
			$twitter   = ((empty($twitter)) ? 'false' : $twitter);
			$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
			$link_text    = ((empty($text)) ? 'S3Bubble Video' : $text);
			$download     = ((empty($download)) ? 'false' : $download);
			$autoplay     = ((empty($autoplay)) ? 'false' : $autoplay);
			$start        = ((empty($start)) ? 'false' : $start);
			$finish       = ((empty($finish)) ? 'false' : $finish);
			
			// Check download
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 1;
				}else{
					if($download == 'true'){
						$download = 1;
					}else{
						$download = 0;
					}
				}
			}

			// Force download
			if($s3bubble_force_download == 'true'){
				$download = 1;
			}
			
            $player_id = uniqid();
		
            return '<a class="s3bubble-popup-link-' . $player_id . '" href="#s3bubble-popup-' . $player_id . '">' . $link_text . '</a>
            <script type="text/javascript">
				jQuery(document).ready(function($) {
					var fireOnce = true;
					$("body").append("<div id=\"s3bubble-popup-' . $player_id . '\" class=\"s3bubble-popup-styles\"><div class=\"single-video-' . $player_id . '\"></div></div>");
					$(".s3bubble-popup-link-' . $player_id . '").magnificPopup({
					  type:"inline",
					  callbacks: {
				            elementParse: function(item){
					            if(fireOnce){
					                $(".single-video-' . $player_id . '").singleVideo({
										Ajax:       "' . admin_url('admin-ajax.php') . '",
										ApiCall:    "s3bubble_video_single_internal_ajax",
										Flash:      "https://s3.amazonaws.com/s3bubble.assets/flash/s3bubble.rtmp.swf",
										Supplied:   "m4v",
										Pid:		"' . $player_id . '",
										Bucket:		"' . $bucket . '",
										Key:		"' . $track . '",
										Cloudfront:	"' . $cloudfront . '",
										Security:	"' . $ajax_nonce . '",
										AutoPlay:	' . $autoplay . ',
										Download:	' . $download . ',
										Aspect:	    "' . $aspect . '",
										DisableSkip:"' . $disable_skip . '",
										Twitter:    "' . $twitter . '",
										TwitterText:    "' . $twitter_text . '",
										TwitterHandler:	"' . $twitter_handler . '",
										Start:      "' . $start . '",
										Finish:	    "' . $finish . '"
									},function(){
										
									});
									fireOnce = false;
								}
				            }
				        }
					});
				});
				jQuery( window ).on("beforeunload",function() {
					addListener(window.s3bubbleAnalytics);
				});
			</script>';

		}


		/*
		* Run the jplayer supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_jplayer_video_rtmp($atts){
	   		
	   		//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );
			$responsive          = get_option("s3-responsive");

	        extract( shortcode_atts( array(
	        	'download'   => 'false',
	        	'twitter' => 'false',
	        	'twitter_handler' => '@s3bubble',
	        	'twitter_text' => 'Shared via s3bubble.com media streaming',
				'aspect'     => '16:9',
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'disable_skip' => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3bubbleRtmpVideoDefault' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$disable_skip = ((empty($disable_skip)) ? 'false' : $disable_skip);
			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$start = ((empty($start)) ? 'false' : $start);
			$finish = ((empty($finish)) ? 'false' : $finish);

            $player_id = uniqid();

            $params = json_encode(array(
						'Flash' =>      "https://s3.amazonaws.com/s3bubble.assets/flash/s3bubble.rtmp.swf",
						'Pid' =>		$player_id,
						'Bucket' =>		$bucket,
						'Key' =>		$track,
						'Cloudfront' =>	$cloudfront,
						'Supplied' =>   "rtmpv",
						'Security' =>	$ajax_nonce,
						'AutoPlay' =>	$autoplay,
						'Download' =>	$download,
						'Aspect' =>	    $aspect,
						'DisableSkip' =>$disable_skip,
						'Twitter' =>    $twitter,
						'TwitterText' =>    $twitter_text,
						'TwitterHandler' =>	$twitter_handler,
						'Start' =>      $start,
						'Finish' =>	    $finish
					));

			return "<div class='s3bubble-jplayer-rtmp-video' id='single-jplayer-rtmp-{$player_id}' data-params='" . $params . "'></div>";				

       }


       /*
		* Run the s3bubble jplayer video playlist function
		* @author sameast
		* @none
		*/ 
        function s3bubble_jplayer_video_playlist_progressive($atts){
	        
	        //Run a S3Bubble security check

			$loggedin           = get_option("s3-loggedin");
			$search             = get_option("s3-search");
			$responsive         = get_option("s3-responsive");
			$s3bubble_force_download = get_option("s3bubble_force_download");

        	extract( shortcode_atts( array(
				'playlist'   => 'show',
				'download'   => 'false',
				'aspect'     => '16:9',
				'search'     => $search,
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'order'      => 'asc',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3bubbleVideo' ) );
			extract( shortcode_atts( array(
				'playlist'   => 'show',
				'download'   => 'false',
				'aspect'     => '16:9',
				'search'     => $search,
				'responsive' => $responsive,
				'autoplay'   => 'false',
				'order'      => 'asc',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3video' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$playlist = ((empty($playlist)) ? 'show' : $playlist);
			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			
			// Check download
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 1;
				}else{
					if($download == 'true'){
						$download = 1;
					}else{
						$download = 0;
					}
				}
			}

			// Force download
			if($s3bubble_force_download == 'true'){
				$download = 1;
			}

            $player_id = uniqid();

            $params = json_encode(array(
						'Pid' =>		$player_id,
						'Bucket' =>		$bucket,
						'Folder' =>		$folder,
						'Cloudfront' =>	$cloudfront,
						'AutoPlay' =>	$autoplay,
						'Download' =>	$download,
						'Aspect' =>	    $aspect,
						'Height' =>    $height,
						'Playlist' =>  (($playlist == 'show') ? "" : "display:none;" )
					));

			return "<div class='s3bubble-jplayer-progressive-video-playlist' id='s3bubble-jplayer-progressive-playlist-{$player_id}' data-params='" . $params . "'></div>";

		}

		 /*
		* Run the s3bubble jplayer single audio function
		* @author sameast
		* @none
		*/ 
		function s3bubble_jplayer_audio_progressive($atts){

			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$s3bubble_force_download = get_option("s3bubble_force_download");

			extract( shortcode_atts( array(
				'style'      => 'bar',
				'download'   => 'false',
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'preload'    => 'auto',
				'bucket'     => '',
				'track'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleAudioSingle' ) );
			extract( shortcode_atts( array(
				'style'      => 'bar',
				'download'   => 'false',
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'preload'    => 'auto',
				'bucket'     => '',
				'track'      => '',
				'cloudfront' => ''
			), $atts, 's3audibleSingle' ) );

			$style    = ((empty($style)) ? 'bar' : $style);
			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$preload  = ((empty($preload)) ? 'auto' : $preload);
			$start = ((empty($start)) ? 'false' : $start);
			$finish = ((empty($finish)) ? 'false' : $finish);
			
			// Check download
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 1;
				}else{
					if($download == 'true'){
						$download = 1;
					}else{
						$download = 0;
					}
				}
			}

			// Force download
			if($s3bubble_force_download == 'true'){
				$download = 1;
			}

            $player_id = uniqid();

            $params = json_encode(array(
						'Pid' =>		$player_id,
						'Bucket' =>		$bucket,
						'Key' =>		$track,
						'Cloudfront' =>	$cloudfront,
						'AutoPlay' =>	$autoplay,
						'Download' =>	$download,
						'Styles' =>      $style,
						'Start' =>      $start,
						'Finish' =>	    $finish
					));

            return "<div class='s3bubble-jplayer-progressive-audio-single' id='s3bubble-jplayer-progressive-audio-single-{$player_id}' data-params='" . $params . "'></div>";

		}

		/*
		* Run the jplayer supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_jplayer_audio_rtmp($atts){
	
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");

			 extract( shortcode_atts( array(
				'style'      => 'bar',
				'download'   => 'false',
				'autoplay'   => 'false',
				'preload'    => 'auto',
				'bucket'     => '',
				'track'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleRtmpAudioDefault' ) );
			extract( shortcode_atts( array(
				'style'      => 'bar',
				'download'   => 'false',
				'autoplay'   => 'false',
				'preload'    => 'auto',
				'bucket'     => '',
				'track'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleRtmpAudioDefault' ) );

			$style    = ((empty($style)) ? 'bar' : $style);
			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$preload  = ((empty($preload)) ? 'auto' : $preload);
			
			// Check download
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 1;
				}else{
					if($download == 'true'){
						$download = 1;
					}else{
						$download = 0;
					}
				}
			}
            $player_id = uniqid();

            return '<div class="single-audio-' . $player_id . '"></div>
            <script type="text/javascript">
				jQuery(document).ready(function($) {
					$(".single-audio-' . $player_id . '").singleAudioRtmp({
						Pid:		"' . $player_id . '",
						Bucket:		"' . $bucket . '",
						Key:		"' . $track . '",
						Cloudfront:	"' . $cloudfront . '",
						AutoPlay:	' . $autoplay . ',
						Download:	' . $download . ',
						Styles:      "' . $style . '",
						Start:      "' . $start . '",
						Finish:	    "' . $finish . '"
					},function(){
						
					});
				});
				jQuery( window ).on("beforeunload",function() {
					addListener(window.s3bubbleAnalytics);
				});
			</script>';

       }

		 /*
		* Run the s3bubble jplayer playlist function
		* @author sameast
		* @none
		*/ 
	   function s3bubble_jplayer_audio_playlist_progressive($atts){
	   	  	
	   	  	//Run a S3Bubble security check
			$ajax_nonce = wp_create_nonce( "s3bubble-nonce-security" );

			/*
			 * player options
			 */ 		
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$s3bubble_force_download = get_option("s3bubble_force_download");

	        extract( shortcode_atts( array(
				'playlist'   => 'show',
				'order'      => 'asc',
				'download'   => 'false',
				'search'     => $search,
				'autoplay'   => 'false',
				'preload'   => 'auto',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3bubbleAudio' ) );
			extract( shortcode_atts( array(
				'playlist'   => 'show',
				'order'      => 'asc',
				'download'   => 'false',
				'search'     => $search,
				'autoplay'   => 'false',
				'preload'   => 'auto',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => ''
			), $atts, 's3audible' ) );

			$playlist = ((empty($playlist)) ? 'show' : $playlist);
			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$preload  = ((empty($preload)) ? 'auto' : $preload);
			
			// Check download
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 1;
				}else{
					if($download == 'true'){
						$download = 1;
					}else{
						$download = 0;
					}
				}
			}

			// Force download
			if($s3bubble_force_download == 'true'){
				$download = 1;
			}

            $player_id = uniqid();

            $params = json_encode(array(
						'Pid' =>		$player_id,
						'Bucket' =>		$bucket,
						'Folder' =>		$folder,
						'Cloudfront' =>	$cloudfront,
						'Security' =>	$ajax_nonce,
						'AutoPlay' =>	$autoplay,
						'Download' =>	$download,
						'Preload' =>	$preload,
						'Height' =>    $height,
						'Playlist' =>  (($playlist == 'hidden') ? 'none' : 'block' )
					));

			return "<div class='s3bubble-jplayer-progressive-audio-playlist' id='s3bubble-jplayer-progressive-audio-playlist-{$player_id}' data-params='" . $params . "'></div>";

        }

		// ------------------------------ END JPLAYER PLAYERS BELOW --------------------------- //


		// ------------------------------ MEDIA JS PLAYERS BELOW --------------------------- //

		/*
		* Run the media element video supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_mediajs_video_progressive($atts){

	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleMediaElementVideo' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$poster = "false";
			if(get_option("s3-thumbs") === "true"){
				if(has_post_thumbnail()){
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 's3bubble-single-video-poster' );
					$poster = $thumbnail[0];
				}
			}

			$player_id = uniqid();

			$params = json_encode(array(
						'Poster' =>     $poster,
						'Pid' =>		$player_id,
						'Bucket' =>		$bucket,
						'Key' =>		$track,
						'Cloudfront' =>	$cloudfront,
						'AutoPlay' =>	$autoplay,
						'Aspect' =>	    $aspect
					));

			return "<div class='s3bubble-mediajs-progressive-video-single' id='s3bubble-mediajs-progressive-video-single-{$player_id}' data-params='" . $params . "'></div>";	
			
       }

       /*
		* Run the media element video supports HLS streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_mediajs_video_hls($atts){

			extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleHlsVideo' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			if(get_option("s3-thumbs") === "true"){
				if(has_post_thumbnail()){
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 's3bubble-single-video-poster' );
					$poster = $thumbnail[0];
				}
			}
			$player_id = uniqid();
            
            $params = json_encode(array(
						'Poster' =>     $poster,
						'Pid' =>		$player_id,
						'Bucket' =>		$bucket,
						'Key' =>		$track,
						'Cloudfront' =>	$cloudfront,
						'AutoPlay' =>	$autoplay,
						'Aspect' =>	    $aspect
					));

			return "<div class='s3bubble-mediajs-hls-video-single' id='s3bubble-mediajs-hls-video-single-{$player_id}' data-params='" . $params . "'></div>";

			
       }

		 /*
		* Run the media element video supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_mediajs_video_rtmp($atts){

	        extract( shortcode_atts( array(
	        	'aspect'     => '16:9',
	        	'autoplay'   => 'false',
				'track'      => '',
				'bucket'     => '',
				'cloudfront' => ''
			), $atts, 's3bubbleRtmpVideo' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			if(get_option("s3-thumbs") === "true"){
				if(has_post_thumbnail()){
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 's3bubble-single-video-poster' );
					$poster = $thumbnail[0];
				}
			}
			$player_id = uniqid();
            
            $params = json_encode(array(
						'Poster' =>     $poster,
						'Pid' =>		$player_id,
						'Bucket' =>		$bucket,
						'Key' =>		$track,
						'Cloudfront' =>	$cloudfront,
						'AutoPlay' =>	$autoplay,
						'Aspect' =>	    $aspect
					));

			return "<div class='s3bubble-mediajs-rtmp-video-single' id='s3bubble-mediajs-rtmp-video-single-{$player_id}' data-params='" . $params . "' style='width:100%;overflow:hidden;'></div>";
			
       }


       /*
		* WORKING HERE LIVE STREAM - Main HLS and RTMP Live Streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_mediajs_video_broadcaster($atts){

	        extract( shortcode_atts( array(
				'aspect'     => '16:9',
				'autoplay'   => 'false',
				'comments' => 'false',
				'fblike'   => 'false',
				'rtmp'      => '',
				'stream'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleLiveStreamMedia' ) ); 

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$comments = ((empty($comments)) ? 'false' : $comments);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			
			$player_id = uniqid();

			//Split the key
			$path_parts = pathinfo($stream);

			if(isset($path_parts['extension']) && $path_parts['extension'] == 'm3u8'){

				// Setup secure url
				$secret = 'secret'; // To make the hash more difficult to reproduce.
				$path   = '/hls/' . $path_parts['filename'] . '.m3u8'; // This is the file to send to the user.
				$expire = time() + 3600; // At which point in time the file should expire. time() + x; would be the usual usage.
				$md5 = base64_encode(md5($secret .  $path . $expire , true)); // Using binary hashing.
				$md5 = strtr($md5, '+/', '-_'); // + and / are considered special characters in URLs, see the wikipedia page linked in references.
				$md5 = str_replace('=', '', $md5); // When used in query parameters the base64 padding character is considered special.
				$url = $stream . '?st=' . $md5 . '&e=' . time();

			}else{

				$url = $stream;

			}

			$poster = (has_post_thumbnail()) ? wp_get_attachment_url( get_post_thumbnail_id(get_the_ID())) : 'https://s3.amazonaws.com/s3bubble.assets/video.player/placeholder.png';
			
			$commentsOutput = '';
			if($comments == 'facebook'){
				$commentsOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=803844463017959";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-comments" data-href="' . get_permalink( get_the_ID() ) . '" data-num-posts="5"></div>';
			}

			$fblikeOutput = '';
			if($fblike == 'true'){
				$fblikeOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=495263607271128";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-like" data-href="' . get_permalink( $post_id ) . '" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>';
			}

			if(empty($stream) && empty($rtmp)){

				echo "No live steam url has been set";

			}else{

				$params = json_encode(array(
						'Poster' =>     $poster,
						'Pid' =>		$player_id,
						'Rtmp' =>		$rtmp,
						'Stream' =>		$stream,
						'AutoPlay' =>	$autoplay,
						'Aspect' =>	    $aspect
					));

				return "<div class='s3bubble-mediajs-broadcaster-video-single' id='s3bubble-mediajs-broadcaster-video-single-{$player_id}' data-params='" . $params . "'></div>";

			}
	
        }

        /*
		* WORKING HERE LIVE STREAM - Main HLS and RTMP Live Streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_mediajs_video_broadcaster_mobile_app($atts){

	        extract( shortcode_atts( array(
				'aspect'         => '16:9',
				'autoplay'       => 'false',
				'comments'       => 'false',
				'fblike'         => 'false',
				'offlinemessage' => 'This stream is currently offline',
				'ip'             => '',
				'stream'         => '',
				'cloudfront'     => ''
			), $atts, 's3bubbleMobileAppBroadcast' ) );

			$aspect   = ((empty($aspect)) ? '16:9' : $aspect);
			$comments = ((empty($comments)) ? 'false' : $comments);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$offlinemessage = ((empty($offlinemessage)) ? 'This stream is currently offline' : $offlinemessage);
			
			$player_id = uniqid();

			$poster = (has_post_thumbnail()) ? wp_get_attachment_url( get_post_thumbnail_id(get_the_ID())) : 'https://s3.amazonaws.com/s3bubble.assets/video.player/placeholder.png';
			
			$commentsOutput = '';
			if($comments == 'facebook'){
				$commentsOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=803844463017959";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-comments" data-href="' . get_permalink( get_the_ID() ) . '" data-num-posts="5"></div>';
			}

			$fblikeOutput = '';
			if($fblike == 'true'){
				$fblikeOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=495263607271128";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-like" data-href="' . get_permalink( $post_id ) . '" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>';
			}

			if(empty($stream)){

				echo "No live steam url has been set";

			}else{

				$params = json_encode(array(
						'Poster' =>     $poster,
						'Pid' =>		$player_id,
						'Stream' =>		$stream,
						'Ip' =>		    $ip,
						'AutoPlay' =>	$autoplay,
						'OfflineMessage' =>	$offlinemessage,
						'Aspect' =>	    $aspect
					));

				return "<div class='s3bubble-mediajs-broadcaster-video-mobile' id='s3bubble-mediajs-broadcaster-video-mobile-{$player_id}' data-params='" . $params . "'></div>";

			}
	
        }

        /*
		* Run the media element audio does not currently supports RTMP streaming
		* @author sameast
		* @none
		*/ 
	   function s3bubble_mediajs_audio_progressive($atts){
	   	
			$s3bubble_access_key = get_option("s3-s3audible_username");
			$s3bubble_secret_key = get_option("s3-s3audible_email");		
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$stream              = get_option("s3-stream");
	        extract( shortcode_atts( array(
	        	'autoplay'   => 'false',
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'style'      => '',
				'cloudfront' => ''
			), $atts, 's3bubbleMediaElementAudio' ) );

			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_audio_media_element';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Key' => $track
			);

			if(!function_exists('curl_init')){
    			echo json_encode(array("error" => "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>"));
    			exit();
    		}
			
			//open connection
			$ch = curl_init();
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//execute post
		    $result = curl_exec($ch);
			$track = json_decode($result, true);
			curl_close($ch);

			if(!empty($track['error'])){
				echo $track['error'];
			}else{
				$player_id = uniqid();
				if(is_array($track)){
					if($cloudfront != ''){
				    	return '<p>rtmp only currently support for video</p>';
				    }else{
						return '<audio width="100%" src="' . $track[0]['mp3'] . '" id="audio-' . $player_id . '"></audio>
								<script>
									jQuery(document).ready(function($) {
										var Bucket = "' . $bucket . '";
										var Key = "' . $track[0]['key'] . '";
										var Current = -1;
										$("#audio-' . $player_id . '").mediaelementplayer({
											videoVolume: "horizontal",
							    			features: ["playpause","current","progress","duration","tracks","volume","fullscreen"],
							    			plugins: ["flash","html"],
							    			success: function(mediaElement, node, player) {
							    				'. (($autoplay == 'true') ? 'mediaElement.play();' : '') . '
							    				// add event listener
										        mediaElement.addEventListener("play", function(e) {
										            if(Current < 0){
														addListener({
															app_id: s3bubble_all_object.s3appid,
															server: s3bubble_all_object.serveraddress,
															bucket: Bucket,
															key: Key,
															type: "audio",
															advert: false
														});
														Current = 1;
													}
										        }, false);
									     	}
						    			});
									});
								</script>';
					}
				}
			}
			
       }

       // ------------------------------ END MEDIA JS PLAYERS BELOW --------------------------- //

       // ------------------------------ SOUNDCLOUD PLAYERS BELOW --------------------------- //

		/*
		* Run the s3bubble jplayer single audio function
		* @author sameast
		* @none
		*/ 
		function s3bubble_waveform_playlist_player($atts){

			$loggedin = get_option("s3-loggedin");

			extract( shortcode_atts( array(
				'playlist'   => 'show',
				'order'      => 'asc',
				'download'   => 'false',
				'autoplay'   => 'false',
				'preload'   => 'auto',
				'height'     => '',
				'bucket'     => '',
				'folder'     => '',
				'download'   => 'false',
				'comments'   => 'false',
				'fblike'   => 'false',
				'autoplay'   => 'false',
				'brand'   => 'S3Bubble.com',
				'hex' => ''
			), $atts, 's3bubbleWaveform' ) );

			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'false' : $autoplay);
			$brand = ((empty($brand)) ? 'S3Bubble.com' : $brand);
			
			// Check download
			$hreftext = $brand;
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 'true';
					$hreftext = 'Download Audio';
				}else{
					if($download == 'true'){
						$download = 'true';
						$hreftext = 'Download Audio';
					}else{
						$download = 'false';
						$hreftext = 'Login To Download';
					}
				}
			}

			$player_id = uniqid();
			$post_id   = get_the_ID();
			
			$commentsOutput = '';
			if($comments == 'facebook'){
				$commentsOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=495263607271128";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-comments" data-href="' . get_permalink( $post_id ) . '" data-num-posts="5"></div>';
			}

			$fblikeOutput = '';
			if($fblike == 'true'){
				$fblikeOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=495263607271128";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-like" data-href="' . get_permalink( $post_id ) . '" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>';
			}

            return "<div id='ag1-{$player_id}' class='audiogallery skin-wave' style='opacity:0;'><div class='items'></div></div>
            {$commentsOutput}{$fblikeOutput}
		    <script type='text/javascript'>
		    	jQuery(document).ready(function($) {
					$('ag1-{$player_id}').s3bubbleWaveform({
						Pid:		'{$player_id}',
						Bucket:		'{$bucket}',
						Folder:		'{$folder}',
						AutoPlay:	'{$autoplay}',
						Download:	'{$download}',
						DownloadText:	'{$hreftext}',
						Brand:	'{$brand}'
					},function(){
						
					});
				});
		    </script>";

		}

       /*
		* Run the s3bubble jplayer single audio function
		* @author sameast
		* @none
		*/ 
		function s3bubble_waveform_single_player($atts){

			$loggedin = get_option("s3-loggedin");

			extract( shortcode_atts( array(
				'style'      => 'bar',
				'download'   => 'false',
				'autoplay'   => 'false',
				'start'      => 'false',
				'finish'     => 'false',
				'preload'    => 'auto',
				'bucket'     => '',
				'track'      => '',
				'cloudfront' => '',
				'comments'   => 'false',
				'fblike'   => 'false',
				'brand'   => 'S3Bubble.com',
				'hex' => ''
			), $atts, 's3bubbleWaveformSingle' ) );

			$download = ((empty($download)) ? 'false' : $download);
			$autoplay = ((empty($autoplay)) ? 'on' : $autoplay);
			$brand = ((empty($brand)) ? 'S3Bubble.com' : $brand);
			
			// Check download
			$hreftext = $brand;
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 'true';
					$hreftext = 'Download Audio';
				}else{
					if($download == 'true'){
						$download = 'true';
						$hreftext = 'Download Audio';
					}else{
						$download = 'false';
						$hreftext = 'Login To Download';
					}
				}
			}

			$player_id = uniqid();
			$post_id   = get_the_ID();

			$commentsOutput = '';
			if($comments == 'facebook'){
				$commentsOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=495263607271128";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-comments" data-href="' . get_permalink( $post_id ) . '" data-num-posts="5"></div>';
			}
            
            $fblikeOutput = '';
			if($fblike == 'true'){
				$fblikeOutput = '<div id="fb-root"></div><script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=495263607271128";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script>
							<div class="fb-like" data-href="' . get_permalink( $post_id ) . '" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>';
			}

            return "<div id='ag1-{$player_id}' class='audiogallery skin-wave' style='opacity:0;''><div class='items'></div></div>
            {$commentsOutput}{$fblikeOutput}
		    <script type='text/javascript'>
		    	jQuery(document).ready(function($) {
					$('ag1-{$player_id}').s3bubbleWaveformSingle({
						Pid:		'{$player_id}',
						Bucket:		'{$bucket}',
						Key:		'{$track}',
						AutoPlay:	'{$autoplay}',
						Download:	'{$download}',
						DownloadText:	'{$hreftext}',
						Brand:	'{$brand}'
					},function(){
						
					});
				});
		    </script>";

		}

		// ------------------------------ END SOUNDCLOUD PLAYERS BELOW --------------------------- //
    
    }

	/*
	* Initiate the class
	* @author sameast
	* @none
	*/ 
	$s3bubble_audio = new s3bubble_audio();
	
} //End Class S3Bubble