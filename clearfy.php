<?php
/**
 * Plugin Name: Webcraftic Clearfy – WordPress optimization plugin
 * Plugin URI: https://wordpress.org/plugins/clearfy/
 * Description: Disables unused Wordpress features, improves performance and increases SEO rankings, using Clearfy, which makes WordPress very easy.
 * Author: Webcraftic <wordpress.webraftic@gmail.com>
 * Version: 1.7.2
 * Text Domain: clearfy
 * Domain Path: /languages/
 * Author URI: http://clearfy.pro
 * Framework Version: FACTORY_000_VERSION
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

// @formatter:off
/**
 * -----------------------------------------------------------------------------
 * CHECK REQUIREMENTS
 * Check compatibility with php and wp version of the user's site. As well as checking
 * compatibility with other plugins from Webcraftic.
 * -----------------------------------------------------------------------------
 */

require_once(dirname(__FILE__) . '/libs/factory/core/includes/class-factory-requirements.php');

// @formatter:off
$plugin_info = array(
	'prefix' => 'wbcr_clearfy_',
	'plugin_name' => 'wbcr_clearfy',
	'plugin_title' => __('Clearfy', 'clearfy'),
	// PLUGIN SUPPORT
	'support_details' => array(
		'url' => 'http://clearfy.pro',
		'pages_map' => array(
			'features' => 'premium-features',  // {site}/premium-features
			'pricing' => 'pricing',           // {site}/prices
			'support' => 'support',           // {site}/support
			'docs' => 'docs'               // {site}/docs
		)
	),
	//todo: for compatibility with Robin image optimizer
	'freemius_plugin_id' => '2315',
	'freemius_public_key' => 'pk_70e226af07d37d2b9a69720e0952c',

	// PLUGIN PREMIUM SETTINGS
	'has_premium' => true,
	'license_settings' => array(
		'provider' => 'freemius',
		'slug' => 'clearfy_package',
		'plugin_id' => '2315',
		'public_key' => 'pk_70e226af07d37d2b9a69720e0952c',
		'price' => 29,
		'has_updates' => true,
		'updates_settings' => array(
			'maybe_rollback' => true,
			'rollback_settings' => array(
				'prev_stable_version' => '0.0.0'
			)
		)
	),
	// PLUGIN ADVERTS
	'render_adverts' => true,
	'adverts_settings' => array(
		'dashboard_widget' => true, // show dashboard widget (default: false)
		'right_sidebar' => true, // show adverts sidebar (default: false)
		'notice' => true, // show notice message (default: false)
	),
	// FRAMEWORK MODULES
	'load_factory_modules' => array(
		array('libs/factory/bootstrap', 'factory_bootstrap_000', 'admin'),
		array('libs/factory/forms', 'factory_forms_000', 'admin'),
		array('libs/factory/pages', 'factory_pages_000', 'admin'),
		array('libs/factory/clearfy', 'factory_clearfy_000', 'all'),
		array('libs/factory/freemius', 'factory_freemius_000', 'all'),
		array('libs/factory/adverts', 'factory_adverts_000', 'admin')
	),
	'load_plugin_components' => array(
		'disable_notices' => array(
			'autoload' => 'components/disable-admin-notices/clearfy.php',
			'plugin_prefix' => 'WDN_'
		),
		'cyrlitera' => array(
			'autoload' => 'components/cyrlitera/clearfy.php',
			'plugin_prefix' => 'WCTR_'
		),
		'updates_manager' => array(
			'autoload' => 'components/updates-manager/clearfy.php',
			'plugin_prefix' => 'WUPM_'
		),
		'comments_tools' => array(
			'autoload' => 'components/comments-plus/clearfy.php',
			'plugin_prefix' => 'WCM_'
		),
		'ga_cache' => array(
			'autoload' => 'components/ga-cache/clearfy.php',
			'plugin_prefix' => 'WGA_'
		),
		'assets_manager' => array(
			'autoload' => 'components/assets-manager/clearfy.php',
			'plugin_prefix' => 'WGZ_'
		),
		'minify_and_combine' => array(
			'autoload' => 'components/minify-and-combine/clearfy.php',
			'plugin_prefix' => 'WMAC_'
		),
		'html_minify' => array(
			'autoload' => 'components/html-minify/clearfy.php',
			'plugin_prefix' => 'WHTM_'
		),
	)
);

#comp remove
// Отладочные данные, удаляются при компиляции.
/*$plugin_info['license_settings']['plugin_id']   = 2980;
$plugin_info['license_settings']['plugin_slug'] = 'clearfy';
$plugin_info['license_settings']['public_key']  = 'pk_541cb4e047456785c577658896ea8';*/
#endcomp

$clearfy_compatibility = new Wbcr_Factory000_Requirements(__FILE__, array_merge($plugin_info, array(
	'plugin_already_activate' => defined('WCL_PLUGIN_ACTIVE'),
	'required_php_version' => '5.6',
	'required_wp_version' => '4.9.0',
	'required_clearfy_check_component' => false
)));

/**
 * If the plugin is compatible, then it will continue its work, otherwise it will be stopped,
 * and the user will throw a warning.
 */
if( !$clearfy_compatibility->check() ) {
	return;
}

/**
 * -----------------------------------------------------------------------------
 * CONSTANTS
 * Install frequently used constants and constants for debugging, which will be
 * removed after compiling the plugin.
 * -----------------------------------------------------------------------------
 */

// This plugin is activated
define('WCL_PLUGIN_ACTIVE', true);

// For for compatibility with old plugins
define('WBCR_CLEARFY_PLUGIN_ACTIVE', true);

// Plugin version
define('WCL_PLUGIN_VERSION', $clearfy_compatibility->get_plugin_version());
define('WCL_FRAMEWORK_VER', 'FACTORY_000_VERSION');

define('WCL_PLUGIN_DIR', dirname(__FILE__));
define('WCL_PLUGIN_BASE', plugin_basename(__FILE__));
define('WCL_PLUGIN_URL', plugins_url(null, __FILE__));

#comp remove
// Эта часть кода для компилятора, не требует редактирования.
// Все отладочные константы будут удалены после компиляции плагина.

// Сборка плагина
// build: free, premium, ultimate
if( !defined('BUILD_TYPE') ) {
	define('BUILD_TYPE', 'premium');
}
// Языки уже не используются, нужно для работы компилятора
// language: en_US, ru_RU
if( !defined('LANG_TYPE') ) {
	define('LANG_TYPE', 'en_EN');
}

// Тип лицензии
// license: free, paid
if( !defined('LICENSE_TYPE') ) {
	define('LICENSE_TYPE', 'paid');
}

// wordpress language
if( !defined('WPLANG') ) {
	define('WPLANG', LANG_TYPE);
}

define('WCL_PLUGIN_DEBUG', true);
define('WCL_PLUGIN_FREEMIUS_DEBUG', false);

/**
 * Включить режим отладки миграций с версии x.x.x до x.x.y. Если true и
 * установлена константа FACTORY_MIGRATIONS_FORCE_OLD_VERSION, ваш файл
 * миграции будет вызваться постоянно.
 */
if( !defined('FACTORY_MIGRATIONS_DEBUG') ) {
	define('FACTORY_MIGRATIONS_DEBUG', false);

	/**
	 * Так как, после первого выполнения миграции, плагин обновляет
	 * опцию plugin_version, чтобы миграция больше не выполнялась,
	 * в тестовом режиме миграций, старая версия плагина берется не
	 * из опции в базе данных, а из текущей константы.
	 *
	 * Новая версия плагина всегда берется из константы WRIO_PLUGIN_VERSION
	 * или из комментариев к входному файлу плагина.
	 */
	define('FACTORY_MIGRATIONS_FORCE_OLD_VERSION', '1.5.4');
}

/**
 * Включить режим отладки обновлений плагина и обновлений его премиум версии.
 * Если true, плагин не будет кешировать результаты проверки обновлений, а
 * будет проверять обновления через установленный интервал в константе
 * FACTORY_CHECK_UPDATES_INTERVAL.
 */
if( !defined('FACTORY_UPDATES_DEBUG') ) {
	define('FACTORY_UPDATES_DEBUG', false);

	// Через какой интервал времени проверять обновления на удаленном сервере?
	define('FACTORY_CHECK_UPDATES_INTERVAL', MINUTE_IN_SECONDS);
}

/**
 * Включить режим отладки для рекламного модуля. Если FACTORY_ADVERTS_DEBUG true,
 * то рекламный модуля не будет кешировать запросы к сереверу. Упрощает настройку
 * рекламы.
 */
if( !defined('FACTORY_ADVERTS_DEBUG') ) {
	define('FACTORY_ADVERTS_DEBUG', true);
}

/**
 * Остановить показ рекламы для всех плагинов созданных на Factory фреймворке.
 * Это может пригодиться в некоторых случаях, при неисправностях или из-за
 * файрвола в стране пользователя. Чтобы реклама не обременяла пользователя
 * он может ее заблокировать.
 */
/*if ( ! defined( 'FACTORY_ADVERTS_BLOCK' ) ) {
	define( 'FACTORY_ADVERTS_BLOCK', false );
}*/

// the compiler library provides a set of functions like onp_build and onp_license
// to check how the plugin work for diffrent builds on developer machines

require_once(WCL_PLUGIN_DIR . '/libs/onepress/compiler/boot.php');
// creating a plugin via the factory

// #fix compiller bug new Factory000_Plugin
#endcomp

/**
 * -----------------------------------------------------------------------------
 * PLUGIN INIT
 * -----------------------------------------------------------------------------
 */
try {
	require_once(WCL_PLUGIN_DIR . '/includes/helpers.php');

	// creating a plugin via the factory
	require_once(WCL_PLUGIN_DIR . '/libs/factory/core/boot.php');
	require_once(WCL_PLUGIN_DIR . '/includes/class.plugin.php');

	new WCL_Plugin(__FILE__, array_merge($plugin_info, array(
		'plugin_version' => WCL_PLUGIN_VERSION,
		'plugin_text_domain' => $clearfy_compatibility->get_text_domain(),
	)));
} catch( Exception $e ) {
	// Plugin wasn't initialized due to an error
	define('WRIO_PLUGIN_THROW_ERROR', true);

	$clearfy_plugin_error_func = function () use ($e) {
		$error = sprintf("The %s plugin has stopped. <b>Error:</b> %s Code: %s", 'Clearfy', $e->getMessage(), $e->getCode());
		echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
	};

	add_action('admin_notices', $clearfy_plugin_error_func);
	add_action('network_admin_notices', $clearfy_plugin_error_func);
}
// @formatter:on

