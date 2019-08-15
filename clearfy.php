<?php
/**
 * Plugin Name: Webcraftic Clearfy – WordPress optimization plugin
 * Plugin URI: https://wordpress.org/plugins/clearfy/
 * Description: Disables unused Wordpress features, improves performance and increases SEO rankings, using Clearfy, which makes WordPress very easy.
 * Author: Webcraftic <wordpress.webraftic@gmail.com>
 * Version: 1.5.5
 * Text Domain: clearfy
 * Domain Path: /languages/
 * Author URI: http://clearfy.pro
 * Framework Version: FACTORY_000_VERSION
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
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

require_once( dirname( __FILE__ ) . '/libs/factory/core/includes/class-factory-requirements.php' );

// @formatter:off
$plugin_info = array(
	'prefix'               => 'wbcr_clearfy_',
	'plugin_name'          => 'wbcr_clearfy',
	'plugin_title'         => __( 'Clearfy', 'clearfy' ),
	'freemius_plugin_id'   => 2315,
	'freemius_plugin_slug' => 'clearfy',
	'freemius_public_key'  => 'pk_70e226af07d37d2b9a69720e0952c',


	// PLUGIN SUPPORT
	'support_details'      => array(
		'url'       => 'http://clearfy.pro',
		'pages_map' => array(
			'features' => 'premium-features',  // {site}/premium-features
			'pricing'  => 'pricing',           // {site}/prices
			'support'  => 'support',           // {site}/support
			'docs'     => 'docs'               // {site}/docs
		)
	),

	// PLUGIN UPDATED SETTINGS
	/*'has_updates'          => false,
	'updates_settings'     => array(
		'repository'        => 'wordpress',
		'slug'              => 'robin-image-optimizer',
		'maybe_rollback'    => true,
		'rollback_settings' => array(
			'prev_stable_version' => '0.0.0'
		)
	),*/

	// PLUGIN PREMIUM SETTINGS
	'has_premium'          => true,
	'license_settings'     => array(
		'provider'         => 'freemius',
		//'slug'             => 'robin-image-optimizer',
		//'plugin_id'        => '3464',
		//'public_key'       => 'pk_cafff5a51bd5fcf09c6bde806956d',
		// SANDBOX
		'slug'             => 'robin-image-optimizer',
		'plugin_id'        => '3106',
		'public_key'       => 'pk_f4e5e537d4a5cb45d516fb9bdceec',
		'price'            => 19,
		'has_updates'      => false,
		'updates_settings' => array(
			'maybe_rollback'    => true,
			'rollback_settings' => array(
				'prev_stable_version' => '0.0.0'
			)
		)
	),

	// FRAMEWORK MODULES
	'load_factory_modules' => array(
		array( 'libs/factory/bootstrap', 'factory_bootstrap_000', 'admin' ),
		array( 'libs/factory/forms', 'factory_forms_000', 'admin' ),
		array( 'libs/factory/pages', 'factory_pages_000', 'admin' ),
		array( 'libs/factory/clearfy', 'factory_clearfy_000', 'all' ),
		array( 'libs/factory/freemius', 'factory_freemius_000', 'all' )
	)
);

#comp remove
// Отладочные данные, удаляются при компиляции.
//$plugin_info['freemius_plugin_id']   = 2980;
//$plugin_info['freemius_plugin_slug'] = 'clearfy';
//$plugin_info['freemius_public_key']  = 'pk_541cb4e047456785c577658896ea8';
#endcomp

$clearfy_compatibility = new Wbcr_Factory000_Requirements( __FILE__, array_merge( $plugin_info, array(
	'plugin_already_activate'          => defined( 'WRIO_PLUGIN_ACTIVE' ),
	'required_php_version'             => '5.4',
	'required_wp_version'              => '4.2.0',
	'required_clearfy_check_component' => false
) ) );


/**
 * If the plugin is compatible, then it will continue its work, otherwise it will be stopped,
 * and the user will throw a warning.
 */
if ( ! $clearfy_compatibility->check() ) {
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
define( 'WCL_PLUGIN_ACTIVE', true );

// Plugin version
define( 'WCL_PLUGIN_VERSION', $clearfy_compatibility->get_plugin_version() );
define( 'WCL_FRAMEWORK_VER', 'FACTORY_000_VERSION' );

define( 'WCL_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'WCL_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'WCL_PLUGIN_URL', plugins_url( null, __FILE__ ) );

#comp remove
// Эта часть кода для компилятора, не требует редактирования.
// Все отладочные константы будут удалены после компиляции плагина.

// Сборка плагина
// build: free, premium, ultimate
if ( ! defined( 'BUILD_TYPE' ) ) {
	define( 'BUILD_TYPE', 'free' );
}
// Языки уже не используются, нужно для работы компилятора
// language: en_US, ru_RU
if ( ! defined( 'LANG_TYPE' ) ) {
	define( 'LANG_TYPE', 'en_EN' );
}

// Тип лицензии
// license: free, paid
if ( ! defined( 'LICENSE_TYPE' ) ) {
	define( 'LICENSE_TYPE', 'free' );
}

// wordpress language
if ( ! defined( 'WPLANG' ) ) {
	define( 'WPLANG', LANG_TYPE );
}

define( 'WCL_PLUGIN_DEBUG', true );
define( 'WCL_PLUGIN_FREEMIUS_DEBUG', false );

/**
 * Включить режим отладки миграций с версии x.x.x до x.x.y. Если true и
 * установлена константа FACTORY_MIGRATIONS_FORCE_OLD_VERSION, ваш файл
 * миграции будет вызваться постоянно.
 */
if ( ! defined( 'FACTORY_MIGRATIONS_DEBUG' ) ) {
	define( 'FACTORY_MIGRATIONS_DEBUG', false );

	/**
	 * Так как, после первого выполнения миграции, плагин обновляет
	 * опцию plugin_version, чтобы миграция больше не выполнялась,
	 * в тестовом режиме миграций, старая версия плагина берется не
	 * из опции в базе данных, а из текущей константы.
	 *
	 * Новая версия плагина всегда берется из константы WRIO_PLUGIN_VERSION
	 * или из комментариев к входному файлу плагина.
	 */
	define( 'FACTORY_MIGRATIONS_FORCE_OLD_VERSION', '1.2.9' );
}

/**
 * Включить режим отладки обновлений плагина и обновлений его премиум версии.
 * Если true, плагин не будет кешировать результаты проверки обновлений, а
 * будет проверять обновления через установленный интервал в константе
 * FACTORY_CHECK_UPDATES_INTERVAL.
 */
if ( ! defined( 'FACTORY_UPDATES_DEBUG' ) ) {
	define( 'FACTORY_UPDATES_DEBUG', false );

	// Через какой интервал времени проверять обновления на удаленном сервере?
	define( 'FACTORY_CHECK_UPDATES_INTERVAL', MINUTE_IN_SECONDS );
}

// the compiler library provides a set of functions like onp_build and onp_license
// to check how the plugin work for diffrent builds on developer machines

require_once( WCL_PLUGIN_DIR . '/libs/onepress/compiler/boot.php' );
// creating a plugin via the factory

// #fix compiller bug new Factory000_Plugin
#endcomp

/**
 * -----------------------------------------------------------------------------
 * PLUGIN INIT
 * -----------------------------------------------------------------------------
 */
try {
	require_once( WCL_PLUGIN_DIR . '/includes/helpers.php' );

	// creating a plugin via the factory
	require_once( WCL_PLUGIN_DIR . '/libs/factory/core/boot.php' );
	require_once( WCL_PLUGIN_DIR . '/includes/class.plugin.php' );

	new WCL_Plugin(  __FILE__, array_merge( $plugin_info, array(
		'plugin_version'     => WCL_PLUGIN_VERSION,
		'plugin_text_domain' => $clearfy_compatibility->get_text_domain(),
	) )  );
} catch( Exception $e ) {
	// Plugin wasn't initialized due to an error
	define( 'WRIO_PLUGIN_THROW_ERROR', true );

	$clearfy_plugin_error_func = function () use ( $e ) {
		$error = sprintf( "The %s plugin has stopped. <b>Error:</b> %s Code: %s", 'Clearfy', $e->getMessage(), $e->getCode() );
		echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
	};

	add_action( 'admin_notices', $clearfy_plugin_error_func );
	add_action( 'network_admin_notices', $clearfy_plugin_error_func );
}
// @formatter:on