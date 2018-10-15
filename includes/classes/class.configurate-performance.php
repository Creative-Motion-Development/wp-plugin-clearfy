<?php
	/**
	 * This class configures the code cleanup settings
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */
	
	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}
	
	class WCL_ConfigPerformance extends Wbcr_FactoryClearfy000_Configurate {
		
		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			parent::__construct($plugin);
			
			$this->plugin = $plugin;
		}
		
		public function registerActionsAndFilters()
		{
			if( $this->getPopulateOption('disable_emoji') ) {
				add_action('init', array($this, 'disableEmojis'));
			}
			
			if( $this->getPopulateOption('remove_jquery_migrate') && !is_admin() ) {
				add_filter('wp_default_scripts', array($this, 'removeJqueryMigrate'));
			}
			
			if( $this->getPopulateOption('disable_embeds') ) {
				add_action('init', array($this, 'disableEmbeds'));
			}
			
			if( $this->getPopulateOption('disable_json_rest_api') ) {
				$this->removeRestApi();
			}
			
			if( !is_admin() ) {
				if( $this->getPopulateOption('disable_feed') ) {
					$this->disableFeed();
				}
				
				if( $this->getPopulateOption('disable_dashicons') ) {
					add_action('wp_print_styles', array($this, 'disableDashicons'), -1);
				}
				
				if( $this->getPopulateOption('remove_xfn_link') ) {
					add_action('wp_loaded', array($this, 'htmlCompressor'));
				}
				
				if( $this->getPopulateOption('remove_recent_comments_style') ) {
					add_action('widgets_init', array($this, 'removeRecentCommentsStyle'));
				}

				/**
				 * Priority set to 9999. Higher numbers correspond with later execution.
				 * Hook into the style loader and remove the version information.
				 */

				if( $this->getPopulateOption('remove_style_version') ) {
					add_filter('style_loader_src', array($this, 'hideWordpressVersionInScript'), 9999, 2);
				}

				/**
				 * Hook into the script loader and remove the version information.
				 */

				if( $this->getPopulateOption('remove_js_version') ) {
					add_filter('script_loader_src', array($this, 'hideWordpressVersionInScript'), 9999, 2);
				}
				
				$this->remove_tags_from_head();
			}
		}


		/**
		 * Remove wp version from any enqueued scripts
		 *
		 * @param string $target_url
		 * @return string
		 */
		public function hideWordpressVersionInScript($src, $handle)
		{
			if( is_user_logged_in() and $this->getPopulateOption('disable_remove_style_version_for_auth_users', false) ) {
				return $src;
			}
			$filename_arr = explode('?', basename($src));
			$exclude_file_list = $this->getPopulateOption('remove_version_exclude', '');
			$exclude_files_arr = array_map('trim', explode(PHP_EOL, $exclude_file_list));

			if( strpos($src, 'ver=') && !in_array(str_replace('?' . $filename_arr[1], '', $src), $exclude_files_arr, true) ) {
				$src = remove_query_arg('ver', $src);
			}

			return $src;
		}

		/**
		 * Disable dashicons for all but the auth user
		 */
		public function disableDashicons()
		{
			if( !is_admin_bar_showing() && !is_customize_preview() ) {
				wp_deregister_style('dashicons');
			}
		}

		/**
		 * Disable the emoji's
		 */
		public function disableEmojis()
		{
			remove_action('wp_head', 'print_emoji_detection_script', 7);
			remove_action('admin_print_scripts', 'print_emoji_detection_script');
			remove_action('wp_print_styles', 'print_emoji_styles');
			remove_action('admin_print_styles', 'print_emoji_styles');
			remove_filter('the_content_feed', 'wp_staticize_emoji');
			remove_filter('comment_text_rss', 'wp_staticize_emoji');
			remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
			add_filter('emoji_svg_url', '__return_false');
			add_filter('tiny_mce_plugins', array($this, 'disableEmojisTinymce'));
			add_filter('wp_resource_hints', array($this, 'disableEmojisRemoveDnsPrefetch'), 10, 2);
		}

		/**
		 * Filter function used to remove the tinymce emoji plugin.
		 *
		 * @param    array $plugins
		 * @return   array Difference betwen the two arrays
		 */
		function disableEmojisTinymce($plugins)
		{
			if( is_array($plugins) ) {
				return array_diff($plugins, array('wpemoji'));
			}

			return array();
		}

		/**
		 * Remove emoji CDN hostname from DNS prefetching hints.
		 *
		 * @param  array $urls URLs to print for resource hints.
		 * @param  string $relation_type The relation type the URLs are printed for.
		 * @return array Difference betwen the two arrays.
		 */
		function disableEmojisRemoveDnsPrefetch($urls, $relation_type)
		{

			if( 'dns-prefetch' == $relation_type ) {

				// Strip out any URLs referencing the WordPress.org emoji location
				$emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
				foreach($urls as $key => $url) {
					if( strpos($url, $emoji_svg_url_bit) !== false ) {
						unset($urls[$key]);
					}
				}
			}

			return $urls;
		}

		/**
		 * Disables the WP REST API for visitors not logged into WordPress.
		 */
		public function removeRestApi()
		{
			/*
				Disable REST API link in HTTP headers
				Link: <https://example.com/wp-json/>; rel="https://api.w.org/"
			*/
			remove_action('template_redirect', 'rest_output_link_header', 11);

			/*
				Disable REST API links in HTML <head>
				<link rel='https://api.w.org/' href='https://example.com/wp-json/' />
			*/
			remove_action('wp_head', 'rest_output_link_wp_head', 10);
			remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');

			/*
				Disable REST API
			*/
			if( version_compare(get_bloginfo('version'), '4.7', '>=') ) {
				add_filter('rest_authentication_errors', array($this, 'disableWpRestApi'));
			} else {
				// REST API 1.x
				add_filter('json_enabled', '__return_false');
				add_filter('json_jsonp_enabled', '__return_false');

				// REST API 2.x
				add_filter('rest_enabled', '__return_false');
				add_filter('rest_jsonp_enabled', '__return_false');
			}
		}

		public function disableWpRestApi($access)
		{
			if( !is_user_logged_in() ) {

				$message = apply_filters('disable_wp_rest_api_error', __('REST API restricted to authenticated users.', 'clearfy'));

				return new WP_Error('rest_login_required', $message, array('status' => rest_authorization_required_code()));
			}

			return $access;
		}


		// todo: не работает должным образом, проверить
		public function removeRecentCommentsStyle()
		{
			global $wp_widget_factory;
			
			$widget_recent_comments = isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])
				? $wp_widget_factory->widgets['WP_Widget_Recent_Comments']
				: null;
			
			if( !empty($widget_recent_comments) ) {
				remove_action('wp_head', array(
					$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
					'recent_comments_style'
				));
			}
		}
		
		/**
		 * Disable feeds
		 */
		public function disableFeed()
		{
			add_action('wp_loaded', array($this, 'removeFeedLinks'));
			add_action('template_redirect', array($this, 'filterFeeds'), 1);
			add_filter('bbp_request', array($this, 'filterBbpFeeds'), 9);
		}
		
		
		public function removeFeedLinks()
		{
			remove_action('wp_head', 'feed_links', 2);
			remove_action('wp_head', 'feed_links_extra', 3);
		}
		
		public function filterFeeds()
		{
			if( !is_feed() || is_404() ) {
				return;
			}
			
			$this->disabled_feed_behaviour();
		}

		public function disabled_feed_behaviour()
		{
			global $wp_rewrite, $wp_query;
			
			if( $this->getPopulateOption('disabled_feed_behaviour', 'redirect_301') == 'redirect_404' ) {
				$wp_query->is_feed = false;
				$wp_query->set_404();
				status_header(404);
				// Override the xml+rss header set by WP in send_headers
				header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
			} else {
				if( isset($_GET['feed']) ) {
					wp_redirect(esc_url_raw(remove_query_arg('feed')), 301);
					exit;
				}
				
				if( 'old' !== get_query_var('feed') ) {    // WP redirects these anyway, and removing the query var will confuse it thoroughly
					set_query_var('feed', '');
				}
				
				redirect_canonical();    // Let WP figure out the appropriate redirect URL.
				
				// Still here? redirect_canonical failed to redirect, probably because of a filter. Try the hard way.
				$struct = (!is_singular() && is_comment_feed())
					? $wp_rewrite->get_comment_feed_permastruct()
					: $wp_rewrite->get_feed_permastruct();
				
				$struct = preg_quote($struct, '#');
				$struct = str_replace('%feed%', '(\w+)?', $struct);
				$struct = preg_replace('#/+#', '/', $struct);
				$requested_url = (is_ssl()
						? 'https://'
						: 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				
				$new_url = preg_replace('#' . $struct . '/?$#', '', $requested_url);
				
				if( $new_url !== $requested_url ) {
					wp_redirect($new_url, 301);
					exit;
				}
			}
		}
		
		/**
		 * BBPress feed detection sourced from bbp_request_feed_trap() in BBPress Core.
		 *
		 * @param  array $query_vars
		 * @return array
		 */
		public function filterBbpFeeds($query_vars)
		{
			// Looking at a feed
			if( isset($query_vars['feed']) ) {
				
				// Forum/Topic/Reply Feed
				if( isset($query_vars['post_type']) ) {
					
					// Matched post type
					$post_type = false;
					$post_types = array();
					
					if( function_exists('bbp_get_forum_post_type') && function_exists('bbp_get_topic_post_type') && function_exists('bbp_get_reply_post_type') ) // Post types to check
					{
						$post_types = array(
							bbp_get_forum_post_type(),
							bbp_get_topic_post_type(),
							bbp_get_reply_post_type(),
						);
					}
					
					// Cast query vars as array outside of foreach loop
					$qv_array = (array)$query_vars['post_type'];
					
					// Check if this query is for a bbPress post type
					foreach($post_types as $bbp_pt) {
						if( in_array($bbp_pt, $qv_array, true) ) {
							$post_type = $bbp_pt;
							break;
						}
					}
					
					// Looking at a bbPress post type
					if( !empty($post_type) ) {
						$this->disabled_feed_behaviour();
					}
				}
			}
			
			// No feed so continue on
			return $query_vars;
		}
		
		/**
		 * Remove unused tags from head
		 */
		public function remove_tags_from_head()
		{
			/*if( $this->getPopulateOption('remove_dns_prefetch') ) {
				remove_action('wp_head', 'wp_resource_hints', 2);
			}*/
			
			if( $this->getPopulateOption('remove_rsd_link') ) {
				remove_action('wp_head', 'rsd_link');
			}
			
			if( $this->getPopulateOption('remove_wlw_link') ) {
				remove_action('wp_head', 'wlwmanifest_link');
			}
			
			if( $this->getPopulateOption('remove_adjacent_posts_link') ) {
				remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
				remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
			}
			
			if( $this->getPopulateOption('remove_shortlink_link') ) {
				remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
				remove_action('template_redirect', 'wp_shortlink_header', 11, 0);
			}

			if( $this->getPopulateOption('remove_xfn_link') ) {
				add_filter('avf_profile_head_tag', array($this, 'removeXfnLink'));
			}
		}

		/**
		 * For more information about XFN relationships and examples concerning their use, see the
		 *
		 * http://gmpg.org/xfn/
		 * @return bool
		 */
		public function removeXfnLink()
		{
			return false;
		}

		/**
		 * Remove jQuery Migrate
		 *
		 * @param WP_Scripts $scripts
		 */
		public function removeJqueryMigrate(&$scripts)
		{
			$scripts->remove('jquery');
			$scripts->add('jquery', false, array('jquery-core'), '1.12.4');
		}
		
		// Disable Embeds
		public function disableEmbeds()
		{
			global $wp, $wp_embed;
			
			$wp->public_query_vars = array_diff($wp->public_query_vars, array('embed'));
			remove_filter('the_content', array($wp_embed, 'autoembed'), 8);
			
			// Remove content feed filter
			remove_filter('the_content_feed', '_oembed_filter_feed_content');
			
			// Abort embed libraries loading
			remove_action('plugins_loaded', 'wp_maybe_load_embeds', 0);
			
			// No auto-embedding support
			add_filter('pre_option_embed_autourls', '__return_false');
			
			// Avoid oEmbed auto discovery
			add_filter('embed_oembed_discover', '__return_false');
			
			// Remove REST API related hooks
			remove_action('rest_api_init', 'wp_oembed_register_route');
			remove_filter('rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10);
			
			// Remove header actions
			remove_action('wp_head', 'wp_oembed_add_discovery_links');
			remove_action('wp_head', 'wp_oembed_add_host_js');
			
			remove_action('embed_head', 'enqueue_embed_scripts', 1);
			remove_action('embed_head', 'print_emoji_detection_script');
			remove_action('embed_head', 'print_embed_styles');
			remove_action('embed_head', 'wp_print_head_scripts', 20);
			remove_action('embed_head', 'wp_print_styles', 20);
			remove_action('embed_head', 'wp_no_robots');
			remove_action('embed_head', 'rel_canonical');
			remove_action('embed_head', 'locale_stylesheet', 30);
			
			remove_action('embed_content_meta', 'print_embed_comments_button');
			remove_action('embed_content_meta', 'print_embed_sharing_button');
			
			remove_action('embed_footer', 'print_embed_sharing_dialog');
			remove_action('embed_footer', 'print_embed_scripts');
			remove_action('embed_footer', 'wp_print_footer_scripts', 20);
			
			remove_filter('excerpt_more', 'wp_embed_excerpt_more', 20);
			remove_filter('the_excerpt_embed', 'wptexturize');
			remove_filter('the_excerpt_embed', 'convert_chars');
			remove_filter('the_excerpt_embed', 'wpautop');
			remove_filter('the_excerpt_embed', 'shortcode_unautop');
			remove_filter('the_excerpt_embed', 'wp_embed_excerpt_attachment');
			
			// Remove data and results filters
			remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
			remove_filter('oembed_response_data', 'get_oembed_response_data_rich', 10);
			remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
			
			// WooCommerce embeds in short description
			remove_filter('woocommerce_short_description', 'wc_do_oembeds');
			
			add_filter('tiny_mce_plugins', array($this, 'disableEmbedsTinyMcePlugin'));
			add_filter('rewrite_rules_array', array($this, 'disableEmbedsRewrites'));
			
			wp_deregister_script('wp-embed');
		}
		
		public function disableEmbedsTinyMcePlugin($plugins)
		{
			return array_diff($plugins, array('wpembed', 'wpview'));
		}
		
		public function disableEmbedsRewrites($rules)
		{
			$new_rules = array();
			foreach($rules as $rule => $rewrite) {
				if( false !== ($pos = strpos($rewrite, '?')) ) {
					$params = explode('&', substr($rewrite, $pos + 1));
					if( in_array('embed=true', $params) ) {
						continue;
					}
				}
				$new_rules[$rule] = $rewrite;
			}
			
			return $new_rules;
		}
		
		public function htmlCompressor()
		{
			ob_start(array($this, 'htmlCompressorMain'));
		}

		public function htmlCompressorMain($content)
		{
			$old_content = $content;

			if( $this->getPopulateOption('remove_xfn_link') ) {
				$content = preg_replace('/<link[^>]+href=(?:\'|")https?:\/\/gmpg.org\/xfn\/11(?:\'|")(?:[^>]+)?>/', '', $content);

				if( empty($content) ) {
					$content = $old_content;
				}
			}

			return $content;
		}
	}
