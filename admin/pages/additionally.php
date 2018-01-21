<?php

	/**
	 * The page Settings.
	 *
	 * @since 1.0.0
	 */
	class WbcrClr_AdditionallyPage extends WbcrClr_Page {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "additionally";

		public $page_menu_dashicon = 'dashicons-list-view';

		public $page_menu_position = 0;

		public function __construct(Factory000_Plugin $plugin)
		{
			$this->menuTitle = __('Advanced', 'clearfy');

			parent::__construct($plugin);
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

			if( $this->isPostRevisionConstant() ) {
				$this->printWarningNotice(__('Warning! In the wp-config.php file, a constant WP_POST_REVISIONS is found, it determines the number of revisions. Delete it so you can change this value through the admin panel.', 'clearfy'));
			}
		}

		/**
		 * Permalinks options.
		 *
		 * @since 1.0.0
		 * @return mixed[]
		 */
		public function getOptions()
		{
			$options = array();

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
					array('60', '60 ' . __('seconds', 'clearfy'))
				),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('Select the heartbeat frequency wordpress. We recommend you 60 seconds, default is 20 seconds.', 'clearfy'),
				'default' => 'default'
			);

			/*$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_admin_notices',
				'title' => __('Disable admin notice', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('Whenever there’s a major release available, a notification will display at the top of your admin area, letting you know your version is out-of-date and you need to update the core code.<br>
For many people, this nag can be annoying. And if you developer websites for clients, you may want to hide it. After all, who wants to let their clients know their software is old?', 'clearfy') . '<br><b>Clearfy</b>: ' . __('Disable admin notices.', 'clearfy'),
				'default' => false
			);*/

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

			/*$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_selection_patent',
				'title' => __('Disable selection of parent category', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('If you do not use this section, you can disable it.', 'clearfy') . '<br><b>Clearfy</b>: ' . __('Disable selection of parent category.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'enable_categoty_tree',
				'title' => __('Enable category checklist tree', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'green'),
				'hint' => __('Enable this if you want to enable category checklist tree. On the post editing screen, after saving a post, you will notice that the checked categories are displayed on top, breaking the category hierarchy. This option removes that feature. Additionally, it automatically scrolls to the first checked category.', 'clearfy') . '<br><b>Clearfy</b>: ' . __('Enable category checklist tree.', 'clearfy'),
				'default' => false
			);*/

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

			$options[] = array(
				'type' => 'html',
				'html' => '<div class="wbcr-clearfy-group-header">' . '<strong>' . __('Others', 'clearfy') . '</strong>' . '<p>' . __('Other useful features.', 'clearfy') . '</p>' . '</div>'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'enable_wordpres_sanitize',
				'title' => __('Enable Sanitization of WordPress', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('File names and some titles can have special characters, which can cause problems when creating permalinks.', 'clearfy') . '<br><b>Clearfy</b>: ' . __('Removes symbols, spaces, latin and other languages characters from uploaded files and gives them "permalink" structure (clean characters, only lowercase and dahes).', 'clearfy'),
				'default' => false
			);

			$formOptions = array();

			$formOptions[] = array(
				'type' => 'form-group',
				'items' => $options,
				//'cssClass' => 'postbox'
			);

			return apply_filters('wbcr_clr_additionally_form_options', $formOptions, $this);
		}
	}

	FactoryPages000::register($wbcr_clearfy_plugin, 'WbcrClr_AdditionallyPage');
