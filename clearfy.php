<?php
/**
 * Plugin Name: Webcraftic Clearfy – WordPress optimization plugin
 * Plugin URI: https://clearfy.pro
 * Description: Disables unused Wordpress features, improves performance and increases SEO rankings, using Clearfy, which makes WordPress very easy.
 * Author: Creative Motion <info@cm-wp.com>
 * Version: 2.1.5
 * Text Domain: clearfy
 * Domain Path: /languages/
 * Author URI: https://cm-wp.com
 * Framework Version: FACTORY_000_VERSION
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

/**
 * -----------------------------------------------------------------------------
 * CHECK REQUIREMENTS
 * Check compatibility with php and wp version of the user's site. As well as checking
 * compatibility with other plugins from Webcraftic.
 * -----------------------------------------------------------------------------
 */

require_once(dirname(__FILE__) . '/libs/factory/core/includes/class-factory-requirements.php');

$plugin_info = [
	'prefix' => 'wbcr_clearfy_',
	'plugin_name' => 'wbcr_clearfy',
	'plugin_title' => __('Clearfy', 'clearfy'),

	// PLUGIN SUPPORT
	'support_details' => [
		'url' => 'http://clearfy.pro',
		'pages_map' => [
			'features' => 'premium-features',  // {site}/premium-features
			'pricing' => 'pricing',           // {site}/prices
			'support' => 'support',           // {site}/support
			'docs' => 'docs'               // {site}/docs
		]
	],
	//todo: for compatibility with Robin image optimizer
	'freemius_plugin_id' => '2315',
	'freemius_public_key' => 'pk_70e226af07d37d2b9a69720e0952c',

	'has_updates' => true,
	'updates_settings' => [
		'repository' => 'github',
		'alternate_updates_mode' => true,
		'github_username' => 'Creative-Motion-Development', // обязательно, имя пользователя репозитория в Github
		'github_authorize_token' => '', // необязательно, только если репозиторий приватный
		'github_repository' => 'wp-plugin-clearfy', // необязательно, если не заполнено берется из slug
		'slug' => 'clearfy', // обязательно, slug плагина
		'maybe_rollback' => false,
		'rollback_settings' => [
			'prev_stable_version' => '0.0.0'
		]
	],

	// PLUGIN PREMIUM SETTINGS
	'has_premium' => true,
	'license_settings' => [
		'provider' => 'freemius',
		'slug' => 'clearfy_package',
		'plugin_id' => '2315',
		'public_key' => 'pk_70e226af07d37d2b9a69720e0952c',
		'price' => 29,
		'has_updates' => true,
		'updates_settings' => [
			'maybe_rollback' => true,
			'rollback_settings' => [
				'prev_stable_version' => '0.0.0'
			]
		]
	],

	// PLUGIN SUBSCRIBE FORM
	'subscribe_widget' => true,
	'subscribe_settings' => ['group_id' => '105407119'],

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
		['libs/factory/templates', 'factory_templates_000', 'all'],
		['libs/factory/freemius', 'factory_freemius_000', 'all'],
		['libs/factory/adverts', 'factory_adverts_000', 'admin'],
		['libs/factory/logger', 'factory_logger_000', 'all'],

	],
	'load_plugin_components' => [
		'disable_notices' => [
			'autoload' => 'components/disable-admin-notices/clearfy.php',
			'plugin_prefix' => 'WDN_'
		],
		'cyrlitera' => [
			'autoload' => 'components/cyrlitera/clearfy.php',
			'plugin_prefix' => 'WCTR_'
		],
		'updates_manager' => [
			'autoload' => 'components/updates-manager/clearfy.php',
			'plugin_prefix' => 'WUPM_'
		],
		'comments_tools' => [
			'autoload' => 'components/comments-plus/clearfy.php',
			'plugin_prefix' => 'WCM_'
		],
		'ga_cache' => [
			'autoload' => 'components/ga-cache/clearfy.php',
			'plugin_prefix' => 'WGA_'
		],
		'assets_manager' => [
			'autoload' => 'components/assets-manager/clearfy.php',
			'plugin_prefix' => 'WGZ_'
		],
		'minify_and_combine' => [
			'autoload' => 'components/minify-and-combine/clearfy.php',
			'plugin_prefix' => 'WMAC_'
		],
		'html_minify' => [
			'autoload' => 'components/html-minify/clearfy.php',
			'plugin_prefix' => 'WHTM_'
		],
		'clearfy_cache' => [
			'autoload' => 'components/cache/clearfy.php',
			'plugin_prefix' => 'WCACHE_'
		],
	]
];

#comp remove
// Отладочные данные, удаляются при компиляции.
/*$plugin_info['license_settings']['plugin_id']   = 2980;
$plugin_info['license_settings']['plugin_slug'] = 'clearfy';
$plugin_info['license_settings']['public_key']  = 'pk_541cb4e047456785c577658896ea8';*/
#endcomp

$clearfy_compatibility = new Wbcr_Factory000_Requirements(__FILE__, array_merge($plugin_info, [
	'plugin_already_activate' => defined('WCL_PLUGIN_ACTIVE'),
	'required_php_version' => '7.0',
	'required_wp_version' => '5.2.0',
	'required_clearfy_check_component' => false
]));

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
	define('FACTORY_UPDATES_DEBUG', true);

	// Через какой интервал времени проверять обновления на удаленном сервере?
	define('FACTORY_CHECK_UPDATES_INTERVAL', MINUTE_IN_SECONDS);
}

/**
 * Включить режим отладки для рекламного модуля. Если FACTORY_ADVERTS_DEBUG true,
 * то рекламный модуля не будет кешировать запросы к сереверу. Упрощает настройку
 * рекламы.
 */
if( !defined('FACTORY_ADVERTS_DEBUG') ) {
	define('FACTORY_ADVERTS_DEBUG', false);
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
	// creating a plugin via the factory
	require_once(WCL_PLUGIN_DIR . '/libs/factory/core/boot.php');
	require_once(WCL_PLUGIN_DIR . '/includes/class.plugin.php');

	new WCL_Plugin(__FILE__, array_merge($plugin_info, [
		'plugin_version' => WCL_PLUGIN_VERSION,
		'plugin_text_domain' => $clearfy_compatibility->get_text_domain(),
	]));
} catch( Exception $e ) {
	// Plugin wasn't initialized due to an error
	define('WCL_PLUGIN_THROW_ERROR', true);

	$clearfy_plugin_error_func = function () use ($e) {
		$error = sprintf("The %s plugin has stopped. <b>Error:</b> %s Code: %s", 'Clearfy', $e->getMessage(), $e->getCode());
		echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
	};

	add_action('admin_notices', $clearfy_plugin_error_func);
	add_action('network_admin_notices', $clearfy_plugin_error_func);
}

