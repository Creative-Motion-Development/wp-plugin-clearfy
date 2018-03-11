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

	class WCL_WidgetsPage extends WCL_Page {

		/**
		 * The id of the page in the admin menu.
		 *
		 * Mainly used to navigate between pages.
		 * @see FactoryPages000_AdminPage
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $id = "widgets";

		public $page_menu_dashicon = 'dashicons-networking';

		/**
		 * @param WCL_Plugin $plugin
		 */
		public function __construct(WCL_Plugin $plugin)
		{
			$this->menu_title = __('Widgets', 'clearfy');

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
				'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Удалите неиспользуемые виджеты</strong>.', 'clearfy') . '<p>' . __('Некоторые виджеты, как, например, Календарь или Облако меток, создают по лишнему запросу к базе данных, даже если не выводятся на сайте. Конечно, пару обращений к базе данных не принесут особого вреда целому сайту. Но если ресурс размещает в себе огромное количество контента, и скорость его работы превыше всего, то в таком случае отключение неиспользуемого функционала — первый шаг к оптимизации его работы.', 'clearfy') . '</p></div>'
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_page',
				'title' => __('Remove the "Pages" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_calendar',
				'title' => __('Remove calendar widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_tag_cloud',
				'title' => __('Remove the "Cloud of tags" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_archives',
				'title' => __('Remove the "Archives" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_links',
				'title' => __('Remove the "Links" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_meta',
				'title' => __('Remove the "Meta" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_search',
				'title' => __('Remove the "Search" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_text',
				'title' => __('Remove the "Text" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_categories',
				'title' => __('Remove the "Categories" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_recent_posts',
				'title' => __('Remove the "Recent Posts" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_recent_comments',
				'title' => __('Remove the "Recent Comments" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_rss',
				'title' => __('Remove the "RSS" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_menu',
				'title' => __('Remove the "Menu" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$options[] = array(
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'remove_unneeded_widget_twenty_eleven_ephemera',
				'title' => __('Remove the "Twenty Eleven Ephemera" widget', 'clearfy'),
				'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
				'default' => false
			);

			$form_options = array();

			$form_options[] = array(
				'type' => 'form-group',
				'items' => $options,
				//'cssClass' => 'postbox'
			);

			return apply_filters('wbcr_clr_seo_form_options', $form_options);
		}
	}