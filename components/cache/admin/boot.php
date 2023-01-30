<?php
/**
 * Clearfy quick start options
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 03.06.2020, Webcraftic
 * @version       1.0
 */

add_filter('wbcr/clearfy/adminbar_menu_items', function ($menu_items) {
	$menu_items['clearfy-clear-all-cache'] = [
		'id' => 'clearfy-clear-all-cache',
		'title' => '<span class="dashicons dashicons-update"></span> ' . __('Clear all cache', 'clearfy'),
		'href' => add_query_arg('wclearfy_cache_delete', '1')
	];

	return $menu_items;
});

add_filter("wbcr_clearfy_group_options", function ($options) {
	$options[] = [
		'name' => 'enable_cache',
		'title' => __('Enable cache', 'clearfy'),
		'tags' => ['optimize_performance']
	];

	$options[] = [
		'name' => 'dont_cache_for_logged_in_users',
		'title' => __('Don\'t cache for logged-in users', 'clearfy'),
		'tags' => ['optimize_performance']
	];

	$options[] = [
		'name' => 'cache_reject_uri',
		'title' => __('Never Cache URL(s)', 'clearfy'),
		'tags' => []
	];

	$options[] = [
		'name' => 'preload_cache',
		'title' => __('Preload cache', 'clearfy'),
		'tags' => []
	];

	$options[] = [
		'name' => 'clear_cache_for_newpost',
		'title' => __('Clear cache for new post', 'clearfy'),
		'tags' => ['optimize_performance']
	];

	$options[] = [
		'name' => 'exclude_files',
		'title' => __('Filenames that can be cached', 'clearfy'),
		'tags' => []
	];

	$options[] = [
		'name' => 'exclude_pages',
		'title' => __('Rejected User Agents', 'clearfy'),
		'tags' => []
	];

	$options[] = [
		'name' => 'gzip',
		'title' => __('Gzip', 'clearfy'),
		'tags' => ['optimize_performance']
	];

	$options[] = [
		'name' => 'browser_caching',
		'title' => __('Browser Caching', 'clearfy'),
		'tags' => ['optimize_performance']
	];

	$options[] = [
		'name' => 'cache_mobile',
		'title' => __('Mobile', 'clearfy'),
		'tags' => []
	];

	$options[] = [
		'name' => 'cache_mobile_theme',
		'title' => __('Create cache for mobile theme', 'clearfy'),
		'tags' => []
	];

	$options[] = [
		'name' => 'widget_cache',
		'title' => __('Widget Cache', 'clearfy'),
		'tags' => ['optimize_performance']
	];

	return $options;
});

add_action('wclearfy/setup_wizard/speed_optimize_step/continue_step', function () {
	require_once WCACHE_PLUGIN_DIR . '/includes/helpers.php';
	try {
		\WCL_Cache_Helpers::modifyHtaccess();
	} catch( \Exception $e ) {

	}
});

add_filter('wclearfy/setup_wizard/speed_optimize_step/form_options', function ($options) {
	array_unshift($options, [
		'type' => 'checkbox',
		'way' => 'buttons',
		'name' => 'enable_cache',
		'title' => __('Enable cache', 'clearfy'),
		'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'green'],
		'hint' => __('This option enable cache to generates static html files from your dynamic WordPress blog. After a html file is generated your webserver will serve that file instead of processing the comparatively heavier and more expensive WordPress PHP scripts.', 'clearfy'),
		'default' => true
	], [
		'type' => 'checkbox',
		'way' => 'buttons',
		'name' => 'gzip',
		'title' => __('Gzip', 'clearfy'),
		'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'green'],
		'hint' => __('Reduce the size of page decrease the page load time a lot. You can reduce the size of page with GZIP compression feature.

If the size of requested files are big, loading takes time so in this case there is needed to reduce the size of files. Gzip Compression feature compresses the pages and resources before sending so the transfer time is reduced.', 'clearfy'),
		'default' => false
	], [
		'type' => 'checkbox',
		'way' => 'buttons',
		'name' => 'browser_caching',
		'title' => __('Browser Caching', 'clearfy'),
		'layout' => ['hint-type' => 'icon', 'hint-icon-color' => 'green'],
		'hint' => __('Reduce the load times of pages by storing commonly used files from your website on your visitors browser.

A browser loads the css, js, images resources to display the web page to the visitors. This process is always performed.

If the commonly used files are cached by browser, the visitors’ browsers do not have to load them evert time so the load times of pages are reduced.', 'clearfy'),
		'default' => false
	]);

	return $options;
});

add_action('wclearfy/cache/settings_page/after_form_save', function () {
	if( WCL_Cache_Helpers::is_nginx() && WCL_Plugin::app()->getPopulateOption('enable_cache') ) {
		wp_redirect(WCL_Plugin::app()->getPluginPageUrl('clearfy_cache_nginx_rules'));
		exit;
	}
}, 10);

add_action('wbcr/factory/pages/impressive/print_all_notices', function ($plugin, $page) {
	if( "clearfy_cache" === $page->id ) {
		if( WCL_Cache_Helpers::is_nginx() && WCL_Plugin::app()->getPopulateOption('enable_cache') ) {
			$button = '<br><a class="btn btn-default" href="' . WCL_Plugin::app()->getPluginPageUrl('clearfy_cache_nginx_rules') . '">NGINX configuration</a>';
			$page->printWarningNotice("<p>" . __("Clearfy will work out of the box on NGINX servers. But if you want to get the best performance results, place the NGINX rules we generated in your server config. It enables NGINX to directly serve previously cached files without calling WordPress or any PHP. It also adds headers to cached CSS, JS, and images via browser cache.", 'wbcr_factory_pages_000') . "</p>" . $button);
		}
	}
}, 10, 2);

add_filter('wclearfy/cache/htaccess_rules', function ($htaccess) {
	$gzip = WCL_Plugin::app()->getPopulateOption('gzip');

	if( $gzip ) {
		$data = "# BEGIN GzipWClearfyCache" . "\n" . "<IfModule mod_deflate.c>" . "\n" . "AddType x-font/woff .woff" . "\n" . "AddType x-font/ttf .ttf" . "\n" . "AddOutputFilterByType DEFLATE image/svg+xml" . "\n" . "AddOutputFilterByType DEFLATE text/plain" . "\n" . "AddOutputFilterByType DEFLATE text/html" . "\n" . "AddOutputFilterByType DEFLATE text/xml" . "\n" . "AddOutputFilterByType DEFLATE text/css" . "\n" . "AddOutputFilterByType DEFLATE text/javascript" . "\n" . "AddOutputFilterByType DEFLATE application/xml" . "\n" . "AddOutputFilterByType DEFLATE application/xhtml+xml" . "\n" . "AddOutputFilterByType DEFLATE application/rss+xml" . "\n" . "AddOutputFilterByType DEFLATE application/javascript" . "\n" . "AddOutputFilterByType DEFLATE application/x-javascript" . "\n" . "AddOutputFilterByType DEFLATE application/x-font-ttf" . "\n" . "AddOutputFilterByType DEFLATE x-font/ttf" . "\n" . "AddOutputFilterByType DEFLATE application/vnd.ms-fontobject" . "\n" . "AddOutputFilterByType DEFLATE font/opentype font/ttf font/eot font/otf" . "\n" . "</IfModule>" . "\n";

		if( defined("WCLEARFY_GZIP_FOR_COMBINED_FILES") && WCLEARFY_GZIP_FOR_COMBINED_FILES ) {
			$data = $data . "\n" . '<FilesMatch "\d+index\.(css|js)(\.gz)?$">' . "\n" . "# to zip the combined css and js files" . "\n\n" . "RewriteEngine On" . "\n" . "RewriteCond %{HTTP:Accept-encoding} gzip" . "\n" . "RewriteCond %{REQUEST_FILENAME}\.gz -s" . "\n" . "RewriteRule ^(.*)\.(css|js) $1\.$2\.gz [QSA]" . "\n\n" . "# to revent double gzip and give the correct mime-type" . "\n\n" . "RewriteRule \.css\.gz$ - [T=text/css,E=no-gzip:1,E=FORCE_GZIP]" . "\n" . "RewriteRule \.js\.gz$ - [T=text/javascript,E=no-gzip:1,E=FORCE_GZIP]" . "\n" . "Header set Content-Encoding gzip env=FORCE_GZIP" . "\n" . "</FilesMatch>" . "\n";
		}

		$data = $data . "# END GzipWClearfyCache" . "\n";

		$htaccess = preg_replace("/\s*\#\s?BEGIN\s?GzipWClearfyCache.*?#\s?END\s?GzipWClearfyCache\s*/s", "", $htaccess);

		return $data . $htaccess;
	} else {
		//delete gzip rules
		$htaccess = preg_replace("/\s*\#\s?BEGIN\s?GzipWClearfyCache.*?#\s?END\s?GzipWClearfyCache\s*/s", "", $htaccess);

		return $htaccess;
	}
});

add_filter('wclearfy/cache/htaccess_rules', function ($htaccess) {
	$browser_caching = WCL_Plugin::app()->getPopulateOption('browser_caching');

	if( $browser_caching ) {

		$data = "
# BEGIN LBCWClearfyCache
<IfModule mod_expires.c>
ExpiresActive on
ExpiresDefault                              'access plus 1 month'
ExpiresByType text/cache-manifest           'access plus 0 seconds'
ExpiresByType text/html                     'access plus 0 seconds'
ExpiresByType text/xml                      'access plus 0 seconds'
ExpiresByType application/xml               'access plus 0 seconds'
ExpiresByType application/json              'access plus 0 seconds'
ExpiresByType application/rss+xml           'access plus 1 hour'
ExpiresByType application/atom+xml          'access plus 1 hour'
ExpiresByType image/x-icon                  'access plus 1 week'
ExpiresByType image/gif                     'access plus 4 months'
ExpiresByType image/png                     'access plus 4 months'
ExpiresByType image/jpeg                    'access plus 4 months'
ExpiresByType image/webp                    'access plus 4 months'
ExpiresByType video/ogg                     'access plus 4 months'
ExpiresByType audio/ogg                     'access plus 4 months'
ExpiresByType video/mp4                     'access plus 4 months'
ExpiresByType video/webm                    'access plus 4 months'
ExpiresByType text/x-component              'access plus 1 month'
ExpiresByType font/ttf                      'access plus 4 months'
ExpiresByType font/otf                      'access plus 4 months'
ExpiresByType font/woff                     'access plus 4 months'
ExpiresByType font/woff2                    'access plus 4 months'
ExpiresByType image/svg+xml                 'access plus 1 month'
ExpiresByType application/vnd.ms-fontobject 'access plus 1 month'
ExpiresByType text/css                      'access plus 1 year'
ExpiresByType application/javascript        'access plus 1 year'
</IfModule>
# END LBCWClearfyCache";

		if( !preg_match("/BEGIN\s*LBCWClearfyCache/", $htaccess) ) {
			return $data . $htaccess;
		} else {
			return $htaccess;
		}
	} else {
		//delete levere browser caching
		$htaccess = preg_replace("/#\s?BEGIN\s?LBCWClearfyCache.*?#\s?END\s?LBCWClearfyCache/s", "", $htaccess);

		return $htaccess;
	}
});

//add_action('wfactory/activated_' . WCL_Plugin::app()->getPluginName() . '_component', function ($component_name) {
//todo: Проверить совместимость с плагинами, включить или отключить компонент в зависимсти от результатов
//});