<?php
	/**
	 * Plugin Name: Webcraftic Clearfy – WordPress optimization plugin
	 * Plugin URI: https://wordpress.org/plugins/clearfy/
	 * Description: Disables unused Wordpress features, improves performance and increases SEO rankings, using Clearfy, which makes WordPress very easy.
	 * Author: Webcraftic <wordpress.webraftic@gmail.com>
	 * Version: 1.3.1
	 * Text Domain: clearfy
	 * Domain Path: /languages/
	 * Author URI: http://webcraftic.com
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( defined('WBCR_CLEARFY_PLUGIN_ACTIVE') ) {
		return;
	}
	define('WBCR_CLEARFY_PLUGIN_ACTIVE', true);

	define('WCL_PLUGIN_DIR', dirname(__FILE__));
	define('WCL_PLUGIN_BASE', plugin_basename(__FILE__));
	define('WCL_PLUGIN_URL', plugins_url(null, __FILE__));

	#comp remove
	// the following constants are used to debug features of diffrent builds
	// on developer machines before compiling the plugin

	// build: free, premium, ultimate
	if( !defined('BUILD_TYPE') ) {
		define('BUILD_TYPE', 'free');
	}
	// language: en_US, ru_RU
	if( !defined('LANG_TYPE') ) {
		define('LANG_TYPE', 'en_EN');
	}
	// license: free, paid
	if( !defined('LICENSE_TYPE') ) {
		define('LICENSE_TYPE', 'free');
	}
	// wordpress language
	if( !defined('WPLANG') ) {
		define('WPLANG', LANG_TYPE);
	}

	define('WCL_PLUGIN_DEBUG', true);
	define('WCL_PLUGIN_FREEMIUS_DEBUG', false);

	// the compiler library provides a set of functions like onp_build and onp_license
	// to check how the plugin work for diffrent builds on developer machines

	require_once(WCL_PLUGIN_DIR . '/libs/onepress/compiler/boot.php');
	// #fix compiller bug new Factory000_Plugin
	#endcomp

	require_once(WCL_PLUGIN_DIR . '/includes/helpers.php');

	// creating a plugin via the factory
	require_once(WCL_PLUGIN_DIR . '/libs/factory/core/boot.php');
	require_once(WCL_PLUGIN_DIR . '/includes/class.plugin.php');

	new WCL_Plugin(__FILE__, array(
		'prefix' => 'wbcr_clearfy_',
		'plugin_name' => 'wbcr_clearfy',
		'plugin_title' => __('Clearfy', 'clearfy'),
		'plugin_version' => '1.3.1',
		'required_php_version' => '5.2',
		'required_wp_version' => '4.2',
		'freemius_plugin_id' => 2315,
		'freemius_plugin_slug' => 'clearfy',
		'freemius_public_key' => 'pk_70e226af07d37d2b9a69720e0952c',
		'plugin_build' => BUILD_TYPE,
		'updates' => WCL_PLUGIN_DIR . '/updates/',
		'author_site_url' => 'htts://clearfy.pro',
		'author_ru_site_url' => 'htts://ru.clearfy.pro'
	));