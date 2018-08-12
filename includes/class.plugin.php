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
				array('libs/factory/notices', 'factory_notices_000', 'admin'),
				array('libs/factory/clearfy', 'factory_clearfy_000', 'all')
			));
		}

		public function setAddons()
		{
			$addons = array();

			if( defined('WCL_PLUGIN_DEBUG') && WCL_PLUGIN_DEBUG ) {
				if( file_exists(WCL_PLUGIN_DIR . '/components/hide-my-wp/hide-my-wp.php') ) {
					$addons['webcraftic-hide-my-wp'] = array(
						'WHM_Plugin',
						WCL_PLUGIN_DIR . '/components/hide-my-wp/hide-my-wp.php'
					);
				}
			}

			if( $this->isActivateComponent('html_minify') && !defined('WGA_PLUGIN_ACTIVE') ) {
				$addons['html_minify'] = array(
					'WHTM_Plugin',
					WCL_PLUGIN_DIR . '/components/html-minify/html-minify.php'
				);
			}

			if( $this->isActivateComponent('minify_and_combine') && !defined('WMAC_PLUGIN_ACTIVE') ) {
				$addons['minify_and_combine'] = array(
					'WMAC_Plugin',
					WCL_PLUGIN_DIR . '/components/minify-and-combine/minify-and-combine.php'
				);
			}

			// This module is for Cyrillic users only, for other users it should be disabled
			if( $this->isActivateComponent('cyrlitera') && !defined('WCTR_PLUGIN_ACTIVE') ) {
				$addons['cyrlitera'] = array(
					'WCTR_Plugin',
					WCL_PLUGIN_DIR . '/components/cyrlitera/cyrlitera.php'
				);
			}

			if( $this->isActivateComponent('disable_notices') && !defined('WDN_PLUGIN_ACTIVE') ) {
				$addons['disable_admin_notices'] = array(
					'WDN_Plugin',
					WCL_PLUGIN_DIR . '/components/disable-admin-notices/disable-admin-notices.php'
				);
			}

			if( $this->isActivateComponent('updates_manager') && !defined('WUP_PLUGIN_ACTIVE') ) {
				$addons['updates_manager'] = array(
					'WUP_Plugin',
					WCL_PLUGIN_DIR . '/components/updates-manager/webcraftic-updates-manager.php'
				);
			}

			if( $this->isActivateComponent('comments_tools') && !defined('WCM_PLUGIN_ACTIVE') ) {
				$addons['comments_plus'] = array(
					'WCM_Plugin',
					WCL_PLUGIN_DIR . '/components/comments-plus/comments-plus.php'
				);
			}

			if( $this->isActivateComponent('asset_manager') && !defined('WGZ_PLUGIN_ACTIVE') ) {
				$addons['gonzales'] = array(
					'WGZ_Plugin',
					WCL_PLUGIN_DIR . '/components/assets-manager/gonzales.php'
				);
			}

			if( $this->isActivateComponent('ga_cache') && !defined('WGA_PLUGIN_ACTIVE') ) {
				$addons['ga_cache'] = array(
					'WGA_Plugin',
					WCL_PLUGIN_DIR . '/components/ga-cache/simple_google_analytics.php'
				);
			}

			/**
			 * Include plugin components
			 */

			require_once(WCL_PLUGIN_DIR . '/includes/classes/class.package.php');

			if( !defined('WCL_PLUGIN_DEBUG') || !WCL_PLUGIN_DEBUG ) {
				$package = WCL_Package::instance();
				$package_addons = $package->getActivedAddons();
				$addons = array_merge($addons, $package_addons);
			}

			$this->loadAddons($addons);
		}

		private function adminScripts()
		{
			require_once(WCL_PLUGIN_DIR . '/admin/includes/classes/class.pages.php');
			require_once(WCL_PLUGIN_DIR . '/admin/includes/classes/class.option.php');
			require_once(WCL_PLUGIN_DIR . '/admin/includes/classes/class.group.php');

			require_once(WCL_PLUGIN_DIR . '/admin/activation.php');

			if( defined('DOING_AJAX') && DOING_AJAX && isset($_REQUEST['action']) ) {
				if( $_REQUEST['action'] == 'wbcr_clearfy_configurate' ) {
					require(WCL_PLUGIN_DIR . '/admin/ajax/configurate.php');
				}

				if( $_REQUEST['action'] == 'wbcr_clearfy_import_settings' ) {
					require(WCL_PLUGIN_DIR . '/admin/ajax/import-settings.php');
				}

				//if( $_REQUEST['action'] == 'wbcr-clearfy-activate-external-addon' ) {
				require(WCL_PLUGIN_DIR . '/admin/ajax/install-addons.php');
				require(WCL_PLUGIN_DIR . '/admin/ajax/update-package.php');
				//}
			}

			require_once(WCL_PLUGIN_DIR . '/admin/boot.php');
			
			require_once(WCL_PLUGIN_DIR . '/includes/classes/class.licensing.php');
			require_once(WCL_PLUGIN_DIR . '/includes/classes/class.package.php');

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
			/*if( is_admin() ) {
				$this->registerPages();
			}*/

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

		/**
		 * Allows you to get a button to install the plugin component
		 *
		 * @param $component_type
		 * @param $slug
		 * @return WCL_InstallPluginsButton
		 */
		public function getInstallComponentsButton($component_type, $slug)
		{
			require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';

			return new WCL_InstallPluginsButton($component_type, $slug);
		}

		/**
		 * Allows you to get a button to delete the plugin component
		 *
		 * @param $component_type
		 * @param $slug
		 * @return WCL_InstallPluginsButton
		 */
		public function getDeleteComponentsButton($component_type, $slug)
		{
			require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';
			require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.delete-plugins-button.php';

			return new WCL_DeletePluginsButton($component_type, $slug);
		}

		/**
		 * Get a link to the official website of the developer
		 *
		 * @return string|null
		 */
		public function getAuthorSiteUrl()
		{
			if( get_locale() == 'ru_RU' ) {
				return $this->app()->getPluginInfoAttr('author_ru_site_url');
			}

			return $this->app()->getPluginInfoAttr('author_site_url');
		}

		/**
		 * Get a link to the official website of the developer
		 *
		 * @param string $page - page address
		 * @param string $utm_content - from which page or part of the plugin user moved to the site
		 * @return string
		 */
		public function getAuthorSitePageUrl($page, $utm_content = null)
		{
			$build_url = $this->getAuthorSiteUrl() . '/' . $page . '/?utm_source=wordpress.org&utm_campaign=' . $this->getPluginName();

			if( !empty($utm_content) ) {
				$build_url .= '&utm_content=' . $utm_content;
			}

			return $build_url;
		}
	}
