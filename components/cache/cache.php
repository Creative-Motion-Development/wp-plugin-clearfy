<?php
/**
 * Plugin Name: Webcraftic Clearfy Cache
 * Plugin URI: https://webcraftic.com
 * Description: Webcraftic Clearfy Cache
 * Author: Webcraftic <wordpress.webraftic@gmail.com>
 * Version: 1.0.0
 * Text Domain: clearfy_cache
 * Domain Path: /languages/
 * Author URI: https://webcraftic.com
 * Framework Version: FACTORY_000_VERSION
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * Developers who contributions in the development plugin:
 *
 * Alexander Kovalev
 * ---------------------------------------------------------------------------------
 * Full plugin development.
 *
 * Email:         alex.kovalevv@gmail.com
 * Personal card: https://alexkovalevv.github.io
 * Personal repo: https://github.com/alexkovalevv
 * ---------------------------------------------------------------------------------
 */

/**
 * -----------------------------------------------------------------------------
 * CHECK REQUIREMENTS
 * Check compatibility with php and wp version of the user's site. As well as checking
 * compatibility with other plugins from Webcraftic.
 * -----------------------------------------------------------------------------
 */

require_once(dirname(__FILE__) . '/libs/factory/core/includes/class-factory-requirements.php');

// @formatter:off
$wcache_plugin_info = [
	'prefix' => 'wcache_',
	'plugin_name' => 'wcache',
	'plugin_title' => __('Webcraftic Clearfy Cache', 'clearfy_cache'),

	// PLUGIN SUPPORT
	'support_details' => [
		'url' => 'https://webcraftic.com',
		'pages_map' => [
			'support' => 'support',        // {site}/support
			'docs' => 'docs'               // {site}/docs
		]
	],

	// PLUGIN SUBSCRIBE FORM
	'subscribe_widget' => true,
	'subscribe_settings' => ['group_id' => '105408892'],

	// PLUGIN ADVERTS
	'render_adverts' => true,
	'adverts_settings' => [
		'dashboard_widget' => true, // show dashboard widget (default: false)
		'right_sidebar' => true, // show adverts sidebar (default: false)
		'notice' => true, // show notice message (default: false)
	],

	// FRAMEWORK MODULES
	'load_factory_modules' => [
		['libs/factory/bootstrap', 'factory_bootstrap_000', 'admin'],
		['libs/factory/forms', 'factory_forms_000', 'admin'],
		['libs/factory/pages', 'factory_pages_000', 'admin'],
		['libs/factory/clearfy', 'factory_templates_000', 'all'],
		['libs/factory/adverts', 'factory_adverts_000', 'admin']
	]
];

$wcache_compatibility = new Wbcr_Factory000_Requirements(__FILE__, array_merge($wcache_plugin_info, [
	'plugin_already_activate' => defined('WCACHE_ACTIVE'),
	'required_php_version' => '5.4',
	'required_wp_version' => '4.2.0',
	'required_clearfy_check_component' => false
]));

/**
 * If the plugin is compatible, then it will continue its work, otherwise it will be stopped,
 * and the user will throw a warning.
 */
if( !$wcache_compatibility->check() ) {
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
define('WCACHE_PLUGIN_ACTIVE', true);
define('WCACHE_PLUGIN_VERSION', $wcache_compatibility->get_plugin_version());
define('WCACHE_PLUGIN_DIR', dirname(__FILE__));
define('WCACHE_PLUGIN_BASE', plugin_basename(__FILE__));
define('WCACHE_PLUGIN_URL', plugins_url(null, __FILE__));

#comp remove
// Эта часть кода для компилятора, не требует редактирования.
// Все отладочные константы будут удалены после компиляции плагина.

// Сборка плагина
// build: free, premium, ultimate
if( !defined('BUILD_TYPE') ) {
	define('BUILD_TYPE', 'free');
}
// Языки уже не используются, нужно для работы компилятора
// language: en_US, ru_RU
if( !defined('LANG_TYPE') ) {
	define('LANG_TYPE', 'en_EN');
}

// Тип лицензии
// license: free, paid
if( !defined('LICENSE_TYPE') ) {
	define('LICENSE_TYPE', 'free');
}

// wordpress language
if( !defined('WPLANG') ) {
	define('WPLANG', LANG_TYPE);
}

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
	 * Новая версия плагина всегда берется из константы WCACHE_VERSION
	 * или из комментариев к входному файлу плагина.
	 */
	//define( 'FACTORY_MIGRATIONS_FORCE_OLD_VERSION', '1.1.9' );
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
if( !defined('FACTORY_ADVERTS_BLOCK') ) {
	define('FACTORY_ADVERTS_BLOCK', false);
}

// the compiler library provides a set of functions like onp_build and onp_license
// to check how the plugin work for diffrent builds on developer machines

require_once(WCACHE_PLUGIN_DIR . '/libs/onepress/compiler/boot.php');
// creating a plugin via the factory

// #fix compiller bug new Factory000_Plugin
#endcomp

/**
 * -----------------------------------------------------------------------------
 * PLUGIN INIT
 * -----------------------------------------------------------------------------
 */

require_once(WCACHE_PLUGIN_DIR . '/libs/factory/core/boot.php');
require_once(WCACHE_PLUGIN_DIR . '/includes/class-plugin.php');

try {
	new WCTR_Plugin(__FILE__, array_merge($wcache_plugin_info, [
		'plugin_version' => WCACHE_PLUGIN_VERSION,
		'plugin_text_domain' => $wcache_compatibility->get_text_domain(),
	]));
} catch( Exception $e ) {
	// Plugin wasn't initialized due to an error
	define('WCACHE_THROW_ERROR', true);

	$wcache_plugin_error_func = function () use ($e) {
		$error = sprintf("The %s plugin has stopped. <b>Error:</b> %s Code: %s", 'Webcraftic Clearfy Cache', $e->getMessage(), $e->getCode());
		echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
	};

	add_action('admin_notices', $wcache_plugin_error_func);
	add_action('network_admin_notices', $wcache_plugin_error_func);
}
// @formatter:on