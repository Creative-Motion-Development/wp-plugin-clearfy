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

	class WCL_PerformanceHtmlMinifyPage extends WCL_Page {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "performance_html_minify";

		/**
		 * @var string
		 */
		public $page_parent_page = 'performance';

		/**
		 * @var string
		 */
		public $page_menu_dashicon = 'dashicons-performance';

		/**
		 * @var int
		 */
		public $page_menu_position = 20;

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			$this->menu_title = __('Html Minify', 'clearfy');

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
				'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Make your website’s markup look professional by using Minify HTML options</strong>.', 'clearfy') . '<p>' . __('Ever look at the HTML markup of your website and notice how sloppy and amateurish it looks? The Minify HTML options cleans up sloppy looking markup and minifies, which also speeds up download time.', 'clearfy') . '</p></div>'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'html_minify',
				'title' => __('HTML minify', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'hint' => __('Reduces the weight of the page by removing line breaks, tabs, spaces, etc.', 'clearfy') . '<br><br><b>Clearfy: </b>' . __('Minify pages.', 'clearfy'),
				'default' => false,
				'eventsOn' => array(
					'show' => '#wbcr-clearfy-performance-html-minify-options'
				),
				'eventsOff' => array(
					'hide' => '#wbcr-clearfy-performance-html-minify-options'
				)
			);

			$options[] = array(
				'type' => 'separator'
			);

			$sub_options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'minify_javascript',
				'title' => __('Minify inline JavaScript', 'clearfy'),
				'default' => true
			);

			$sub_options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'minify_html_comments',
				'title' => __('Remove HTML, JavaScript and CSS comments', 'clearfy'),
				'default' => true
			);

			$sub_options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'minify_html_xhtml',
				'title' => __('Remove XHTML closing tags from HTML5 void elements', 'clearfy'),
				'default' => false
			);

			$sub_options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'minify_html_relative',
				'title' => __('Remove relative domain from internal URLs', 'clearfy'),
				'default' => false
			);

			$sub_options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'minify_html_scheme',
				'title' => __('Remove schemes (HTTP: and HTTPS:) from all URLs', 'clearfy'),
				'default' => false
			);

			$sub_options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'minify_html_utf8',
				'title' => __('Support multi-byte UTF-8 encoding (if you see odd characters)', 'clearfy'),
				'default' => in_array(get_locale(), array('ru_RU', 'bel', 'kk', 'hy', 'uk', 'bg', 'bg_BG', 'ka_GE'))
					? true
					: false
			);

			$options[] = array(
				'type' => 'div',
				'id' => 'wbcr-clearfy-performance-html-minify-options',
				'items' => $sub_options
			);

			$form_options = array();

			$form_options[] = array(
				'type' => 'form-group',
				'items' => $options,
				//'cssClass' => 'postbox'
			);

			return apply_filters('wbcr_clr_code_clean_form_options', $form_options, $this);
		}
	}