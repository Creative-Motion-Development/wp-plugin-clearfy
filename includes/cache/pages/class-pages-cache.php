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

class WCL_CachePage extends WCL_Page {

	/**
	 * @see {@inheritDoc}
	 *
	 * @var string
	 */
	public $id = "cache";

	/**
	 * @var string
	 */
	public $page_parent_page = 'performance';

	/**
	 * @see {@inheritDoc}
	 *
	 * @var string
	 */
	public $page_menu_dashicon = 'dashicons-performance';

	/**
	 * @see {@inheritDoc}
	 *
	 * @var int
	 */
	public $page_menu_position = 20;

	/**
	 * @see {@inheritDoc}
	 *
	 * @var bool
	 */
	public $available_for_multisite = true;

	protected $errors = [
		'server_is_not_support' => "<label>.htaccess was not found</label> <a target='_blank' href='http://www.wpfastestcache.com/warnings/htaccess-was-not-found/'>Read More</a>",
		'need_constant' => "define('WP_CACHE', true); is needed to be added into wp-config.php",
		'need_permalinks_structure' => "You have to set <strong><u><a href=" . "'/wp-admin/options-permalink.php'>permalinks</a></u></strong>",
		'fast_velocity_plugin_needs_deactivated' => "Fast Velocity Minify needs to be deactivated",
		'future_expiration_plugin_need_be_deactivated' => 'Far Future Expiration Plugin needs to be deactivated',
		'sg_optimizer_plugin_need_be_deactivated' => "SG Optimizer needs to be deactived",
		'adrotate_plugin_need_be_deactivated' => "AdRotate needs to be deactived",
		'mobilepress_plugin_need_be_deactivated' => "MobilePress needs to be deactived",
		'speed_booster_pack_plugin_need_be_deactivated' => "Speed Booster Pack needs to be deactived",
		'wp_performance_score_booster_plugin_need_be_deactivated' => "WP Performance Score Booster needs to be deactivated<br>This plugin has aldready Gzip, Leverage Browser Caching features",
		'check_and_enable_gzip_compression_plugin_need_be_deactivated' => "Check and Enable GZIP compression needs to be deactivated<br>This plugin has aldready Gzip feature",
		'gzippy_plugin_need_be_deactivated' => "GZippy needs to be deactivated<br>This plugin has aldready Gzip feature",
		'gzip_ninja_speed_compression_plugin_need_be_deactivated' => "GZip Ninja Speed Compression needs to be deactivated<br>This plugin has aldready Gzip feature",
		'wordpress_gzip_compression_plugin_need_be_deactivated' => "WordPress Gzip Compression needs to be deactivated<br>This plugin has aldready Gzip feature",
		'gzip_output_needs_plugin_need_be_deactivated' => "GZIP Output needs to be deactivated<br>This plugin has aldready Gzip feature",
		'head_cleaner_plugin_need_be_deactivated' => "Head Cleaner needs to be deactivated",
		'far_future_expiration_plugin_need_be_deactivated' => "Far Future Expiration Plugin needs to be deactivated",
	];

	/**
	 * @param WCL_Plugin $plugin
	 */
	public function __construct(WCL_Plugin $plugin)
	{
		$this->menu_title = __('Cache', 'clearfy');
		$this->page_menu_short_description = __('Optimization js, css, fonts', 'clearfy');

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
		$options = [];

		$options[] = [
			'type' => 'html',
			'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Cache settings</strong>', 'clearfy') . '<p>' . __('This set of settings will help you remove unnecessary links and code from the head section, as well as reduce your website\'s pages weight.', 'clearfy') . '</p></div>'
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'enable_cache',
			'title' => __('Enable cache', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('This option enable cache to generates static html files from your dynamic WordPress blog. After a html file is generated your webserver will serve that file instead of processing the comparatively heavier and more expensive WordPress PHP scripts.', 'clearfy'),
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'cache_mobile',
			'title' => __('Mobile', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __("Don't show the cached version for desktop to mobile devices", 'clearfy'),
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'cache_mobile_theme',
			'title' => __('Create cache for mobile theme', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Reduce the number of SQL queries', 'clearfy'),
			'default' => false,
			'cssClass' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['factory-checkbox-disabled wbcr-factory-clearfy-icon-pro'] : [],
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'widget_cache',
			'title' => __('Widget Cache', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Reduce the number of SQL queries', 'clearfy'),
			'default' => false,
			'cssClass' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['factory-checkbox-disabled wbcr-factory-clearfy-icon-pro'] : [],
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'preload_cache',
			'title' => __('Preload cache', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Create the cache of all the site automatically', 'clearfy'),
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'dont_cache_for_logged_in_users',
			'title' => __('Don\'t cache for logged-in users', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Don\'t show the cached version for logged-in users', 'clearfy'),
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'clear_cache_for_newpost',
			'title' => __('Clear cache for new post', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Clear cache files when a post or page is published', 'clearfy'),
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'clear_cache_for_updated_post',
			'title' => __('Clear cache for updated Post', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Clear cache files when a post or page is updated', 'clearfy'),
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'gzip',
			'title' => __('Gzip', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Reduce the size of files sent from your server', 'clearfy'),
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'browser_caching',
			'title' => __('Browser Caching', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Reduce page load times for repeat visitors', 'clearfy'),
			'default' => false
		];

		$options[] = [
			'type' => 'textarea',
			'name' => 'exclude_files',
			'title' => __('Filenames that can be cached', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Add here those filenames that can be cached, even if they match one of the rejected substring specified above.', 'clearfy'),
			'default' => 'wp-comments-popup.php
wp-links-opml.php
wp-locations.php
'
		];
		$options[] = [
			'type' => 'textarea',
			'name' => 'exclude_pages',
			'title' => __('Rejected User Agents', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Strings in the HTTP ’User Agent’ header that prevent WP-Cache from caching bot, spiders, and crawlers’ requests. Note that super cached files are still sent to these agents if they already exists.', 'clearfy'),
			'default' => 'bot
ia_archive
slurp
crawl
spider
Yandex
'
		];

		$form_options = [];

		$form_options[] = [
			'type' => 'form-group',
			'items' => $options,
			//'cssClass' => 'postbox'
		];

		return apply_filters('wbcr_clr_code_clean_form_options', $form_options, $this);
	}

	/**
	 * We register notifications for some actions
	 *
	 * @param                        $notices
	 * @param \Wbcr_Factory000_Plugin $plugin
	 *
	 * @return array
	 * @see libs\factory\pages\themplates\FactoryPages000_ImpressiveThemplate
	 */
	public function getActionNotices($notices)
	{

		$notices[] = [
			'conditions' => [
				'wclearfy-cache-cleared' => 1
			],
			'type' => 'success',
			'message' => 'Cache has been cleared!'
		];

		foreach($this->errors as $key => $error_message) {
			$notices[] = [
				'conditions' => [
					'wclearfy-cache-error' => $key
				],
				'type' => 'danger',
				'message' => $error_message
			];
		}

		return $notices;
	}

	public function afterFormSave()
	{
		require_once WCL_PLUGIN_DIR . '/includes/cache/includes/helpers.php';
		try {
			WCL_Cache_Helpers::modifyHtaccess();
		} catch( Exception $e ) {
			if( !empty($e->getCode()) && isset($this->errors[$e->getCode()]) ) {
				$this->redirectToAction('index', ['wclearfy-cache-error' => $e->getCode()]);
			}
		}
	}
}
