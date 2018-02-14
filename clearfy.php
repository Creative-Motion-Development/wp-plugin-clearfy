<?php
	/**
	 * Plugin Name: Webcraftic Clearfy – WordPress optimization plugin
	 * Plugin URI: https://wordpress.org/plugins/clearfy/
	 * Description: Disables unused Wordpress features, improves performance and increases SEO rankings, using Clearfy, which makes WordPress very easy.
	 * Author: Webcraftic <wordpress.webraftic@gmail.com>
	 * Version: 1.1.91
	 * Text Domain: clearfy
	 * Domain Path: /languages/
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

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

	require_once(WBCR_CLR_PLUGIN_DIR . '/includes/helpers.php');

	// creating a plugin via the factory
	require_once(WBCR_CLR_PLUGIN_DIR . '/libs/factory/core/boot.php');

	class WCL_Plugin extends Wbcr_Factory000_Plugin {

		private static $app;

		public function __construct($plugin_path, $data)
		{
			self::$app = $this;
			parent::__construct($plugin_path, $data);

			$this->setTextDomain();
			$this->setModules();
			$this->setAddons();

			//add_action('wbcr_factory_000_core_modules_loaded-' . $this->plugin_name, array($this, 'registerPages'));

			$this->initPluginGlobalArea();

			if( is_admin() ) {
				$this->initPluginAdminArea();
			}

			$this->registerPages();
		}

		public static function app()
		{
			return self::$app;
		}

		public function registerPages()
		{
			require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/quick-start.php');
			Wbcr_FactoryPages000::register($this, 'WCL_QuickStartPage');

			require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/advanced.php');
			Wbcr_FactoryPages000::register($this, 'WCL_AdvancedPage');

			require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/code-clean.php');
			Wbcr_FactoryPages000::register($this, 'WCL_CodeCleanPage');
			/*FactoryPages000::register($this, 'WCL_PrivacyPage');
			require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/privacy.php');s

			require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/seo.php');
			Wbcr_FactoryPages000::register($this, 'WCL_SeoPage');

			require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/double-pages.php');
			Wbcr_FactoryPages000::register($this, 'WCL_DoublePagesPage');

			require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/defence.php');
			Wbcr_FactoryPages000::register($this, 'WCL_DefencePage');



			//if( empty($preinsatall_components) || !in_array('widget_tools', $preinsatall_components) ) {
			require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/widgets.php');
			Wbcr_FactoryPages000::register($this, 'WCL_WidgetsPage');
			//}



			require_once(WBCR_CLR_PLUGIN_DIR . '/admin/pages/components.php');
			Wbcr_FactoryPages000::register($this, 'WCL_ComponentsPage');*/
		}

		public function setTextDomain()
		{
			// Localization plugin
			load_plugin_textdomain($this->plugin_name, false, dirname(WBCR_CLR_PLUGIN_BASE) . '/languages/');
		}

		public function setModules()
		{
			$this->load(array(
				array('libs/factory/bootstrap', 'factory_bootstrap', 'admin'),
				array('libs/factory/forms', 'factory_forms', 'admin'),
				array('libs/factory/pages', 'factory_pages', 'admin'),
				array('libs/factory/clearfy', 'factory_clearfy', 'all')
			));
		}

		public function setAddons()
		{
			$addons = array();

			if( $this->isActivateComponent('updates_manager') ) {
				//$addons['updates_manager'] = WBCR_CLR_PLUGIN_DIR . '/components/updates-manager/webcraftic-updates-manager.php';
			}
			if( $this->isActivateComponent('comments_tools') ) {
				//$addons['comments_plus'] = WBCR_CLR_PLUGIN_DIR . '/components/comments-plus/comments-plus.php';
			}
			if( $this->isActivateComponent('asset_manager') ) {
				//$addons['gonzales'] = WBCR_CLR_PLUGIN_DIR . '/components/assets-manager/gonzales.php';
			}
			if( $this->isActivateComponent('disable_notices') ) {
				//$addons['disable_admin_notices'] = WBCR_CLR_PLUGIN_DIR . '/components/disable-admin-notices/disable-admin-notices.php';
			}

			/**
			 * Include plugin components
			 */
			$this->loadAddons($addons);
		}

		public function initPluginAdminArea()
		{
			require_once(WBCR_CLR_PLUGIN_DIR . '/admin/includes/classes/class.pages.php');
			require_once(WBCR_CLR_PLUGIN_DIR . '/admin/boot.php');
		}

		public function initPluginGlobalArea()
		{
			//require_once(WBCR_CLR_PLUGIN_DIR . '/includes/classes/class.configurate-code-clean.php');
			//require_once(WBCR_CLR_PLUGIN_DIR . '/includes/classes/class.configurate-privacy.php');
			//require_once(WBCR_CLR_PLUGIN_DIR . '/includes/classes/class.configurate-security.php');
			//require_once(WBCR_CLR_PLUGIN_DIR . '/includes/classes/class.configurate-seo.php');
			//require_once(WBCR_CLR_PLUGIN_DIR . '/includes/classes/class.configurate-advanced.php');

			//new WbcrClearfy_ConfigCodeClean($wbcr_clearfy_plugin);
			//new WbcrClearfy_ConfigPrivacy($wbcr_clearfy_plugin);
			//new WbcrClearfy_ConfigSecurity($wbcr_clearfy_plugin);
			//new WbcrClearfy_ConfigSeo($wbcr_clearfy_plugin);
			//new WbcrClearfy_ConfigAdvanced($wbcr_clearfy_plugin);
		}

		/**
		 * @param string $component_name
		 * @return bool
		 * @throws Exception
		 */
		public function isActivateComponent($component_name)
		{
			if( !is_string($component_name) ) {
				throw new Exception('Attribute component_name must be is string');
			}

			$deactivate_components = $this->getOption('deactive_preinstall_components');

			if( $deactivate_components && !in_array($component_name, $deactivate_components) ) {
				return false;
			}

			return true;
		}
	}

	add_action('plugins_loaded', 'wbcr_clearfy_plugin_init');

	function wbcr_clearfy_plugin_init()
	{
		return new WCL_Plugin(__FILE__, array(
			'prefix' => 'wbcr_clearfy_',
			'plugin_name' => 'clearfy',
			'plugin_title' => __('Clearfy', 'clearfy'),
			'plugin_version' => '1.1.91',
			'plugin_url' => 'https://wordpress.org/plugins/clearfy/',
			'plugin_assembly' => BUILD_TYPE,
			'updates' => WBCR_CLR_PLUGIN_DIR . '/updates/',
			'deactive_preinstall_components' => get_option('wbcr_clearfy_deactive_preinstall_components', array())

		));
	}