<?php

	/**
	 * The page Settings.
	 *
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}
	
	class WCL_AdvancedPage extends Wbcr_FactoryClearfy000_PageBase {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "advanced";

		public $page_menu_dashicon = 'dashicons-list-view';

		public $page_menu_position = 1;
		
		public $available_for_multisite = true;

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			$this->menu_title = __('Advanced', 'clearfy');
			$this->page_menu_short_description = __('Useful tweaks', 'clearfy');

			parent::__construct($plugin);

			$this->plugin = $plugin;
		}

		protected function isPostRevisionConstant()
		{
			$config_path = ABSPATH . '/wp-config.php';

			if( file_exists($config_path) ) {
				$file = fopen($config_path, 'r');
				$content = fread($file, filesize($config_path));
				fclose($file);

				if( !empty($content) && preg_match('/define(.+?)WP_POST_REVISIONS/', $content) ) {
					return true;
				}
			}

			return false;
		}

		public function warningNotice()
		{
			parent::warningNotice();

			if( !$this->plugin->getPopulateOption('revisions_disable') && $this->isPostRevisionConstant() ) {
				$this->printWarningNotice(__('Warning! In the wp-config.php file, a constant WP_POST_REVISIONS is found, it determines the number of revisions. Delete it so you can change this value through the admin panel.', 'clearfy'));
			}
		}

		/**
		 * Permalinks options.
		 *
		 * @since 1.0.0
		 * @return mixed[]
		 */
		public function getPopulateOptions()
		{

			$options = array();

			$options[] = array(
				'type' => 'html',
				'html' => '<div class="wbcr-clearfy-group-header">' . '<strong>' . __('Clearfy options', 'clearfy') . '</strong>' . '<p>' . __('This group of settings allows you to configure the work of the Clearfy plugin.', 'clearfy') . '</p>' . '</div>'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_clearfy_extra_menu',
				'title' => __('Disable Clearfy extra menu', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('This setting allows you to disable the additional menu of the Clearfy plugin, in the admin bar. This menu is required to work with the Minify and Combine and Assets Manager components.', 'clearfy'),
				'default' => false
			);


			$options[] = array(
				'type' => 'html',
				'html' => '<div class="wbcr-clearfy-group-header">' . '<strong>' . __('Heartbeat', 'clearfy') . '</strong>' . '<p>' . __('The WordPress Heartbeat API uses /wp-admin/admin-ajax.php to run AJAX calls from the web-browser. While this is great and all it can also cause high CPU usage and crazy amounts of PHP calls. For example, if you leave your dashboard open it will keep sending POST requests to this file on a regular interval, every 15 seconds. Here is an example below of it happening.', 'clearfy') . '</p>' . '</div>'
			);

			$options[] = array(
				'type' => 'dropdown',
				'name' => 'disable_heartbeat',
				'way' => 'buttons',
				'title' => __('Disable Heartbeat', 'clearfy'),
				'data' => array(
					array('default', __('Default', 'clearfy')),
					array('everywhere', __('Everywhere', 'clearfy')),
					array('on_dashboard_page', __('On dashboard page', 'clearfy')),
					array('allow_only_on_post_edit_pages', __('Only allow when editing Posts/Pages', 'clearfy'))
				),
				//'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				//'hint' => __('You can disable all plugin updates or choose manual or automatic update mode.', 'clearfy'),
				'events' => array(
					'default' => array(
						'show' => '.factory-control-heartbeat_frequency'
					),
					'on_dashboard_page' => array(
						'show' => '.factory-control-heartbeat_frequency'
					),
					'allow_only_on_post_edit_pages' => array(
						'show' => '.factory-control-heartbeat_frequency'
					),
					'everywhere' => array(
						'hide' => '.factory-control-heartbeat_frequency'
					)
				),
				'default' => 'default',
			);

			$options[] = array(
				'type' => 'dropdown',
				'name' => 'heartbeat_frequency',
				'title' => __('Heartbeat frequency', 'clearfy'),
				'data' => array(
					array('default', __('Wordpress default', 'clearfy')),
					array('20', '20 ' . __('seconds', 'clearfy')),
					array('25', '25 ' . __('seconds', 'clearfy')),
					array('30', '30 ' . __('seconds', 'clearfy')),
					array('35', '35 ' . __('seconds', 'clearfy')),
					array('40', '40 ' . __('seconds', 'clearfy')),
					array('45', '45 ' . __('seconds', 'clearfy')),
					array('50', '50 ' . __('seconds', 'clearfy')),
					array('55', '55 ' . __('seconds', 'clearfy')),
					array('60', '60 ' . __('seconds', 'clearfy')),
					array('80', '80 ' . __('seconds', 'clearfy')),
					array('120', '120 ' . __('seconds', 'clearfy')),
					array('150', '150 ' . __('seconds', 'clearfy')),
					array('200', '200 ' . __('seconds', 'clearfy')),
					array('250', '250 ' . __('seconds', 'clearfy')),
					array('300', '300 ' . __('seconds', 'clearfy')),
					array('400', '400 ' . __('seconds', 'clearfy')),
					array('500', '500 ' . __('seconds', 'clearfy'))
				),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('Select the heartbeat frequency wordpress. We recommend you 60 seconds, default is 20 seconds.', 'clearfy'),
				'default' => 'default'
			);

			//============================================================
			//                 ADMINBAR MANAGER COMPONENT
			//============================================================

			if( $this->plugin->isActivateComponent('adminbar_manager') ) {
				$options[] = array(
					'type' => 'html',
					'html' => '<div class="wbcr-clearfy-group-header">' . '<strong>' . __('Admin bar', 'clearfy') . '</strong>' . '<p>' . __('In this group of settings, you can manage the adminbar.', 'clearfy') . '</p>' . '</div>'
				);

				$options[] = array(
					'type' => 'dropdown',
					'name' => 'disable_admin_bar',
					'way' => 'buttons',
					'title' => __('Disable admin top bar', 'clearfy'),
					'data' => array(
						array('enable', __('Default enable', 'clearfy')),
						array('for_all_users', __('For all users', 'clearfy')),
						array(
							'for_all_users_except_administrator',
							__('For all users except administrator', 'clearfy')
						)
					),
					'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
					'hint' => __('In some cases, you need to disable the floating top admin panel. You can disable this panel.', 'clearfy') . '<br><b>Clearfy</b>: ' . __('Disable admin top bar.', 'clearfy'),
					'default' => 'enable',
				);

				$options[] = array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'disable_admin_bar_logo',
					'title' => __('Remove admin bar WP logo', 'clearfy'),
					'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'green'),
					'hint' => __('Enable this if you want to remove wp logo from admin bar.', 'clearfy'),
					'default' => false
				);

				$options[] = array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'replace_howdy_welcome',
					'title' => __('Replace "Howdy" text with "Welcome"', 'clearfy'),
					'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'green'),
					'hint' => __('Replaces the welcome text in admin bar.', 'clearfy'),
					'default' => false
				);
			}

			//============================================================
			//                      POST TOOLS COMPONENT
			//============================================================

			if( $this->plugin->isActivateComponent('post_tools') ) {
				$options[] = array(
					'type' => 'html',
					'html' => '<div class="wbcr-clearfy-group-header">' . '<strong>' . __('Posts', 'clearfy') . '</strong>' . '<p>' . __('In this group of options, you can manage revisions and post autosave.', 'clearfy') . '</p>' . '</div>'
				);

				$options[] = array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'revisions_disable',
					'title' => __('Disable revision', 'clearfy'),
					'default' => false,
					'eventsOn' => array(
						'hide' => '.factory-control-revision_limit'
					),
					'eventsOff' => array(
						'show' => '.factory-control-revision_limit'
					),
				);

				$options[] = array(
					'type' => 'dropdown',
					'name' => 'revision_limit',
					'title' => __('Limit Post Revisions', 'clearfy'),
					'data' => array(
						array('default', __('Wordpress default', 'clearfy')),
						array('15', '15 ' . __('revisions', 'clearfy')),
						array('20', '20 ' . __('revisions', 'clearfy')),
						array('25', '25 ' . __('revisions', 'clearfy')),
						array('30', '30 ' . __('revisions', 'clearfy')),
						array('35', '35 ' . __('revisions', 'clearfy')),
						array('40', '40 ' . __('revisions', 'clearfy')),
						array('45', '45 ' . __('revisions', 'clearfy')),
						array('50', '50 ' . __('revisions', 'clearfy')),
						array('55', '55 ' . __('revisions', 'clearfy')),
						array('60', '60 ' . __('revisions', 'clearfy'))
					),
					'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
					'hint' => __('WordPress automatically saves revisions when you are working on posts and pages. These can begin to add up pretty quick. By default, there is no limit in place. We have seen posts with over 1,000 revisions. Multiply this by 50 pages and suddenly you have over 50,000 revisions sitting in your database. The problem with this is that you will most likely never use them and they can start slowing down your database as well as using disk space.
So we recommend either disabling or limiting your revisions. ', 'clearfy'),
					'default' => 'default'
				);

				$options[] = array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'disable_post_autosave',
					'title' => __('Disable autosave', 'clearfy'),
					'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
					'hint' => __('WordPress by default automatically saves a draft every 60 seconds (1 minute). There are reasons why you might want to change this.', 'clearfy') . '<br><b>Clearfy</b>: ' . __('Disables automatic saving of drafts.', 'clearfy'),
					'default' => false
				);

				$options[] = array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'disable_texturization',
					'title' => __('Disable Texturization - Smart Quotes', 'clearfy'),
					'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
					'hint' => __('Function of text formatting. This function makes the text more correct, readable and visually appealing. But sometimes this function may prevent you from using certain codes and symbols.', 'clearfy') . '<br><b>Clearfy</b>: ' . __('Disable Texturization - Smart Quotes.', 'clearfy'),
					'default' => false
				);

				$options[] = array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'disable_auto_correct_dangit',
					'title' => __('Disable capitalization in Wordpress branding', 'clearfy'),
					'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
					'hint' => __('Replaces the incorrectly written letter "p" in the middle of WordPress (you need to write with the capital P in the middle).', 'clearfy') . '<br><b>Clearfy</b>: ' . __('Disable capitalization in Wordpress branding.', 'clearfy'),
					'default' => false
				);

				$options[] = array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'disable_auto_paragraph',
					'title' => __('Disable auto inserted paragraphs (i.e. p tags)', 'clearfy'),
					'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
					'hint' => __('Replaces the double shifting of a string to an HTML p ... /p construct, and a single one on br.', 'clearfy') . '<br><b>Clearfy</b>: ' . __('Disable auto inserted paragraphs.', 'clearfy'),
					'default' => false
				);
			}

			$formOptions = array();

			$formOptions[] = array(
				'type' => 'form-group',
				'items' => $options,
				//'cssClass' => 'postbox'
			);

			return apply_filters('wbcr_clr_additionally_form_options', $formOptions, $this);
		}
	}
