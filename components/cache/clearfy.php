<?php
/**
 * Этот файл инициализирует этот плагин, как аддон для плагина Clearfy.
 *
 * Файл будет подключен только в плагине Clearfy, используя особый вариант загрузки. Это более простое решение
 * пришло на смену встроенной системы подключения аддонов в фреймворке.
 *
 * @author        Alexander Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 * @copyright (c) 2018 Webraftic Ltd
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

if( !defined('WCACHE_PLUGIN_ACTIVE') ) {
	define('WCACHE_PLUGIN_VERSION', '1.0.2');
	define('WCACHE_TEXT_DOMAIN', 'clearfy_cache');
	define('WCACHE_PLUGIN_ACTIVE', true);

	// Этот плагин загружен, как аддон для плагина Clearfy
	define('LOADING_CLEARFY_CACHE_AS_ADDON', true);

	if( !defined('WCACHE_PLUGIN_DIR') ) {
		define('WCACHE_PLUGIN_DIR', dirname(__FILE__));
	}

	if( !defined('WCACHE_PLUGIN_BASE') ) {
		define('WCACHE_PLUGIN_BASE', plugin_basename(__FILE__));
	}

	if( !defined('WCACHE_PLUGIN_URL') ) {
		define('WCACHE_PLUGIN_URL', plugins_url(null, __FILE__));
	}

	try {
		// Global scripts
		require_once(WCACHE_PLUGIN_DIR . '/includes/3rd-party/class-clearfy-plugin.php');
		new WCACHE_Plugin();
	} catch( Exception $e ) {
		$wcache_plugin_error_func = function () use ($e) {
			$error = sprintf("The %s plugin has stopped. <b>Error:</b> %s Code: %s", 'Webcraftic Clearfy Cache', $e->getMessage(), $e->getCode());
			echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
		};

		add_action('admin_notices', $wcache_plugin_error_func);
		add_action('network_admin_notices', $wcache_plugin_error_func);
	}
}


