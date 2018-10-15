<?php
	/**
	 * This class configures the parameters advanced
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 2017 Webraftic Ltd
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WCL_ConfigAdvanced extends Wbcr_FactoryClearfy000_Configurate {

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
			if( $this->getPopulateOption('disable_heartbeat') && $this->getPopulateOption('disable_heartbeat') != 'default' ) {
				add_action('init', array($this, 'disableHeartbeat'), 1);
			}

			if( $this->getPopulateOption('heartbeat_frequency') && $this->getPopulateOption('heartbeat_frequency') != 'default' ) {
				add_filter('heartbeat_settings', array($this, 'clearfyHeartbeatFrequency'));
			}

			//============================================================
			//                      POST TOOLS COMPONENT
			//============================================================

			if( $this->plugin->isActivateComponent('post_tools') ) {
				if( ($this->getPopulateOption('revision_limit') || $this->getPopulateOption('revisions_disable')) && is_admin() ) {
					add_filter('wp_revisions_to_keep', array($this, 'clearfyRevisionsToKeep'), 10, 2);
				}

				if( $this->getPopulateOption('disable_post_autosave') && is_admin() ) {
					add_action('wp_print_scripts', array($this, 'disableAutoSave'));
				}

				if( $this->getPopulateOption('disable_texturization') ) {
					remove_filter('comment_text', 'wptexturize');
					remove_filter('the_content', 'wptexturize');
					remove_filter('the_excerpt', 'wptexturize');
					remove_filter('the_title', 'wptexturize');
					remove_filter('the_content_feed', 'wptexturize');
				}

				if( $this->getPopulateOption('disable_auto_correct_dangit') ) {
					remove_filter('the_content', 'capital_P_dangit');
					remove_filter('the_title', 'capital_P_dangit');
					remove_filter('comment_text', 'capital_P_dangit');
				}

				if( $this->getPopulateOption('disable_auto_paragraph') ) {
					remove_filter('the_content', 'wpautop');
				}
			}

			//============================================================
			//                      ADMINBAR MANAGER COMPONENT
			//============================================================

			if( $this->plugin->isActivateComponent('adminbar_manager') && is_user_logged_in() ) {
				if( $this->getPopulateOption('replace_howdy_welcome') ) {
					add_filter('admin_bar_menu', array($this, 'replaceHowdyText'), 25);
				}

				if( $this->getPopulateOption('disable_admin_bar') == 'for_all_users' ) {
					add_filter('show_admin_bar', '__return_false', 999999);
				}

				if( $this->getPopulateOption('disable_admin_bar') == 'for_all_users_except_administrator' ) {
					add_filter('show_admin_bar', array($this, 'removeFunctionAdminBar'));
				}

				if( $this->getPopulateOption('disable_admin_bar_logo') ) {
					add_action('wp_before_admin_bar_render', array($this, 'removeWpLogo'));
				}
			}

			//============================================================
			//                      WIDGETS TOOLS COMPONENT
			//============================================================

			if( $this->plugin->isActivateComponent('widget_tools') ) {
				add_action('widgets_init', array($this, 'unregisterDefaultWidgets'), 11);
			}
		}

		// unregister all widgets
		public function unregisterDefaultWidgets()
		{
			if( $this->getPopulateOption('remove_unneeded_widget_page') ) {
				unregister_widget('WP_Widget_Pages');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_calendar') ) {
				unregister_widget('WP_Widget_Calendar');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_tag_cloud') ) {
				unregister_widget('WP_Widget_Tag_Cloud');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_archives') ) {
				unregister_widget('WP_Widget_Archives');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_links') ) {
				unregister_widget('WP_Widget_Links');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_meta') ) {
				unregister_widget('WP_Widget_Meta');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_search') ) {
				unregister_widget('WP_Widget_Search');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_text') ) {
				unregister_widget('WP_Widget_Text');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_categories') ) {
				unregister_widget('WP_Widget_Categories');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_recent_posts') ) {
				unregister_widget('WP_Widget_Recent_Posts');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_recent_comments') ) {
				unregister_widget('WP_Widget_Recent_Comments');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_rss') ) {
				unregister_widget('WP_Widget_RSS');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_menu') ) {
				unregister_widget('WP_Nav_Menu_Widget');
			}
			if( $this->getPopulateOption('remove_unneeded_widget_twenty_eleven_ephemera') ) {
				unregister_widget('Twenty_Eleven_Ephemera_Widget');
			}
		}

		/**
		 * Revisions limit
		 *
		 * @since     0.9.5
		 */

		public function clearfyRevisionsToKeep($num, $post)
		{
			if( $this->getPopulateOption('revision_limit', null) && is_numeric($this->getPopulateOption('revision_limit', null)) ) {
				$num = $this->getPopulateOption('revision_limit', 0);
			}

			if( $this->getPopulateOption('revisions_disable') ) {
				$num = 0;
			}

			return $num;
		}

		/**
		 * Revisions limit
		 *
		 * @since     0.9.5
		 */

		public function clearfyHeartbeatFrequency($settings)
		{
			if( 0 < (int)$this->getPopulateOption('heartbeat_frequency') ) {
				$settings['interval'] = (int)$this->getPopulateOption('heartbeat_frequency');
			}

			return $settings;
		}

		public function disableHeartbeat()
		{
			switch( $this->getPopulateOption('disable_heartbeat') ) {
				case 'everywhere':
					wp_deregister_script('heartbeat');
					break;
				case 'allow_only_on_post_edit_pages':
					global $pagenow;
					if( $pagenow != 'post.php' && $pagenow != 'post-new.php' ) {
						wp_deregister_script('heartbeat');
					}
					break;
				case 'on_dashboard_page':
					global $pagenow;
					if( 'index.php' === $pagenow ) {
						wp_deregister_script('heartbeat');
					}
					break;
			}
		}

		public function disableAutoSave()
		{
			wp_deregister_script('autosave');
		}

		/**
		 * @param WP_Admin_Bar $wp_admin_bar
		 */
		public function replaceHowdyText($wp_admin_bar)
		{
			#Fix bug when the attribute $wp_admin_bar does not belong to the class WP_Admin_Bar
			require_once ABSPATH . "/wp-includes/class-wp-admin-bar.php";

			if( empty($wp_admin_bar) || !($wp_admin_bar instanceof WP_Admin_Bar) ) {
				return;
			}
			$my_account = $wp_admin_bar->get_node('my-account');

			$newtitle = str_replace(__('Howdy', 'clearfy') . ',', __('Welcome', 'clearfy') . ',', $my_account->title);
			$wp_admin_bar->add_node(array(
				'id' => 'my-account',
				'title' => $newtitle,
			));
		}

		/**
		 * @param $content
		 * @return bool
		 */
		public function removeFunctionAdminBar($content)
		{
			return (current_user_can('administrator'))
				? $content
				: false;
		}

		/**
		 * @global WP_Admin_Bar $wp_admin_bar
		 */
		public function removeWpLogo()
		{
			global $wp_admin_bar;
			$wp_admin_bar->remove_menu('wp-logo');
		}
	}




