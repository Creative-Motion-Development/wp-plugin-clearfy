<?php
	/**
	 * Plugin Name: Webcraftic Clearfy – WordPress optimization plugin
	 * Plugin URI: https://wordpress.org/plugins/clearfy/
	 * Description: Disables unused Wordpress features, improves performance and increases SEO rankings, using Clearfy, which makes WordPress very easy.
	 * Author: Webcraftic <wordpress.webraftic@gmail.com>
	 * Version: 1.1.9
	 * Text Domain: clearfy
	 * Domain Path: /languages/
	 */

	if( defined('WBCR_CLEARFY_PLUGIN_ACTIVE') ) {
		return;
	}
	define('WBCR_CLEARFY_PLUGIN_ACTIVE', true);

	define('WBCR_CLR_PLUGIN_DIR', dirname(__FILE__));
	define('WBCR_CLR_PLUGIN_BASE', plugin_basename(__FILE__));
	define('WBCR_CLR_PLUGIN_URL', plugins_url(null, __FILE__));

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
	// the compiler library provides a set of functions like onp_build and onp_license
	// to check how the plugin work for diffrent builds on developer machines

	require_once(WBCR_CLR_PLUGIN_DIR . '/libs/onepress/compiler/boot.php');
	#endcomp

	// creating a plugin via the factory
	require_once(WBCR_CLR_PLUGIN_DIR . '/libs/factory/core/boot.php');

	add_action('plugins_loaded', 'wbcr_clearfy_plugin_init');

	function wbcr_clearfy_plugin_init()
	{
		global $wbcr_clearfy_plugin;

		// Localization plugin
		load_plugin_textdomain('clearfy', false, dirname(WBCR_CLR_PLUGIN_BASE) . '/languages/');

		$wbcr_clearfy_plugin = new Factory000_Plugin(__FILE__, array(
			'name' => 'wbcr_clearfy',
			'title' => __('Clearfy', 'clearfy'),
			'version' => '1.1.9',
			'host' => 'wordpress.org',
			'url' => 'https://wordpress.org/plugins/clearfy/',
			'assembly' => BUILD_TYPE,
			'updates' => WBCR_CLR_PLUGIN_DIR . '/updates/',
			'deactive_preinstall_components' => get_option('wbcr_clearfy_deactive_preinstall_components', array())
		));

		// requires factory modules
		$wbcr_clearfy_plugin->load(array(
			array('libs/factory/bootstrap', 'factory_bootstrap_000', 'admin'),
			array('libs/factory/forms', 'factory_forms_000', 'admin'),
			array('libs/factory/pages', 'factory_pages_000', 'admin'),
			array('libs/factory/clearfy', 'factory_clearfy_000', 'all')
		));

		require(WBCR_CLR_PLUGIN_DIR . '/includes/functions.php');

		// loading other files
		if( is_admin() ) {
			require(WBCR_CLR_PLUGIN_DIR . '/admin/boot.php');
		}

		require(WBCR_CLR_PLUGIN_DIR . '/includes/classes/class.configurate-code-clean.php');
		require(WBCR_CLR_PLUGIN_DIR . '/includes/classes/class.configurate-privacy.php');
		require(WBCR_CLR_PLUGIN_DIR . '/includes/classes/class.configurate-security.php');
		require(WBCR_CLR_PLUGIN_DIR . '/includes/classes/class.configurate-seo.php');
		require(WBCR_CLR_PLUGIN_DIR . '/includes/classes/class.configurate-advanced.php');

		new WbcrClearfy_ConfigCodeClean($wbcr_clearfy_plugin);
		new WbcrClearfy_ConfigPrivacy($wbcr_clearfy_plugin);
		new WbcrClearfy_ConfigSecurity($wbcr_clearfy_plugin);
		new WbcrClearfy_ConfigSeo($wbcr_clearfy_plugin);
		new WbcrClearfy_ConfigAdvanced($wbcr_clearfy_plugin);

		$addons = array();
		$preinsatall_components = (array)$wbcr_clearfy_plugin->options['deactive_preinstall_components'];

		if( empty($preinsatall_components) || !in_array('update_manager', $preinsatall_components) ) {
			$addons['updates_manager'] = WBCR_CLR_PLUGIN_DIR . '/components/updates-manager/webcraftic-updates-manager.php';
		}
		if( empty($preinsatall_components) || !in_array('comments_tools', $preinsatall_components) ) {
			$addons['comments_plus'] = WBCR_CLR_PLUGIN_DIR . '/components/comments-plus/comments-plus.php';
		}
		if( empty($preinsatall_components) || !in_array('asset_manager', $preinsatall_components) ) {
			$addons['gonzales'] = WBCR_CLR_PLUGIN_DIR . '/components/assets-manager/gonzales.php';
		}
		if( empty($preinsatall_components) || !in_array('disable_notices', $preinsatall_components) ) {
			$addons['disable_admin_notices'] = WBCR_CLR_PLUGIN_DIR . '/components/disable-admin-notices/disable-admin-notices.php';
		}

		/**
		 * Include plugin components
		 */
		$wbcr_clearfy_plugin->loadAddons($addons);
	}

	/**
	 * Activates the plugin.
	 *
	 * TThe activation hook has to be registered before loading the plugin.
	 * The deactivateion hook can be registered in any place (currently in the file plugin.class.php).
	 */
	/*function wbcr_clearfy_plugin_activation()
	{
		if( !current_user_can('activate_plugins') ) {
			wp_die(__('You do not have sufficient permissions to activate plugins for this site.'));
		}

		wbcr_clearfy_plugin_init();

		global $wbcr_clearfy_plugin;
		$wbcr_clearfy_plugin->activate();
	}

	register_activation_hook(__FILE__, 'wbcr_clearfy_plugin_activation');*/
