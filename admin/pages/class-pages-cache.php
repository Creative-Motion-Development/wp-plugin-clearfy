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
		'need_permalinks_structure' => "You have to set <strong><u><a href='/wp-admin/options-permalink.php" . "'>permalinks</a></u></strong>",
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

		/*$options[] = [
			'type' => 'checkbox',
			'way' => 'buttons',
			'name' => 'preload_cache',
			'title' => __('Preload', 'clearfy'),
			'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'grey'],
			'hint' => __('Create the cache of all the site automatically', 'clearfy'),
			'default' => false
		];*/

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

	public function deleteCacheAction()
	{
		$this->deleteCache();

		$this->redirectToAction('index', ['wclearfy-cache-cleared' => 1]);
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

		$path = ABSPATH;
		if( $this->is_subdirectory_install() ) {
			$path = $this->getABSPATH();
		}

		// if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && preg_match("/iis/i", $_SERVER["SERVER_SOFTWARE"])){
		// 	return array("The plugin does not work with Microsoft IIS. Only with Apache", "error");
		// }

		// if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"])){
		// 	return array("The plugin does not work with Nginx. Only with Apache", "error");
		// }

		if( !file_exists($path . ".htaccess") ) {
			if( isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && (preg_match("/iis/i", $_SERVER["SERVER_SOFTWARE"]) || preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"])) ) {
				//
			} else {
				$this->redirectToAction('index', ['wclearfy-cache-error' => 'server_is_not_support']);
			}
		}

		if( $this->isPluginActive('wp-postviews/wp-postviews.php') ) {
			$wp_postviews_options = get_option("views_options");
			$wp_postviews_options["use_ajax"] = true;
			update_option("views_options", $wp_postviews_options);

			if( !WP_CACHE ) {
				if( $wp_config = @file_get_contents(ABSPATH . "wp-config.php") ) {
					$wp_config = str_replace("\$table_prefix", "define('WP_CACHE', true);\n\$table_prefix", $wp_config);

					if( !@file_put_contents(ABSPATH . "wp-config.php", $wp_config) ) {
						$this->redirectToAction('index', ['wclearfy-cache-error' => 'need_constant']);
					}
				} else {
					$this->redirectToAction('index', ['wclearfy-cache-error' => 'need_constant']);
				}
			}
		}

		$htaccess = @file_get_contents($path . ".htaccess");

		// if(defined('DONOTCACHEPAGE')){
		// 	return array("DONOTCACHEPAGE <label>constant is defined as TRUE. It must be FALSE</label>", "error");
		// }else

		if( !get_option('permalink_structure') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'need_permalinks_structure']);
			//} else if( $res = $this->checkSuperCache($path, $htaccess) ) {
			//return $res;
		} else if( $this->isPluginActive('fast-velocity-minify/fvm.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'fast_velocity_plugin_needs_deactivated']);
		} else if( $this->isPluginActive('far-future-expiration/far-future-expiration.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'future_expiration_plugin_need_be_deactivated']);
		} else if( $this->isPluginActive('sg-cachepress/sg-cachepress.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'sg_optimizer_plugin_needs_deactivated']);
		} else if( $this->isPluginActive('adrotate/adrotate.php') || $this->isPluginActive('adrotate-pro/adrotate.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'adrotate_plugin_needs_deactivated']);
		} else if( $this->isPluginActive('mobilepress/mobilepress.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'mobilepress_plugin_need_be_deactivated']);
		} else if( $this->isPluginActive('speed-booster-pack/speed-booster-pack.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'speed_booster_pack_plugin_need_be_deactivated']);
			//} else if( $this->isPluginActive('cdn-enabler/cdn-enabler.php') ) {
			//return array("CDN Enabler needs to be deactivated<br>This plugin has aldready CDN feature", "error");
		} else if( $this->isPluginActive('wp-performance-score-booster/wp-performance-score-booster.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'speed_booster_pack_plugin_need_be_deactivated']);
			//} else if( $this->isPluginActive('bwp-minify/bwp-minify.php') ) {
			//return array(
			//"Better WordPress Minify needs to be deactivated<br>This plugin has aldready Minify feature",
			//"error"
			//);
		} else if( $this->isPluginActive('check-and-enable-gzip-compression/richards-toolbox.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'check_and_enable_gzip_compression_plugin_need_be_deactivated']);
		} else if( $this->isPluginActive('gzippy/gzippy.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'gzippy_plugin_need_be_deactivated']);
		} else if( $this->isPluginActive('gzip-ninja-speed-compression/gzip-ninja-speed.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'gzip_ninja_speed_compression_plugin_need_be_deactivated']);
		} else if( $this->isPluginActive('wordpress-gzip-compression/ezgz.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'wordpress_gzip_compression_plugin_need_be_deactivated']);
		} else if( $this->isPluginActive('filosofo-gzip-compression/filosofo-gzip-compression.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'gzip_output_needs_plugin_need_be_deactivated']);
		} else if( $this->isPluginActive('head-cleaner/head-cleaner.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'head_cleaner_plugin_need_be_deactivated']);
		} else if( $this->isPluginActive('far-future-expiry-header/far-future-expiration.php') ) {
			$this->redirectToAction('index', ['wclearfy-cache-error' => 'far_future_expiration_plugin_need_be_deactivated']);
		} else if( is_writable($path . ".htaccess") ) {
			$htaccess = $this->insertLBCRule($htaccess);
			$htaccess = $this->insertGzipRule($htaccess);
			$htaccess = $this->insertRewriteRule($htaccess);

			$htaccess = $this->to_move_gtranslate_rules($htaccess);

			file_put_contents($path . ".htaccess", $htaccess);
		}

		$dd = 'fd';
	}

	public function is_subdirectory_install()
	{
		if( strlen(site_url()) > strlen(home_url()) ) {
			return true;
		}

		return false;
	}

	public function getABSPATH()
	{
		$path = ABSPATH;
		$siteUrl = site_url();
		$homeUrl = home_url();
		$diff = str_replace($homeUrl, "", $siteUrl);
		$diff = trim($diff, "/");

		$pos = strrpos($path, $diff);

		if( $pos !== false ) {
			$path = substr_replace($path, "", $pos, strlen($diff));
			$path = trim($path, "/");
			$path = "/" . $path . "/";
		}

		return $path;
	}

	public function isPluginActive($plugin)
	{
		return in_array($plugin, (array)get_option('active_plugins', array())) || $this->isPluginActiveForNetwork($plugin);
	}

	public function isPluginActiveForNetwork($plugin)
	{
		if( !is_multisite() ) {
			return false;
		}

		$plugins = get_site_option('active_sitewide_plugins');
		if( isset($plugins[$plugin]) ) {
			return true;
		}

		return false;
	}

	public function checkSuperCache($path, $htaccess)
	{
		if( $this->isPluginActive('wp-super-cache/wp-cache.php') ) {
			return array("WP Super Cache needs to be deactive", "error");
		} else {
			@unlink($path . "wp-content/wp-cache-config.php");

			$message = "";

			if( is_file($path . "wp-content/wp-cache-config.php") ) {
				$message .= "<br>- be sure that you removed /wp-content/wp-cache-config.php";
			}

			if( preg_match("/supercache/", $htaccess) ) {
				$message .= "<br>- be sure that you removed the rules of super cache from the .htaccess";
			}

			return $message ? array(
				"WP Super Cache cannot remove its own remnants so please follow the steps below" . $message,
				"error"
			) : "";
		}

		return "";
	}

	public function warningIncompatible($incompatible, $alternative = false)
	{
		if( $alternative ) {
			return array(
				$incompatible . " <label>needs to be deactive</label><br><label>We advise</label> <a id='alternative-plugin' target='_blank' href='" . $alternative["url"] . "'>" . $alternative["name"] . "</a>",
				"error"
			);
		} else {
			return array($incompatible . " <label>needs to be deactive</label>", "error");
		}
	}

	public function insertLBCRule($htaccess)
	{
		$browser_caching = $this->getPopulateOption('browser_caching');

		if( $browser_caching ) {

			$data = "# BEGIN LBCWpFastestCache" . "\n" . '<FilesMatch "\.(webm|ogg|mp4|ico|pdf|flv|jpg|jpeg|png|gif|webp|js|css|swf|x-html|css|xml|js|woff|woff2|otf|ttf|svg|eot)(\.gz)?$">' . "\n" . '<IfModule mod_expires.c>' . "\n" . 'AddType application/font-woff2 .woff2' . "\n" . 'AddType application/x-font-opentype .otf' . "\n" . 'ExpiresActive On' . "\n" . 'ExpiresDefault A0' . "\n" . 'ExpiresByType video/webm A10368000' . "\n" . 'ExpiresByType video/ogg A10368000' . "\n" . 'ExpiresByType video/mp4 A10368000' . "\n" . 'ExpiresByType image/webp A10368000' . "\n" . 'ExpiresByType image/gif A10368000' . "\n" . 'ExpiresByType image/png A10368000' . "\n" . 'ExpiresByType image/jpg A10368000' . "\n" . 'ExpiresByType image/jpeg A10368000' . "\n" . 'ExpiresByType image/ico A10368000' . "\n" . 'ExpiresByType image/svg+xml A10368000' . "\n" . 'ExpiresByType text/css A10368000' . "\n" . 'ExpiresByType text/javascript A10368000' . "\n" . 'ExpiresByType application/javascript A10368000' . "\n" . 'ExpiresByType application/x-javascript A10368000' . "\n" . 'ExpiresByType application/font-woff2 A10368000' . "\n" . 'ExpiresByType application/x-font-opentype A10368000' . "\n" . 'ExpiresByType application/x-font-truetype A10368000' . "\n" . '</IfModule>' . "\n" . '<IfModule mod_headers.c>' . "\n" . 'Header set Expires "max-age=A10368000, public"' . "\n" . 'Header unset ETag' . "\n" . 'Header set Connection keep-alive' . "\n" . 'FileETag None' . "\n" . '</IfModule>' . "\n" . '</FilesMatch>' . "\n" . "# END LBCWpFastestCache" . "\n";

			if( !preg_match("/BEGIN\s*LBCWpFastestCache/", $htaccess) ) {
				return $data . $htaccess;
			} else {
				return $htaccess;
			}
		} else {
			//delete levere browser caching
			$htaccess = preg_replace("/#\s?BEGIN\s?LBCWpFastestCache.*?#\s?END\s?LBCWpFastestCache/s", "", $htaccess);

			return $htaccess;
		}
	}

	public function insertGzipRule($htaccess)
	{
		$gzip = $this->getPopulateOption('gzip');

		if( $gzip ) {
			$data = "# BEGIN GzipWpFastestCache" . "\n" . "<IfModule mod_deflate.c>" . "\n" . "AddType x-font/woff .woff" . "\n" . "AddType x-font/ttf .ttf" . "\n" . "AddOutputFilterByType DEFLATE image/svg+xml" . "\n" . "AddOutputFilterByType DEFLATE text/plain" . "\n" . "AddOutputFilterByType DEFLATE text/html" . "\n" . "AddOutputFilterByType DEFLATE text/xml" . "\n" . "AddOutputFilterByType DEFLATE text/css" . "\n" . "AddOutputFilterByType DEFLATE text/javascript" . "\n" . "AddOutputFilterByType DEFLATE application/xml" . "\n" . "AddOutputFilterByType DEFLATE application/xhtml+xml" . "\n" . "AddOutputFilterByType DEFLATE application/rss+xml" . "\n" . "AddOutputFilterByType DEFLATE application/javascript" . "\n" . "AddOutputFilterByType DEFLATE application/x-javascript" . "\n" . "AddOutputFilterByType DEFLATE application/x-font-ttf" . "\n" . "AddOutputFilterByType DEFLATE x-font/ttf" . "\n" . "AddOutputFilterByType DEFLATE application/vnd.ms-fontobject" . "\n" . "AddOutputFilterByType DEFLATE font/opentype font/ttf font/eot font/otf" . "\n" . "</IfModule>" . "\n";

			if( defined("WPFC_GZIP_FOR_COMBINED_FILES") && WPFC_GZIP_FOR_COMBINED_FILES ) {
				$data = $data . "\n" . '<FilesMatch "\d+index\.(css|js)(\.gz)?$">' . "\n" . "# to zip the combined css and js files" . "\n\n" . "RewriteEngine On" . "\n" . "RewriteCond %{HTTP:Accept-encoding} gzip" . "\n" . "RewriteCond %{REQUEST_FILENAME}\.gz -s" . "\n" . "RewriteRule ^(.*)\.(css|js) $1\.$2\.gz [QSA]" . "\n\n" . "# to revent double gzip and give the correct mime-type" . "\n\n" . "RewriteRule \.css\.gz$ - [T=text/css,E=no-gzip:1,E=FORCE_GZIP]" . "\n" . "RewriteRule \.js\.gz$ - [T=text/javascript,E=no-gzip:1,E=FORCE_GZIP]" . "\n" . "Header set Content-Encoding gzip env=FORCE_GZIP" . "\n" . "</FilesMatch>" . "\n";
			}

			$data = $data . "# END GzipWpFastestCache" . "\n";

			$htaccess = preg_replace("/\s*\#\s?BEGIN\s?GzipWpFastestCache.*?#\s?END\s?GzipWpFastestCache\s*/s", "", $htaccess);

			return $data . $htaccess;
		} else {
			//delete gzip rules
			$htaccess = preg_replace("/\s*\#\s?BEGIN\s?GzipWpFastestCache.*?#\s?END\s?GzipWpFastestCache\s*/s", "", $htaccess);

			return $htaccess;
		}
	}

	public function insertRewriteRule($htaccess)
	{
		$enable_cache = $this->getPopulateOption('enable_cache');

		if( $enable_cache ) {
			$htaccess = preg_replace("/#\s?BEGIN\s?WpFastestCache.*?#\s?END\s?WpFastestCache/s", "", $htaccess);
			$htaccess = $this->getHtaccess() . $htaccess;
		} else {
			$htaccess = preg_replace("/#\s?BEGIN\s?WpFastestCache.*?#\s?END\s?WpFastestCache/s", "", $htaccess);
			$this->deleteCache();
		}

		return $htaccess;
	}

	public function prefixRedirect()
	{
		$forceTo = "";

		if( defined("WPFC_DISABLE_REDIRECTION") && WPFC_DISABLE_REDIRECTION ) {
			return $forceTo;
		}

		if( preg_match("/^https:\/\//", home_url()) ) {
			if( preg_match("/^https:\/\/www\./", home_url()) ) {
				$forceTo = "\nRewriteCond %{HTTPS} =on" . "\n" . "RewriteCond %{HTTP_HOST} ^www." . str_replace("www.", "", $_SERVER["HTTP_HOST"]) . "\n";
			} else {
				$forceTo = "\nRewriteCond %{HTTPS} =on" . "\n" . "RewriteCond %{HTTP_HOST} ^" . str_replace("www.", "", $_SERVER["HTTP_HOST"]) . "\n";
			}
		} else {
			if( preg_match("/^http:\/\/www\./", home_url()) ) {
				$forceTo = "\nRewriteCond %{HTTP_HOST} ^" . str_replace("www.", "", $_SERVER["HTTP_HOST"]) . "\n" . "RewriteRule ^(.*)$ " . preg_quote(home_url(), "/") . "\/$1 [R=301,L]" . "\n";
			} else {
				$forceTo = "\nRewriteCond %{HTTP_HOST} ^www." . str_replace("www.", "", $_SERVER["HTTP_HOST"]) . " [NC]" . "\n" . "RewriteRule ^(.*)$ " . preg_quote(home_url(), "/") . "\/$1 [R=301,L]" . "\n";
			}
		}

		return $forceTo;
	}

	public function getHtaccess()
	{
		$mobile = "";
		$loggedInUser = "";
		$ifIsNotSecure = "";
		$trailing_slash_rule = "";
		$consent_cookie = "";

		$language_negotiation_type = apply_filters('wpml_setting', false, 'language_negotiation_type');
		if( ($language_negotiation_type == 2) && $this->isPluginActive('sitepress-multilingual-cms/sitepress.php') ) {
			$cache_path = '/cache/all/%{HTTP_HOST}/';
			$disable_condition = true;
		} else {
			$cache_path = '/cache/all/';
			$disable_condition = false;
		}

		if( isset($_POST["wpFastestCacheMobile"]) && $_POST["wpFastestCacheMobile"] == "on" ) {
			$mobile = "RewriteCond %{HTTP_USER_AGENT} !^.*(" . $this->getMobileUserAgents() . ").*$ [NC]" . "\n";

			if( isset($_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER']) ) {
				$mobile = $mobile . "RewriteCond %{HTTP_CLOUDFRONT_IS_MOBILE_VIEWER} false [NC]" . "\n";
				$mobile = $mobile . "RewriteCond %{HTTP_CLOUDFRONT_IS_TABLET_VIEWER} false [NC]" . "\n";
			}
		}

		if( isset($_POST["wpFastestCacheLoggedInUser"]) && $_POST["wpFastestCacheLoggedInUser"] == "on" ) {
			$loggedInUser = "RewriteCond %{HTTP:Cookie} !wordpress_logged_in" . "\n";
		}

		if( !preg_match("/^https/i", get_option("home")) ) {
			$ifIsNotSecure = "RewriteCond %{HTTPS} !=on";
		}

		if( $this->is_trailing_slash() ) {
			$trailing_slash_rule = "RewriteCond %{REQUEST_URI} \/$" . "\n";
		} else {
			$trailing_slash_rule = "RewriteCond %{REQUEST_URI} ![^\/]+\/$" . "\n";
		}

		$data = "# BEGIN WpFastestCache" . "\n" . "# Modified Time: " . date("d-m-y G:i:s", current_time('timestamp')) . "\n" . "<IfModule mod_rewrite.c>" . "\n" . "RewriteEngine On" . "\n" . "RewriteBase /" . "\n" . $this->ruleForWpContent() . "\n" . $this->prefixRedirect() . $this->excludeRules() . "\n" . $this->excludeAdminCookie() . "\n" . $this->http_condition_rule() . "\n" . "RewriteCond %{HTTP_USER_AGENT} !(" . $this->get_excluded_useragent() . ")" . "\n" . "RewriteCond %{HTTP_USER_AGENT} !(WP\sFastest\sCache\sPreload(\siPhone\sMobile)?\s*Bot)" . "\n" . "RewriteCond %{REQUEST_METHOD} !POST" . "\n" . $ifIsNotSecure . "\n" . "RewriteCond %{REQUEST_URI} !(\/){2}$" . "\n" . $trailing_slash_rule . "RewriteCond %{QUERY_STRING} !.+" . "\n" . $loggedInUser . $consent_cookie . "RewriteCond %{HTTP:Cookie} !comment_author_" . "\n" . //"RewriteCond %{HTTP:Cookie} !woocommerce_items_in_cart"."\n".
			"RewriteCond %{HTTP:Cookie} !safirmobilswitcher=mobil" . "\n" . 'RewriteCond %{HTTP:Profile} !^[a-z0-9\"]+ [NC]' . "\n" . $mobile;

		if( ABSPATH == "//" ) {
			$data = $data . "RewriteCond %{DOCUMENT_ROOT}/" . basename(WP_CONTENT_DIR) . $cache_path . "$1/index.html -f" . "\n";
		} else {
			//WARNING: If you change the following lines, you need to update webp as well
			$data = $data . "RewriteCond %{DOCUMENT_ROOT}/" . basename(WP_CONTENT_DIR) . $cache_path . "$1/index.html -f [or]" . "\n";
			// to escape spaces
			$tmp_WPFC_WP_CONTENT_DIR = str_replace(" ", "\ ", WP_CONTENT_DIR);

			$data = $data . "RewriteCond " . $tmp_WPFC_WP_CONTENT_DIR . $cache_path . $this->getRewriteBase(true) . "$1/index.html -f" . "\n";
		}

		$data = $data . 'RewriteRule ^(.*) "/' . $this->getRewriteBase() . basename(WP_CONTENT_DIR) . $cache_path . $this->getRewriteBase(true) . '$1/index.html" [L]' . "\n";

		//RewriteRule !/  "/wp-content/cache/all/index.html" [L]

		/*if( class_exists("WpFcMobileCache") && isset($this->options->wpFastestCacheMobileTheme) && $this->options->wpFastestCacheMobileTheme ) {
			$wpfc_mobile = new WpFcMobileCache();

			if( $this->isPluginActive('wptouch/wptouch.php') || $this->isPluginActive('wptouch-pro/wptouch-pro.php') ) {
				$wpfc_mobile->set_wptouch(true);
			} else {
				$wpfc_mobile->set_wptouch(false);
			}

			$data = $data . "\n\n\n" . $wpfc_mobile->update_htaccess($data);
		}*/

		$data = $data . "</IfModule>" . "\n" . "<FilesMatch \"index\.(html|htm)$\">" . "\n" . "AddDefaultCharset UTF-8" . "\n" . "<ifModule mod_headers.c>" . "\n" . "FileETag None" . "\n" . "Header unset ETag" . "\n" . "Header set Cache-Control \"max-age=0, no-cache, no-store, must-revalidate\"" . "\n" . "Header set Pragma \"no-cache\"" . "\n" . "Header set Expires \"Mon, 29 Oct 1923 20:30:00 GMT\"" . "\n" . "</ifModule>" . "\n" . "</FilesMatch>" . "\n" . "# END WpFastestCache" . "\n";

		if( is_multisite() ) {
			return "";
		} else {
			return preg_replace("/\n+/", "\n", $data);
		}
	}

	public function http_condition_rule()
	{
		$http_host = preg_replace("/(http(s?)\:)?\/\/(www\d*\.)?/i", "", trim(home_url(), "/"));

		if( preg_match("/\//", $http_host) ) {
			$http_host = strstr($http_host, '/', true);
		}

		if( preg_match("/www\./", home_url()) ) {
			$http_host = "www." . $http_host;
		}

		return "RewriteCond %{HTTP_HOST} ^" . $http_host;
	}

	public function ruleForWpContent()
	{
		return "";
		$newContentPath = str_replace(home_url(), "", content_url());
		if( !preg_match("/wp-content/", $newContentPath) ) {
			$newContentPath = trim($newContentPath, "/");

			return "RewriteRule ^" . $newContentPath . "/cache/(.*) " . WP_CONTENT_DIR . "/cache/$1 [L]" . "\n";
		}

		return "";
	}

	public function getRewriteBase($sub = "")
	{
		if( $sub && $this->is_subdirectory_install() ) {
			$trimedProtocol = preg_replace("/http:\/\/|https:\/\//", "", trim(home_url(), "/"));
			$path = strstr($trimedProtocol, '/');

			if( $path ) {
				return trim($path, "/") . "/";
			} else {
				return "";
			}
		}

		$url = rtrim(site_url(), "/");
		preg_match("/https?:\/\/[^\/]+(.*)/", $url, $out);

		if( isset($out[1]) && $out[1] ) {
			$out[1] = trim($out[1], "/");

			if( preg_match("/\/" . preg_quote($out[1], "/") . "\//", WP_CONTENT_DIR) ) {
				return $out[1] . "/";
			} else {
				return "";
			}
		} else {
			return "";
		}
	}

	public function deleteCache($minified = false)
	{
		//include_once('inc/cdn.php');
		//CdnWPFC::cloudflare_clear_cache();

		//$this->set_preload();

		$created_tmpWpfc = false;
		$cache_deleted = false;
		$minifed_deleted = false;

		$cache_path = $this->getWpContentDir("/cache/all");
		$minified_cache_path = $this->getWpContentDir("/cache/wpfc-minified");

		if( class_exists("WpFcMobileCache") ) {


			if( is_dir($this->getWpContentDir("/cache/wpfc-mobile-cache")) ) {
				if( is_dir($this->getWpContentDir("/cache/tmpWpfc")) ) {
					rename($this->getWpContentDir("/cache/wpfc-mobile-cache"), $this->getWpContentDir("/cache/tmpWpfc/mobile_") . time());
				} else if( @mkdir($this->getWpContentDir("/cache/tmpWpfc"), 0755, true) ) {
					rename($this->getWpContentDir("/cache/wpfc-mobile-cache"), $this->getWpContentDir("/cache/tmpWpfc/mobile_") . time());
				}
			}
		}

		if( !is_dir($this->getWpContentDir("/cache/tmpWpfc")) ) {
			if( @mkdir($this->getWpContentDir("/cache/tmpWpfc"), 0755, true) ) {
				$created_tmpWpfc = true;
			} else {
				$created_tmpWpfc = false;
				//$this->systemMessage = array("Permission of <strong>/wp-content/cache</strong> must be <strong>755</strong>", "error");
			}
		} else {
			$created_tmpWpfc = true;
		}

		//to clear widget cache path
		$this->deleteWidgetCache();

		$this->delete_multiple_domain_mapping_cache($minified);

		if( is_dir($cache_path) ) {
			if( @rename($cache_path, $this->getWpContentDir("/cache/tmpWpfc/") . time()) ) {
				delete_option("WpFastestCacheHTML");
				delete_option("WpFastestCacheHTMLSIZE");
				delete_option("WpFastestCacheMOBILE");
				delete_option("WpFastestCacheMOBILESIZE");

				$cache_deleted = true;
			}
		} else {
			$cache_deleted = true;
		}

		if( $minified ) {
			if( is_dir($minified_cache_path) ) {
				if( @rename($minified_cache_path, $this->getWpContentDir("/cache/tmpWpfc/m") . time()) ) {
					delete_option("WpFastestCacheCSS");
					delete_option("WpFastestCacheCSSSIZE");
					delete_option("WpFastestCacheJS");
					delete_option("WpFastestCacheJSSIZE");

					$minifed_deleted = true;
				}
			} else {
				$minifed_deleted = true;
			}
		} else {
			$minifed_deleted = true;
		}

		if( $created_tmpWpfc && $cache_deleted && $minifed_deleted ) {
			do_action('wpfc_delete_cache');

			//$this->notify(array("All cache files have been deleted", "updated"));

			/*if( $this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php") ) {
				include_once $this->get_premium_path("logs.php");

				$log = new WpFastestCacheLogs("delete");
				$log->action();
			}*/
			$this->rm_folder_recursively($this->getWpContentDir("/cache/tmpWpfc"));

			return true;
		} else {
			// ошибка

			/*$this->notify(array(
				"Permissions Problem: <a href='http://www.wpfastestcache.com/warnings/delete-cache-problem-related-to-permission/' target='_blank'>Read More</a>",
				"error"
			));*/
		}

		// for ajax request
		/*if( isset($_GET["action"]) && in_array($_GET["action"], array(
				"wpfc_delete_cache",
				"wpfc_delete_cache_and_minified"
			)) ) {
			die(json_encode($this->systemMessage));
		}*/
	}

	public function getWpContentDir($path = false)
	{
		/*
		Sample Paths;

		/cache/

		/cache/all/
		/cache/all
		/cache/all/page
		/cache/all/index.html

		/cache/wpfc-minified

		/cache/wpfc-widget-cache

		/cache/wpfc-mobile-cache/
		/cache/wpfc-mobile-cache/page
		/cache/wpfc-mobile-cache/index.html

		/cache/tmpWpfc
		/cache/tmpWpfc/
		/cache/tmpWpfc/mobile_
		/cache/tmpWpfc/m
		/cache/tmpWpfc/w


		/cache/testWpFc/

		/cache/all/testWpFc/

		/cache/wpfc-widget-cache/
		/cache/wpfc-widget-cache
		/cache/wpfc-widget-cache/".$args["widget_id"].".html
		*/

		if( $path ) {
			if( preg_match("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", $path) ) {
				//WPML language switch
				//https://wpml.org/forums/topic/wpml-language-switch-wp-fastest-cache-issue/
				$language_negotiation_type = apply_filters('wpml_setting', false, 'language_negotiation_type');
				if( ($language_negotiation_type == 2) && $this->isPluginActive('sitepress-multilingual-cms/sitepress.php') ) {
					$my_home_url = apply_filters('wpml_home_url', get_option('home'));
					$my_home_url = preg_replace("/https?\:\/\//i", "", $my_home_url);
					$my_home_url = trim($my_home_url, "/");

					$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/" . $my_home_url . "/$1", $path);
				} else if( ($language_negotiation_type == 1) && $this->isPluginActive('sitepress-multilingual-cms/sitepress.php') ) {
					$my_current_lang = apply_filters('wpml_current_language', null);

					if( $my_current_lang ) {
						$path = preg_replace("/\/cache\/wpfc-widget-cache\/(.+)/", "/cache/wpfc-widget-cache/" . $my_current_lang . "-" . "$1", $path);
					}
				}

				if( $this->isPluginActive('multiple-domain-mapping-on-single-site/multidomainmapping.php') ) {
					$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/" . $_SERVER['HTTP_HOST'] . "/$1", $path);
				}

				if( $this->isPluginActive('polylang/polylang.php') ) {
					$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/" . $_SERVER['HTTP_HOST'] . "/$1", $path);
				}

				if( $this->isPluginActive('multiple-domain/multiple-domain.php') ) {
					$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/" . $_SERVER['HTTP_HOST'] . "/$1", $path);
				}

				if( is_multisite() ) {
					$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/" . $_SERVER['HTTP_HOST'] . "/$1", $path);
				}
			}

			return WP_CONTENT_DIR . $path;
		} else {
			return WP_CONTENT_DIR;
		}
	}

	public function delete_multiple_domain_mapping_cache($minified = false)
	{
		//https://wordpress.org/plugins/multiple-domain-mapping-on-single-site/
		if( $this->isPluginActive("multiple-domain-mapping-on-single-site/multidomainmapping.php") ) {
			$multiple_arr = get_option('falke_mdm_mappings');

			if( isset($multiple_arr) && isset($multiple_arr["mappings"]) && isset($multiple_arr["mappings"][0]) ) {
				foreach($multiple_arr["mappings"] as $mapping_key => $mapping_value) {
					if( $minified ) {
						$mapping_domain_path = preg_replace("/(\/cache\/[^\/]+\/all\/)/", "/cache/" . $mapping_value["domain"] . "/", $this->getWpContentDir("/cache/all/"));

						if( is_dir($mapping_domain_path) ) {
							if( @rename($mapping_domain_path, $this->getWpContentDir("/cache/tmpWpfc/") . $mapping_value["domain"] . "_" . time()) ) {

							}
						}
					} else {
						$mapping_domain_path = preg_replace("/(\/cache\/[^\/]+\/all)/", "/cache/" . $mapping_value["domain"] . "/all", $this->getWpContentDir("/cache/all/index.html"));

						@unlink($mapping_domain_path);
					}
				}
			}
		}
	}

	public function deleteWidgetCache()
	{
		$widget_cache_path = $this->getWpContentDir("/cache/wpfc-widget-cache");

		if( is_dir($widget_cache_path) ) {
			if( !is_dir($this->getWpContentDir("/cache/tmpWpfc")) ) {
				if( @mkdir($this->getWpContentDir("/cache/tmpWpfc"), 0755, true) ) {
					//tmpWpfc has been created
				}
			}

			if( @rename($widget_cache_path, $this->getWpContentDir("/cache/tmpWpfc/w") . time()) ) {
				//DONE
			}
		}
	}

	public function to_move_gtranslate_rules($htaccess)
	{
		preg_match("/\#\#\#\s+BEGIN\sGTranslate\sconfig\s\#\#\#[^\#]+\#\#\#\s+END\sGTranslate\sconfig\s\#\#\#/i", $htaccess, $gtranslate);

		if( isset($gtranslate[0]) ) {
			$htaccess = preg_replace("/\#\#\#\s+BEGIN\sGTranslate\sconfig\s\#\#\#[^\#]+\#\#\#\s+END\sGTranslate\sconfig\s\#\#\#/i", "", $htaccess);
			$htaccess = $gtranslate[0] . "\n" . $htaccess;
		}

		return $htaccess;
	}

	public function is_trailing_slash()
	{
		// no need to check if Custom Permalinks plugin is active (https://tr.wordpress.org/plugins/custom-permalinks/)
		if( $this->isPluginActive("custom-permalinks/custom-permalinks.php") ) {
			return false;
		}

		if( $permalink_structure = get_option('permalink_structure') ) {
			if( preg_match("/\/$/", $permalink_structure) ) {
				return true;
			}
		}

		return false;
	}

	protected function getMobileUserAgents()
	{
		return implode("|", $this->get_mobile_browsers()) . "|" . implode("|", $this->get_operating_systems());
	}

	public function get_operating_systems()
	{
		$operating_systems = array(
			'Android',
			'blackberry|\bBB10\b|rim\stablet\sos',
			'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
			'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
			'Windows\sCE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window\sMobile|Windows\sPhone\s[0-9.]+|WCE;',
			'Windows\sPhone\s10.0|Windows\sPhone\s8.1|Windows\sPhone\s8.0|Windows\sPhone\sOS|XBLWP7|ZuneWP7|Windows\sNT\s6\.[23]\;\sARM\;',
			'\biPhone.*Mobile|\biPod|\biPad',
			'Apple-iPhone7C2',
			'MeeGo',
			'Maemo',
			'J2ME\/|\bMIDP\b|\bCLDC\b',
			// '|Java/' produces bug #135
			'webOS|hpwOS',
			'\bBada\b',
			'BREW'
		);

		return $operating_systems;
	}

	public function get_mobile_browsers()
	{
		$mobile_browsers = array(
			'\bCrMo\b|CriOS|Android.*Chrome\/[.0-9]*\s(Mobile)?',
			'\bDolfin\b',
			'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR\/[0-9.]+|Coast\/[0-9.]+',
			'Skyfire',
			'Mobile\sSafari\/[.0-9]*\sEdge',
			'IEMobile|MSIEMobile',
			// |Trident/[.0-9]+
			'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile|FxiOS',
			'bolt',
			'teashark',
			'Blazer',
			'Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari',
			'Tizen',
			'UC.*Browser|UCWEB',
			'baiduboxapp',
			'baidubrowser',
			'DiigoBrowser',
			'Puffin',
			'\bMercury\b',
			'Obigo',
			'NF-Browser',
			'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger',
			'Android.*PaleMoon|Mobile.*PaleMoon'
		);

		return $mobile_browsers;
	}

	public function rm_folder_recursively($dir, $i = 1)
	{
		if( is_dir($dir) ) {
			$files = @scandir($dir);
			foreach((array)$files as $file) {
				if( $i > 50 && !preg_match("/wp-fastest-cache-premium/i", $dir) ) {
					return true;
				} else {
					$i++;
				}
				if( '.' === $file || '..' === $file ) {
					continue;
				}
				if( is_dir("$dir/$file") ) {
					$this->rm_folder_recursively("$dir/$file", $i);
				} else {
					if( file_exists("$dir/$file") ) {
						@unlink("$dir/$file");
					}
				}
			}
		}

		if( is_dir($dir) ) {
			$files_tmp = @scandir($dir);

			if( !isset($files_tmp[2]) ) {
				@rmdir($dir);
			}
		}

		return true;
	}
}
