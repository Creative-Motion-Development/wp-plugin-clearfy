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
	public $id = "clearfy_cache";

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

	/**
	 * @param WCL_Plugin $plugin
	 */
	public function __construct(WCL_Plugin $plugin)
	{
		$this->menu_title = __('Cache', 'clearfy');
		$this->page_menu_short_description = __('Cache pages', 'clearfy');

		if( $plugin->premium->is_activate() && $plugin->premium->is_install_package() && !WCL_Plugin::app()->isActivateComponent('cache') ) {
			$this->type = 'page';
		}

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
			'html' => '<div class="wbcr-factory-page-group-header">' . __('<strong>Cache settings</strong>', 'clearfy') . '<p>' . __('A very fast caching engine for WordPress that produces static html files. You can configure caching in this section.', 'clearfy') . '</p></div>'
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'enable_cache',
			'title' => __('Enable cache', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'green'],
			'hint' => __('This option enable cache to generates static html files from your dynamic WordPress blog. After a html file is generated your webserver will serve that file instead of processing the comparatively heavier and more expensive WordPress PHP scripts.', 'clearfy'),
			'cssClass' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['factory-checkbox-disabled wbcr-factory-clearfy-icon-pro'] : [],
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'cache_mobile',
			'title' => __('Mobile', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __("Don't show the cached version for desktop to mobile devices", 'clearfy'),
			'cssClass' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['factory-checkbox-disabled wbcr-factory-clearfy-icon-pro'] : [],
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'cache_mobile_theme',
			'title' => __('Create cache for mobile theme', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('If you use a mobile theme, you should enable both “Mobile” and “Create cache for mobile theme” options. If you use a responsive theme, no need to use the mobile cache feature. You should disable “Mobile” and “Create cache for mobile theme” options.', 'clearfy'),
			'cssClass' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['factory-checkbox-disabled wbcr-factory-clearfy-icon-pro'] : [],
			'default' => false,
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'widget_cache',
			'title' => __('Widget Cache', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('You can reduce the number of sql queries with this feature.

When “Cache System” is enabled, the page is saved as a static html file, thus PHP and MySQL does not work for the page which has been cached. MySQL and PHP work to generate the html of the other pages which have not been cached yet.

Every time before the cache is created, the same widgets are generated again and again. This feature avoids generating the widgets again and again to reduce the sql queries.', 'clearfy'),
			'cssClass' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['factory-checkbox-disabled wbcr-factory-clearfy-icon-pro'] : [],
			'default' => false,
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'preload_cache',
			'title' => __('Preload cache', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('The preload feature stars to work after delete cache.

When the Preload feature calls the urls, the cache of urls are created automatically. When all the pages are cached, the preload stops working. When the cache is clear, it starts working again.

The Preload runs every 5 minutes. If you want set a specific interval. Note: The preload feature works with the WP_CRON system.', 'clearfy'),
			'cssClass' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['factory-checkbox-disabled wbcr-factory-clearfy-icon-pro'] : [],
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'dont_cache_for_logged_in_users',
			'title' => __('Don\'t cache for logged-in users', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'green'],
			'hint' => __('Don\'t show the cached version for logged-in users', 'clearfy'),
			'cssClass' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['factory-checkbox-disabled wbcr-factory-clearfy-icon-pro'] : [],
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'clear_cache_for_newpost',
			'title' => __('Clear cache for new post', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Clear cache files when a post or page is published', 'clearfy'),
			'cssClass' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['factory-checkbox-disabled wbcr-factory-clearfy-icon-pro'] : [],
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'clear_cache_for_updated_post',
			'title' => __('Clear cache for updated Post', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Clear cache files when a post or page is updated', 'clearfy'),
			'cssClass' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['factory-checkbox-disabled wbcr-factory-clearfy-icon-pro'] : [],
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'gzip',
			'title' => __('Gzip', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'green'],
			'hint' => __('Reduce the size of page decrease the page load time a lot. You can reduce the size of page with GZIP compression feature.

If the size of requested files are big, loading takes time so in this case there is needed to reduce the size of files. Gzip Compression feature compresses the pages and resources before sending so the transfer time is reduced.', 'clearfy'),
			'cssClass' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['factory-checkbox-disabled wbcr-factory-clearfy-icon-pro'] : [],
			'default' => false
		];

		$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'browser_caching',
			'title' => __('Browser Caching', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'green'],
			'hint' => __('Reduce the load times of pages by storing commonly used files from your website on your visitors browser.

A browser loads the css, js, images resources to display the web page to the visitors. This process is always performed.

If the commonly used files are cached by browser, the visitors’ browsers do not have to load them evert time so the load times of pages are reduced.', 'clearfy'),
			'cssClass' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['factory-checkbox-disabled wbcr-factory-clearfy-icon-pro'] : [],
			'default' => false
		];

		$options[] = [
			'type' => 'textarea',
			'name' => 'exclude_files',
			'title' => __('Filenames that can be cached', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Add here those filenames that can be cached, even if they match one of the rejected substring specified above.', 'clearfy'),
			'htmlAttrs' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['disabled' => 'disabled'] : [],
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
			'htmlAttrs' => !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ? ['disabled' => 'disabled'] : [],
			'default' => 'bot
ia_archive
slurp
crawl
spider
Yandex
'
		];

		$form_options = [];

		if( !defined('WCLEARFY_CACHEPRO_PLUGIN_ACTIVE') ) {
			foreach($options as &$option) {
				if( 'checkbox' === $option['type'] ) {
					$option['value'] = false;
				}
			}
		}

		$form_options[] = [
			'type' => 'form-group',
			'items' => $options,
			//'cssClass' => 'postbox'
		];

		return apply_filters('wbcr_clr_code_clean_form_options', $form_options, $this);
	}


	/**
	 * Содержание страницы
	 */
	public function showPageContent()
	{
		require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';
		$install_button = $this->plugin->get_install_component_button('internal', 'cache');
		$install_button->add_class('wbcr-factory-purchase-premium');
		?>
		<script>
			jQuery(document).ready(function($) {
				$.wfactory_000.hooks.add('core/components/updated', function(button, data) {
					console.log(data);

					if( data.plugin_action === 'install' ) {
						button.removeClass('wbcr-factory-purchase-premium');
						button.addClass('wbcr-factory-activate-premium');
					}

				});

				$.wfactory_000.hooks.add('core/components/activated', function(button, data) {
					button.remove();
					window.location.href = '<?php echo $this->getBaseUrl('clearfy_cache'); ?>';

				});
			});
		</script>
		<div class="wbcr-factory-clearfy-000-multisite-suggetion">
			<div class="wbcr-factory-inner-contanier">
				<h3><?php _e('Install Page Cache component', 'clearfy') ?></h3>
				<p><?php _e('To start page caching, you need to install the additional component Cache!', 'clearfy') ?></p>
				<p><?php _e('Installing the component will not take you long, just click the install button, then	activate.', 'clearfy') ?></p>
				<p style="margin-top:20px">
					<?php $install_button->render_link(); ?>
				</p>
			</div>
		</div>
		<?php
	}
}
