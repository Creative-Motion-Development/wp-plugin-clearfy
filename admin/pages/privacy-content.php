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

	class WCL_PrivacyContentPage extends WCL_Page {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "privacy";

		public $page_parent_page = 'defence';

		public $page_menu_position = 15;

		public $page_menu_dashicon = 'dashicons-hidden';

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			$this->menu_title = __('Приватность контента', 'clearfy');

			parent::__construct($plugin);

			$this->plugin = $plugin;
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
				'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Скройте версии плагинов и Wordpress</strong>.', 'mmwp') . '<p>' . __('Многие плагины и даже сам Wordpress, публикуют свою версию в публичных областях вашего сайта, получив эту информацию, злоумышленник может знать об уязвимостях обнаруженных в полученной им номере версии ядра Wordpress или плагинов.', 'clearfy') . '</p></div>'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_html_comments',
				'title' => __('Remove html comments', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('This function will remove all html comments in the source code, except for special and hidden comments. This is necessary to hide the version of installed plugins.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Remove html comments in source code.', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_meta_generator',
				'title' => __('Remove meta generator', 'clearfy') . ' <span class="wbcr-clearfy-recomended-text">(' . __('Recommended', 'clearfy') . ')</span>',
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('Allows attacker to learn the version of WP installed on the site. This meta tag has no useful function.', 'clearfy') . '<br><b>Clearfy: </b>' . sprintf(__('Removes the meta tag from the %s section', 'clearfy'), '&lt;head&gt;'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_style_version',
				'title' => __('Remove Version from Stylesheet', 'clearfy') . ' <span class="wbcr-clearfy-recomended-text">(' . __('Recommended', 'clearfy') . ')</span>',
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('To make it more difficult for others to hack your website you can remove the WordPress version number from your site, your css and js. Without that number it\'s not possible to see if you run not the current version to exploit bugs from the older versions. <br><br>
					Additionally it can improve the loading speed of your site, because without query strings in the URL the css and js files can be cached.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Removes the wordpress version number from stylesheets (not logged in user only).', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_js_version',
				'title' => __('Remove Version from Script', 'clearfy') . ' <span class="wbcr-clearfy-recomended-text">(' . __('Recommended', 'clearfy') . ')</span>',
				'layout' => array('hint-type' => 'icon'),
				'hint' => __('To make it more difficult for others to hack your website you can remove the WordPress version number from your site, your css and js. Without that number it\'s not possible to see if you run not the current version to exploit bugs from the older versions. <br><br>
					Additionally it can improve the loading speed of your site, because without query strings in the URL the css and js files can be cached.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Removes wordpress version number from scripts (not logged in user only).', 'clearfy'),
				'default' => false
			);

			$options[] = array(
				'type' => 'textarea',
				'name' => 'remove_version_exclude',
				'height' => '120',
				'title' => __('Eclude stylesheet/script file names', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('Enter Stylesheet/Script file names to exclude from version removal (each exclude file starts with a new line)', 'clearfy') . '<br><br><b>' . __('Example', 'clearfy') . ':</b>' . ' http://testwp.dev/wp-includes/js/jquery/jquery.js',
			);

			$form_options = array();

			$form_options[] = array(
				'type' => 'form-group',
				'items' => $options,
				//'cssClass' => 'postbox'
			);

			return apply_filters('wbcr_clr_privacy_form_options', $form_options, $this);
		}
	}