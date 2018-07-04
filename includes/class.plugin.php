<?php
	/**
	 * Clearfy core class
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 19.02.2018, Webcraftic
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	class WCL_Plugin extends Wbcr_Factory000_Plugin {

		/**
		 * @var WCL_Plugin
		 */
		private static $app;

		public function __construct($plugin_path, $data)
		{
			self::$app = $this;

			parent::__construct($plugin_path, $data);

			$this->setTextDomain();
			$this->setModules();
			$this->setAddons();

			$this->globalScripts();

			if( is_admin() ) {
				$this->adminScripts();
			}

			add_action('plugins_loaded', array($this, 'pluginsLoaded'));
		}

		public static function app()
		{
			return self::$app;
		}

		protected function setTextDomain()
		{
			// Localization plugin
			load_plugin_textdomain('clearfy', false, dirname(WCL_PLUGIN_BASE) . '/languages/');
		}

		protected function initActivation()
		{
			include_once(WCL_PLUGIN_DIR . '/admin/activation.php');
			$this->registerActivation('WCL_Activation');
		}

		protected function setModules()
		{
			$this->load(array(
				array('libs/factory/bootstrap', 'factory_bootstrap_000', 'admin'),
				array('libs/factory/forms', 'factory_forms_000', 'admin'),
				array('libs/factory/pages', 'factory_pages_000', 'admin'),
				array('libs/factory/clearfy', 'factory_clearfy_000', 'all')
			));
		}

		public function setAddons()
		{
			$addons = array();

			if( $this->isActivateComponent('html_minify') && !defined('LOADING_HTML_MINIFY_AS_ADDON') ) {
				$addons['html_minify'] = array(
					'WHM_Plugin',
					WCL_PLUGIN_DIR . '/components/html-minify/html-minify.php'
				);
			}

			if( $this->isActivateComponent('hide_login_page') && !defined('LOADING_MINIFY_AND_COMBINE_AS_ADDON') ) {
				$addons['minify_and_combine'] = array(
					'WMAC_Plugin',
					WCL_PLUGIN_DIR . '/components/minify-and-combine/minify-and-combine.php'
				);
			}

			// This module is for Cyrillic users only, for other users it should be disabled
			if( $this->isActivateComponent('cyrlitera') && !defined('LOADING_CYRLITERA_AS_ADDON') ) {
				$addons['cyrlitera'] = array(
					'WCTR_Plugin',
					WCL_PLUGIN_DIR . '/components/cyrlitera/cyrlitera.php'
				);
			}

			if( $this->isActivateComponent('disable_notices') && !defined('LOADING_DISABLE_ADMIN_NOTICES_AS_ADDON') ) {
				$addons['disable_admin_notices'] = array(
					'WDN_Plugin',
					WCL_PLUGIN_DIR . '/components/disable-admin-notices/disable-admin-notices.php'
				);
			}

			if( $this->isActivateComponent('updates_manager') && !defined('LOADING_UPDATES_MANAGER_AS_ADDON') ) {
				$addons['updates_manager'] = array(
					'WUP_Plugin',
					WCL_PLUGIN_DIR . '/components/updates-manager/webcraftic-updates-manager.php'
				);
			}

			if( $this->isActivateComponent('comments_tools') && !defined('LOADING_COMMENTS_PLUS_AS_ADDON') ) {
				$addons['comments_plus'] = array(
					'WCM_Plugin',
					WCL_PLUGIN_DIR . '/components/comments-plus/comments-plus.php'
				);
			}

			if( $this->isActivateComponent('asset_manager') && !defined('LOADING_GONZALES_AS_ADDON') ) {
				$addons['gonzales'] = array(
					'WGZ_Plugin',
					WCL_PLUGIN_DIR . '/components/assets-manager/gonzales.php'
				);
			}

			if( $this->isActivateComponent('ga_cache') && !defined('LOADING_GA_CACHE_AS_ADDON') ) {
				$addons['ga_cache'] = array(
					'WGA_Plugin',
					WCL_PLUGIN_DIR . '/components/ga-cache/simple_google_analytics.php'
				);
			}

			/**
			 * Include plugin components
			 */
			$this->loadAddons($addons);
		}

		private function adminScripts()
		{

			require_once(WCL_PLUGIN_DIR . '/admin/includes/classes/class.pages.php');
			require_once(WCL_PLUGIN_DIR . '/admin/includes/classes/class.option.php');
			require_once(WCL_PLUGIN_DIR . '/admin/includes/classes/class.group.php');

			require_once(WCL_PLUGIN_DIR . '/admin/activation.php');

			if( defined('DOING_AJAX') && DOING_AJAX && isset($_REQUEST['action']) && $_REQUEST['action'] == 'wbcr_clearfy_configurate' ) {
				require(WCL_PLUGIN_DIR . '/admin/ajax/configurate.php');
			}

			if( defined('DOING_AJAX') && DOING_AJAX && isset($_REQUEST['action']) && $_REQUEST['action'] == 'wbcr_clearfy_import_settings' ) {
				require(WCL_PLUGIN_DIR . '/admin/ajax/import-settings.php');
			}

			require_once(WCL_PLUGIN_DIR . '/admin/boot.php');
			
			require_once( WCL_PLUGIN_DIR . '/includes/classes/class.licensing.php' );

			$this->initActivation();
			$this->registerPages();
		}

		private function registerPages()
		{
			$this->registerPage('WCL_QuickStartPage', WCL_PLUGIN_DIR . '/admin/pages/quick-start.php');
			$this->registerPage('WCL_AdvancedPage', WCL_PLUGIN_DIR . '/admin/pages/advanced.php');
			$this->registerPage('WCL_PerformancePage', WCL_PLUGIN_DIR . '/admin/pages/performance.php');
			$this->registerPage('WCL_PerformanceGooglePage', WCL_PLUGIN_DIR . '/admin/pages/performance-google.php');
			//$this->registerPage('WCL_PerformanceHtmlMinifyPage', WCL_PLUGIN_DIR . '/admin/pages/performance-html-minify.php');
			$this->registerPage('WCL_ComponentsPage', WCL_PLUGIN_DIR . '/admin/pages/components.php');
			$this->registerPage('WCL_SeoPage', WCL_PLUGIN_DIR . '/admin/pages/seo.php');
			$this->registerPage('WCL_DoublePagesPage', WCL_PLUGIN_DIR . '/admin/pages/seo-double-pages.php');
			$this->registerPage('WCL_DefencePage', WCL_PLUGIN_DIR . '/admin/pages/defence.php');
			$this->registerPage('WCL_PrivacyContentPage', WCL_PLUGIN_DIR . '/admin/pages/defence-privacy-code.php');
			$this->registerPage('WCL_LicensePage', WCL_PLUGIN_DIR . '/admin/pages/license.php');

			if( $this->isActivateComponent('widget_tools') ) {
				$this->registerPage('WCL_WidgetsPage', WCL_PLUGIN_DIR . '/admin/pages/widgets.php');
			}
		}

		private function globalScripts()
		{
			require_once(WCL_PLUGIN_DIR . '/includes/boot.php');

			require_once(WCL_PLUGIN_DIR . '/includes/classes/class.configurate-performance.php');
			require_once(WCL_PLUGIN_DIR . '/includes/classes/class.configurate-google-performance.php');
			require_once(WCL_PLUGIN_DIR . '/includes/classes/class.configurate-privacy.php');
			require_once(WCL_PLUGIN_DIR . '/includes/classes/class.configurate-security.php');
			require_once(WCL_PLUGIN_DIR . '/includes/classes/class.configurate-seo.php');

			new WCL_ConfigPerformance($this);
			new WCL_ConfigGooglePerformance($this);
			new WCL_ConfigPrivacy($this);
			new WCL_ConfigSecurity($this);
			new WCL_ConfigSeo($this);
		}

		public function pluginsLoaded()
		{
			//$this->setModules();
			//$this->setAddons();

			require_once(WCL_PLUGIN_DIR . '/includes/classes/class.configurate-advanced.php');
			new WCL_ConfigAdvanced($this);
		}

		/**
		 * @return bool
		 */
		public function currentUserCan()
		{
			return current_user_can('manage_options');
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

			$deactivate_components = $this->getOption('deactive_preinstall_components', array());

			if( $deactivate_components && in_array($component_name, $deactivate_components) ) {
				return false;
			}

			return true;
		}

		/**
		 * @param string $component_name
		 * @return bool
		 * @throws Exception
		 */
		public function deactivateComponent($component_name)
		{
			if( !$this->isActivateComponent($component_name) ) {
				return true;
			}

			$deactivate_components = $this->getOption('deactive_preinstall_components', array());

			if( !empty($deactivate_components) && is_array($deactivate_components) ) {
				$deactivate_components[] = $component_name;
			} else {
				$deactivate_components = array();
				$deactivate_components[] = $component_name;
			}

			$this->updateOption('deactive_preinstall_components', $deactivate_components);

			return true;
		}

		/**
		 * @param string $component_name
		 * @return bool
		 * @throws Exception
		 */
		public function activateComponent($component_name)
		{
			if( $this->isActivateComponent($component_name) ) {
				return true;
			}

			$deactivate_components = $this->getOption('deactive_preinstall_components', array());

			if( !empty($deactivate_components) && is_array($deactivate_components) ) {
				$index = array_search($component_name, $deactivate_components);
				unset($deactivate_components[$index]);
			}

			if( empty($deactivate_components) ) {
				$this->deleteOption('deactive_preinstall_components');

				return true;
			}

			$this->updateOption('deactive_preinstall_components', $deactivate_components);

			return true;
		}
	}
