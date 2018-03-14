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
			
			if( onp_build('premium') ) {
				if( $this->isActivateComponent('hide_my_wp') ) {
					$addons['hide_my_wp'] = array(
						'WHM_Plugin',
						WCL_PLUGIN_DIR . '/components/hide-my-wp/hide-my-wp.php'
					);
				}
			}

			$is_cyrilic = in_array(get_locale(), array('ru_RU', 'bel', 'kk', 'uk', 'bg', 'bg_BG', 'ka_GE'));

			// This module is for Cyrillic users only, for other users it should be disabled
			if( $this->isActivateComponent('cyrlitera') && $is_cyrilic ) {
				$addons['cyrlitera'] = array(
					'WCTR_Plugin',
					WCL_PLUGIN_DIR . '/components/cyrlitera/cyrlitera.php'
				);
			}

			if( $this->isActivateComponent('disable_notices') ) {
				$addons['disable_admin_notices'] = array(
					'WDN_Plugin',
					WCL_PLUGIN_DIR . '/components/disable-admin-notices/disable-admin-notices.php'
				);
			}

			if( $this->isActivateComponent('updates_manager') ) {
				$addons['updates_manager'] = array(
					'WUP_Plugin',
					WCL_PLUGIN_DIR . '/components/updates-manager/webcraftic-updates-manager.php'
				);
			}

			if( $this->isActivateComponent('comments_tools') ) {
				$addons['comments_plus'] = array(
					'WCM_Plugin',
					WCL_PLUGIN_DIR . '/components/comments-plus/comments-plus.php'
				);
			}

			if( $this->isActivateComponent('asset_manager') ) {
				$addons['gonzales'] = array(
					'WGZ_Plugin',
					WCL_PLUGIN_DIR . '/components/assets-manager/gonzales.php'
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

			$this->initActivation();
			$this->registerPages();
		}

		private function registerPages()
		{
			$this->registerPage('WCL_QuickStartPage', WCL_PLUGIN_DIR . '/admin/pages/quick-start.php');
			$this->registerPage('WCL_AdvancedPage', WCL_PLUGIN_DIR . '/admin/pages/advanced.php');
			$this->registerPage('WCL_PerformancePage', WCL_PLUGIN_DIR . '/admin/pages/performance.php');
			$this->registerPage('WCL_PerformanceGooglePage', WCL_PLUGIN_DIR . '/admin/pages/performance-google.php');
			$this->registerPage('WCL_PerformanceHtmlMinifyPage', WCL_PLUGIN_DIR . '/admin/pages/performance-html-minify.php');
			$this->registerPage('WCL_ComponentsPage', WCL_PLUGIN_DIR . '/admin/pages/components.php');
			$this->registerPage('WCL_SeoPage', WCL_PLUGIN_DIR . '/admin/pages/seo.php');
			$this->registerPage('WCL_DoublePagesPage', WCL_PLUGIN_DIR . '/admin/pages/seo-double-pages.php');
			$this->registerPage('WCL_DefencePage', WCL_PLUGIN_DIR . '/admin/pages/defence.php');
			$this->registerPage('WCL_PrivacyContentPage', WCL_PLUGIN_DIR . '/admin/pages/defence-privacy-code.php');

			if( $this->isActivateComponent('widget_tools') ) {
				$this->registerPage('WCL_WidgetsPage', WCL_PLUGIN_DIR . '/admin/pages/widgets.php');
			}
		}

		private function globalScripts()
		{
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
			require_once(WCL_PLUGIN_DIR . '/includes/classes/class.configurate-advanced.php');
			new WCL_ConfigAdvanced($this);
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
