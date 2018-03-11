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
			if( $this->getOption('disable_heartbeat') && $this->getOption('disable_heartbeat') != 'default' ) {
				add_action('init', array($this, 'disableHeartbeat'), 1);
			}

			if( $this->getOption('heartbeat_frequency') && $this->getOption('heartbeat_frequency') != 'default' ) {
				add_filter('heartbeat_settings', array($this, 'clearfyHeartbeatFrequency'));
			}

			//============================================================
			//                      POST TOOLS COMPONENT
			//============================================================

			if( $this->plugin->isActivateComponent('post_tools') ) {
				if( ($this->getOption('revision_limit') || $this->getOption('revisions_disable')) && is_admin() ) {
					add_filter('wp_revisions_to_keep', array($this, 'clearfyRevisionsToKeep'), 10, 2);
				}

				if( $this->getOption('disable_post_autosave') && is_admin() ) {
					add_action('wp_print_scripts', array($this, 'disableAutoSave'));
				}

				if( $this->getOption('disable_texturization') ) {
					remove_filter('comment_text', 'wptexturize');
					remove_filter('the_content', 'wptexturize');
					remove_filter('the_excerpt', 'wptexturize');
					remove_filter('the_title', 'wptexturize');
					remove_filter('the_content_feed', 'wptexturize');
				}

				if( $this->getOption('disable_auto_correct_dangit') ) {
					remove_filter('the_content', 'capital_P_dangit');
					remove_filter('the_title', 'capital_P_dangit');
					remove_filter('comment_text', 'capital_P_dangit');
				}

				if( $this->getOption('disable_auto_paragraph') ) {
					remove_filter('the_content', 'wpautop');
				}
			}

			//============================================================
			//                      ADMINBAR MANAGER COMPONENT
			//============================================================

			if( $this->plugin->isActivateComponent('adminbar_manager') && is_user_logged_in() ) {
				if( $this->getOption('replace_howdy_welcome') ) {
					add_filter('admin_bar_menu', array($this, 'replaceHowdyText'), 25);
				}

				if( $this->getOption('disable_admin_bar') == 'for_all_users' ) {
					add_filter('show_admin_bar', '__return_false', 999999);
				}

				if( $this->getOption('disable_admin_bar') == 'for_all_users_except_administrator' ) {
					add_filter('show_admin_bar', array($this, 'removeFunctionAdminBar'));
				}

				if( $this->getOption('disable_admin_bar_logo') ) {
					add_action('wp_before_admin_bar_render', array($this, 'removeWpLogo'));
				}
			}

			//============================================================
			//                      WIDGETS TOOLS COMPONENT
			//============================================================

			if( $this->plugin->isActivateComponent('widget_tools') ) {
				add_action('widgets_init', array($this, 'unregisterDefaultWidgets'), 11);
			}

			if( $this->getOption('enable_wordpres_sanitize') ) {
				require_once(WCL_PLUGIN_DIR . '/includes/classes/class.wordpress-sanitize.php');

				if( is_admin() || (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) ) {
					remove_filter('sanitize_title', 'sanitize_title_with_dashes', 11);
					add_filter('sanitize_title', array('Wbcr_Germanizer', 'sanitize_title_filter'), 10, 2);
					add_filter('sanitize_file_name', array('Wbcr_Germanizer', 'sanitize_filename_filter'), 10, 1);
				}
			}
		}

		// unregister all widgets
		public function unregisterDefaultWidgets()
		{
			if( $this->getOption('remove_unneeded_widget_page') ) {
				unregister_widget('WP_Widget_Pages');
			}
			if( $this->getOption('remove_unneeded_widget_calendar') ) {
				unregister_widget('WP_Widget_Calendar');
			}
			if( $this->getOption('remove_unneeded_widget_tag_cloud') ) {
				unregister_widget('WP_Widget_Tag_Cloud');
			}
			if( $this->getOption('remove_unneeded_widget_archives') ) {
				unregister_widget('WP_Widget_Archives');
			}
			if( $this->getOption('remove_unneeded_widget_links') ) {
				unregister_widget('WP_Widget_Links');
			}
			if( $this->getOption('remove_unneeded_widget_meta') ) {
				unregister_widget('WP_Widget_Meta');
			}
			if( $this->getOption('remove_unneeded_widget_search') ) {
				unregister_widget('WP_Widget_Search');
			}
			if( $this->getOption('remove_unneeded_widget_text') ) {
				unregister_widget('WP_Widget_Text');
			}
			if( $this->getOption('remove_unneeded_widget_categories') ) {
				unregister_widget('WP_Widget_Categories');
			}
			if( $this->getOption('remove_unneeded_widget_recent_posts') ) {
				unregister_widget('WP_Widget_Recent_Posts');
			}
			if( $this->getOption('remove_unneeded_widget_text') ) {
				unregister_widget('WP_Widget_Recent_Comments');
			}
			if( $this->getOption('remove_unneeded_widget_rss') ) {
				unregister_widget('WP_Widget_RSS');
			}
			if( $this->getOption('remove_unneeded_widget_menu') ) {
				unregister_widget('WP_Nav_Menu_Widget');
			}
			if( $this->getOption('remove_unneeded_widget_twenty_eleven_ephemera') ) {
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
			if( $this->getOption('revision_limit', null) && is_numeric($this->getOption('revision_limit', null)) ) {
				$num = $this->getOption('revision_limit', 0);
			}

			if( $this->getOption('revisions_disable') ) {
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
			if( 0 < (int)$this->getOption('heartbeat_frequency') ) {
				$settings['interval'] = (int)$this->getOption('heartbeat_frequency');
			}

			return $settings;
		}

		public function disableHeartbeat()
		{
			switch( $this->getOption('disable_heartbeat') ) {
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




