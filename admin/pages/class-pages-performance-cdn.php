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

class WCL_PerformanceCDNPage extends WCL_Page {

	/**
	 * The id of the page in the admin menu.
	 *
	 * Mainly used to navigate between pages.
	 * @see FactoryPages000_AdminPage
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $id = "performance_cdn";

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

	public $available_for_multisite = true;

	/**
	 * @param WCL_Plugin $plugin
	 */
	public function __construct(WCL_Plugin $plugin)
	{
		$this->menu_title = __('CDN', 'clearfy');

		parent::__construct($plugin);

		$this->plugin = $plugin;
	}

	/*public function afterFormSave()
	{
		$ga_cache = $this->getPopulateOption('ga_cache');
		$ga_caos_remove_wp_cron = $this->getPopulateOption('ga_caos_remove_wp_cron');

		if( $ga_cache ) {
			if( !$ga_caos_remove_wp_cron ) {
				if( !wp_next_scheduled('wbcr_clearfy_update_local_ga') ) {
					wp_schedule_event(time(), 'daily', 'wbcr_clearfy_update_local_ga');
				}

				return;
			}
		}

		if( (!$ga_cache || $ga_caos_remove_wp_cron) && wp_next_scheduled('wbcr_clearfy_update_local_ga') ) {
			wp_clear_scheduled_hook('wbcr_clearfy_update_local_ga');
		}
	}*/

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
			'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>CDN general settings</strong>.', 'clearfy') . '<p>' . __('Google Fonts and Maps strongly affect your website loading speed. Use settings below to disable or optimize Google fonts and Maps.', 'clearfy') . '</p></div>'
		);

		$options[] = array(
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'enable_cdn',
			'title' => __('Enable CDN', 'clearfy'),
			'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
			'hint' => __('Enable CDN.', 'clearfy'),
			'default' => false
		);

		$options[] = array(
			'type' => 'textbox',
			'name' => 'cdn_cname',
			'title' => __('Specify the CNAME(s) below', 'clearfy'),
			'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
			'hint' => __('All URLs of static files (CSS, JS, images) will be rewritten to the CNAME(s) you provide.', 'clearfy'),
			'default' => ''
		);
		$options[] = [
			'type' => 'dropdown',
			'way' => 'buttons',
			'name' => 'cdn_zone',
			'data' => [
				['all', __('All files', 'clearfy')],
				['css_and_js', __('CSS ans Javascripts', 'clearfy')],
				['js', __('Javascripts', 'clearfy')],
				['css', __('CSS', 'clearfy')],
				['images', __('Images', 'clearfy')],
			],
			'title' => __('File types', 'clearfy'),
			'hint' => __('Types of files that will be uploaded through the provider you have installed cdn.', 'clearfy'),
			//'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
			'default' => 'all'
		];
		$options[] = array(
			'type' => 'html',
			'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Bunny CDN settings</strong>.', 'clearfy') . '<p>' . __('Google Fonts and Maps strongly affect your website loading speed. Use settings below to disable or optimize Google fonts and Maps.', 'clearfy') . '</p></div>'
		);
		$options[] = array(
			'type' => 'textbox',
			'name' => 'bunny_cdn_api_key',
			'title' => __('Bunny CDN API Key (Optional):', 'clearfy'),
			'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
			'hint' => __('The bunny.net API key to manage the zone. Adding this will enable features such as cache purging. You can find the key in your account settings.', 'clearfy'),
			'default' => ''
		);
		$options[] = array(
			'type' => 'html',
			'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Cdn77 settings</strong>.', 'clearfy') . '<p>' . __('Google Fonts and Maps strongly affect your website loading speed. Use settings below to disable or optimize Google fonts and Maps.', 'clearfy') . '</p></div>'
		);
		$options[] = array(
			'type' => 'textbox',
			'name' => 'cdn77_api_login',
			'title' => __('Login (Optional):', 'clearfy'),
			'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
			'hint' => __('The cdn77.com API key to manage. Adding this will enable features such as cache purging. You can find the key in your account settings.', 'clearfy'),
			'default' => ''
		);
		$options[] = array(
			'type' => 'textbox',
			'name' => 'cdn77_api_cdnid',
			'title' => __('CDN ID (Optional):', 'clearfy'),
			'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
			'hint' => __('The cdn77.com API key to manage. Adding this will enable features such as cache purging. You can find the key in your account settings.', 'clearfy'),
			'default' => ''
		);
		$options[] = array(
			'type' => 'textbox',
			'name' => 'cdn77_api_key',
			'title' => __('API Key (Optional):', 'clearfy'),
			'layout' => array('hint-type' => 'icon', 'hint-icon-color' => 'grey'),
			'hint' => __('The cdn77.com API key to manage. Adding this will enable features such as cache purging. You can find the key in your account settings.', 'clearfy'),
			'default' => ''
		);

		$form_options = array();

		$form_options[] = array(
			'type' => 'form-group',
			'items' => $options,
			//'cssClass' => 'postbox'
		);

		return apply_filters('wclearfy/performance/cdn_form', $form_options, $this);
	}
}
