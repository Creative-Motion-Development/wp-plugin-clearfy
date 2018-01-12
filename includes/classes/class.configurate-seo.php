<?php
	
	/**
	 * This class configures the parameters seo
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */
	class WbcrClearfy_ConfigSeo extends WbcrFactoryClearfy_Configurate {
		
		public function registerActionsAndFilters()
		{

			if( $this->getOption('content_image_auto_alt') ) {
				add_filter('the_content', array($this, 'contentImageAutoAlt'));
			}

			if( $this->getOption('right_robots_txt') ) {
				add_filter('robots_txt', array($this, 'rightRobotsTxt'));
			}

			if( $this->getOption('remove_last_item_breadcrumb_yoast') ) {
				add_filter('wpseo_breadcrumb_single_link', array($this, 'removeLastItemBreadcrumbYoast'));
			}
			
			if( $this->getOption('attachment_pages_redirect') ) {
				add_action('template_redirect', array($this, 'attachmentPagesRedirect'));
			}
			
			if( $this->getOption('remove_single_pagination_duplicate') ) {
				add_action('template_redirect', array($this, 'removeSinglePaginationDuplicate'));
			}
			
			if( $this->getOption('set_last_modified_headers') ) {
				add_action('template_redirect', array($this, 'setLastModifiedHeaders'));
				add_action('wp_logout', array($this, 'lastModifedFlushCookie'));
			}

			if( $this->getOption('yoast_remove_image_from_xml_sitemap') ) {
				$this->yoastRemoveImageFromXmlSitemap();
			}
			
			if( $this->getOption('yoast_remove_head_comment') ) {
				add_action('init', array($this, 'yoastRemoveHeadComment'));
			}

			if( $this->getOption('remove_replytocom') ) {
				add_action('template_redirect', array($this, 'removeReplytocomRedirect'), 1);
				add_filter('comment_reply_link', array($this, 'removeReplytocomLink'));
			}

			//if( $this->getOption('redirect_from_http_to_https') ) {
			//add_action('init', array($this, 'redirectFromHttpToHttps'));
			//}

			if( $this->getOption('yoast_remove_json_ld_search') ) {
				add_filter('disable_wpseo_json_ld_search', '__return_true');
			}

			if( $this->getOption('yoast_remove_json_ld_output') ) {
				add_filter('wpseo_json_ld_output', array($this, 'removeYoastJson'), 10, 1);
			}

			add_action('wp', array($this, 'redirectArchives'));
		}

		public function removeYoastJson($data)
		{
			$data = array();

			return $data;
		}

		/**
		 * Add post title in image alt attribute
		 *
		 * @param $content
		 * @return mixed
		 */
		
		public function contentImageAutoAlt($content)
		{
			
			global $post;
			
			$pattern = array(' alt=""', ' alt=\'\'');
			
			$replacement = array(
				' alt="' . esc_attr($post->post_title) . '"',
				' alt=\'' . esc_attr($post->post_title) . '\''
			);
			
			$content = str_replace($pattern, $replacement, $content);
			
			return $content;
		}

		/**
		 * Add directories to virtual robots.txt file

		 */

		public function rightRobotsTxt($output)
		{
			if( $this->getOption('robots_txt_text') ) {
				return $this->getOption('robots_txt_text');
			}

			return wbcr_clearfy_get_right_robot_txt();
		}

		/**
		 * Remove last item from breadcrumbs SEO by YOAST
		 * http://www.wpdiv.com/remove-post-title-yoast-seo-plugin-breadcrumb/

		 */

		public function removeLastItemBreadcrumbYoast($link_output)
		{

			if( strpos($link_output, 'breadcrumb_last') !== false ) {
				$link_output = '';
			}

			return $link_output;
		}

		/**
		 * Attachment pages redirect
		 */

		public function attachmentPagesRedirect()
		{

			global $post;
			if( is_attachment() ) {
				if( isset($post->post_parent) && ($post->post_parent != 0) ) {
					wp_redirect(get_permalink($post->post_parent), 301);
				} else {
					wp_redirect(home_url(), 301);
				}
				exit;
			}
		}

		/**
		 * Remove single pagination duplicate
		 */

		public function removeSinglePaginationDuplicate()
		{

			if( is_singular() && !is_front_page() ) {

				global $post, $page;

				$num_pages = substr_count($post->post_content, '<!--nextpage-->') + 1;

				if( $page > $num_pages || $page == 1 ) {

					wp_redirect(get_permalink($post->ID));

					exit;
				}
			}
		}

		/**
		 * Remove yoast comment
		 */

		public function yoastRemoveHeadComment()
		{
			if( defined('WPSEO_VERSION') && !is_admin() ) {
				add_action('get_header', array($this, 'yoastRemoveHeadCommentStart'));
				add_action('wp_head', array($this, 'yoastRemoveHeadCommentEnd'), 999);
			}
		}

		public function yoastRemoveHeadCommentStart()
		{
			ob_start(array($this, 'yoastRemoveHeadCommentRemove'));
		}

		public function yoastRemoveHeadCommentEnd()
		{
			ob_end_flush();
		}

		public function yoastRemoveHeadCommentRemove($html)
		{
			return preg_replace('/^<!--.*?[Yy]oast.*?-->$/mi', '', $html);
		}

		/**
		 * Redirect archives author, date, tags
		 */

		public function redirectArchives()
		{

			if( $this->getOption('redirect_archives_author') ) {
				if( is_author() && !is_admin() ) {
					wp_redirect(home_url(), 301);

					die();
				}
			}

			if( $this->getOption('redirect_archives_date') ) {
				if( is_date() && !is_admin() ) {
					wp_redirect(home_url(), 301);

					die();
				}
			}

			if( $this->getOption('redirect_archives_tag') ) {
				if( is_tag() && !is_admin() ) {
					wp_redirect(home_url(), 301);

					die();
				}
			}
		}

		/**
		 * Remove replytocom
		 */
		public function removeReplytocomRedirect()
		{
			if( isset($_GET['replytocom']) && is_singular() ) {
				$post_url = get_permalink($GLOBALS['post']->ID);
				$comment_id = sanitize_text_field($_GET['replytocom']);
				$query_string = remove_query_arg('replytocom', sanitize_text_field($_SERVER['QUERY_STRING']));

				if( !empty($query_string) ) {
					$post_url .= '?' . $query_string;
				}
				$post_url .= '#comment-' . $comment_id;

				wp_redirect($post_url, 301);
				die();
			}

			return false;
		}

		public function removeReplytocomLink($link)
		{
			return preg_replace('`href=(["\'])(?:.*(?:\?|&|&#038;)replytocom=(\d+)#respond)`', 'href=$1#comment-$2', $link);
		}

		/**
		 * Remove <image:image> from sitemap
		 */
		public function yoastRemoveImageFromXmlSitemap()
		{
			add_filter('wpseo_xml_sitemap_img', '__return_false');
			add_filter('wpseo_sitemap_url', array($this, 'yoastRemoveImageFromXmlClean'), 10, 2);
		}

		public function yoastRemoveImageFromXmlClean($output, $url)
		{
			$output = preg_replace('/<image:image[^>]*?>.*?<\/image:image>/si', '', $output);

			return $output;
		}

		public function redirectFromHttpToHttps()
		{
			if( is_ssl() ) {
				return;
			}
			if( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
				wp_redirect(set_url_scheme($_SERVER['REQUEST_URI'], 'https'), 301);
			} else {
				wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
			}
			die();
		}

		public function setLastModifiedHeaders()
		{
			//todo: Fix bug, stop last modidifed headers for logged users
			if( is_user_logged_in() && (defined('DOING_AJAX') && DOING_AJAX) || (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) || (defined('REST_REQUEST') && REST_REQUEST) || (is_admin()) ) {
				return;
			}

			if( class_exists('woocommerce') && function_exists('is_cart') && function_exists('is_checkout') && function_exists('is_account_page') && (is_cart() || is_checkout() || is_account_page()) ) {
				return;
			}

			//todo: Fix bug, admin bar is not hidden after logout
			$last_modified_flush = isset($_COOKIE['wbcr_lastmodifed_flush']);

			global $wp;
			$last_modified_exclude = $this->getOption('last_modified_exclude');
			$last_modified_exclude_exp = explode(PHP_EOL, $last_modified_exclude);

			$current_url = home_url(add_query_arg(array(), $wp->request));

			foreach($last_modified_exclude_exp as $expr) {
				if( !empty($expr) && strpos(urldecode($current_url), $expr) !== false ) {
					return;
				}
			}

			/**
			 * if Search - just return
			 */
			if( is_search() ) {
				return;
			}

			$last_modified = '';
			/**
			 * If posts, pages, custom post types
			 */
			if( is_singular() ) {
				global $post;
				if( !isset($post->post_modified_gmt) ) {
					return;
				}
				$post_time = strtotime($post->post_modified_gmt);
				$modified_time = $post_time;
				/**
				 * If we have comment set new modified date
				 */
				if( (int)$post->comment_count > 0 ) {
					$comments = get_comments(array(
						'post_id' => $post->ID,
						'number' => '1',
						'status' => 'approve',
						'orderby' => 'comment_date_gmt',
					));
					if( !empty($comments) && isset($comments[0]) ) {
						$comment_time = strtotime($comments[0]->comment_date_gmt);
						if( $comment_time > $post_time ) {
							$modified_time = $comment_time;
						}
					}
				}
				$last_modified = str_replace('+0000', 'GMT', gmdate('r', $modified_time));
			}
			/**
			 * If any archives: categories, tags, taxonomy terms, post type archives
			 */
			if( is_archive() || is_home() ) {
				global $posts;
				if( empty($posts) ) {
					return;
				}
				$post = $posts[0];
				if( !isset($post->post_modified_gmt) ) {
					return;
				}
				$post_time = strtotime($post->post_modified_gmt);
				$modified_time = $post_time;
				$last_modified = str_replace('+0000', 'GMT', gmdate('r', $modified_time));
			}

			/**
			 * If headers already sent - do nothing
			 */

			if( headers_sent() ) {
				return;
			}

			if( !empty($last_modified) && !empty($modified_time) ) {

				//todo: Fix bug, admin bar is not hidden after logout
				if( $last_modified_flush ) {
					$modified_time += rand(1, 99);
				}

				header('Last-Modified: ' . $last_modified);

				if( $this->getOption('if_modified_since_headers') && !is_user_logged_in() ) {

					if( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $modified_time ) {

						$protocol = (isset($_SERVER['SERVER_PROTOCOL'])
							? $_SERVER['SERVER_PROTOCOL']
							: 'HTTP/1.0');

						header($protocol . ' 304 Not Modified');
					}
				}
			}
		}

		function lastModifedFlushCookie()
		{
			if( !isset($_COOKIE['wbcr_lastmodifed_flush']) ) {
				setcookie("wbcr_lastmodifed_flush", 1, time() + 3600);
			}
		}
	}