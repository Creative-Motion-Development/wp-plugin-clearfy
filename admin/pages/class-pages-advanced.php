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

class WCL_AdvancedPage extends WCL_Page {

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

	/**
	 * Permalinks options.
	 *
	 * @return mixed[]
	 * @since 1.0.0
	 */
	public function getPageOptions()
	{

		$options = array();

		$options[] = array(
			'type' => 'html',
			'html' => '<div class="wbcr-clearfy-group-header">' . '<strong>' . __('Other', 'clearfy') . '</strong>' . '<p>' . __('In this group of settings, you can manage the adminbar.', 'clearfy') . '</p>' . '</div>'
		);

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'disable_feed',
			'title' => __('Disable RSS feeds', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('By default, WordPress generates all types of different RSS feeds for your site. While RSS feeds can be useful if you are running a blog, businesses might not always utilize these. Not every site out there has a blog.', 'clearfy') . '<br><b>Clearfy: </b>' . sprintf(__('Removes a link to the RSS-feed from the %s section, closes and puts the redirect from all RSS-feeds.', 'clearfy'), '&lt;head&gt;'),
			'default' => false,
			'eventsOn' => [
				'show' => '.factory-control-disabled_feed_behaviour'
			],
			'eventsOff' => [
				'hide' => '.factory-control-disabled_feed_behaviour'
			]
		];

		$options[] = [
			'type' => 'dropdown',
			'way' => 'buttons',
			'name' => 'disabled_feed_behaviour',
			'data' => [
				['redirect_301', __('Redirect 301', 'clearfy')],
				['redirect_404', __('Page 404', 'clearfy')],
			],
			'title' => __('Redirect feed requests', 'clearfy'),
			'hint' => __('Forward all requests to page 404 or to the main page through 301 redirects.', 'clearfy'),
			//'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
			'default' => 'redirect_301'
		];

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
		}

		$options[] = [
			'type' => 'html',
			'html' => '<div class="wbcr-clearfy-group-header">' . '<strong>' . __('Classic editor and Gutenberg', 'clearfy') . '</strong>' . '<p>' . __('In this group of options, you can manage revisions and post autosave.', 'clearfy') . '</p>' . '</div>'
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'revisions_disable',
			'title' => __('Disable revision', 'clearfy'),
			'default' => false,
			'eventsOn' => [
				'hide' => '.factory-control-revision_limit'
			],
			'eventsOff' => [
				'show' => '.factory-control-revision_limit'
			],
		];

		$options[] = [
			'type' => 'dropdown',
			'name' => 'revision_limit',
			'title' => __('Limit Post Revisions', 'clearfy'),
			'data' => [
				['default', __('Wordpress default', 'clearfy')],
				['15', '15 ' . __('revisions', 'clearfy')],
				['20', '20 ' . __('revisions', 'clearfy')],
				['25', '25 ' . __('revisions', 'clearfy')],
				['30', '30 ' . __('revisions', 'clearfy')],
				['35', '35 ' . __('revisions', 'clearfy')],
				['40', '40 ' . __('revisions', 'clearfy')],
				['45', '45 ' . __('revisions', 'clearfy')],
				['50', '50 ' . __('revisions', 'clearfy')],
				['55', '55 ' . __('revisions', 'clearfy')],
				['60', '60 ' . __('revisions', 'clearfy')]
			],
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('WordPress automatically saves revisions when you are working on posts and pages. These can begin to add up pretty quick. By default, there is no limit in place. We have seen posts with over 1,000 revisions. Multiply this by 50 pages and suddenly you have over 50,000 revisions sitting in your database. The problem with this is that you will most likely never use them and they can start slowing down your database as well as using disk space.
So we recommend either disabling or limiting your revisions. ', 'clearfy'),
			'default' => 'default'
		];

		if( version_compare(get_bloginfo('version'), '5.0', '>=') ) {
			$options[] = [
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'gutenberg_autosave_control',
				'title' => __('Gutenberg autosave control', 'clearfy'),
				'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
				'hint' => __('By activating this option autosave feature in the Gutenberg editor will be disabled. Alternatively it also provides options in the editor to select a longer autosave interval time than the default 10 seconds.', 'clearfy'),
				'default' => false
			];
		} else {
			$options[] = [
				'type' => 'checkbox',
				'way' => 'buttons',
				'name' => 'disable_post_autosave',
				'title' => __('Disable autosave', 'clearfy'),
				'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
				'hint' => __('WordPress by default automatically saves a draft every 60 seconds (1 minute). There are reasons why you might want to change this.', 'clearfy') . '<br><b>Clearfy</b>: ' . __('Disables automatic saving of drafts.', 'clearfy'),
				'default' => false
			];
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
