<?php
/**
 * Clearfy core class
 *
 * @author        Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 19.02.2018, Webcraftic
 * @version       1.0
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class WCL_Plugin extends Wbcr_Factory000_Plugin {

	/**
	 * @see self::app()
	 * @var Wbcr_Factory000_Plugin
	 */
	private static $app;


	/**
	 * Конструктор
	 *
	 * Применяет конструктор родительского класса и записывает экземпляр текущего класса в свойство $app.
	 * Подробнее о свойстве $app см. self::app()
	 *
	 * @param string $plugin_path
	 * @param array  $data
	 *
	 * @throws Exception
	 */
	public function __construct($plugin_path, $data)
	{
		self::$app = $this;
		parent::__construct($plugin_path, $data);

		require_once(WCL_PLUGIN_DIR . '/includes/helpers.php');
		require_once(WCL_PLUGIN_DIR . '/includes/classes/class.licensing.php');

		if( is_admin() ) {
			if( is_plugin_active('wp-rocket/wp-rocket.php') ) {
				require_once(WCL_PLUGIN_DIR . '/includes/classes/3rd-party/boot.php');
			}
			require_once(WCL_PLUGIN_DIR . '/admin/includes/classes/class.option.php');
			require_once(WCL_PLUGIN_DIR . '/admin/includes/classes/class.group.php');

			require_once(WCL_PLUGIN_DIR . '/admin/activation.php');

			if( defined('DOING_AJAX') && DOING_AJAX ) {
				require(WCL_PLUGIN_DIR . '/admin/ajax/configurate.php');
				require(WCL_PLUGIN_DIR . '/admin/ajax/google-page-speed.php');
				require(WCL_PLUGIN_DIR . '/admin/ajax/import-settings.php');
			}

			require_once(WCL_PLUGIN_DIR . '/admin/includes/compatibility.php');
			require_once(WCL_PLUGIN_DIR . '/admin/boot.php');

			$this->register_activator();
		}

		$this->global_scripts();

		add_action('plugins_loaded', [$this, 'plugins_loaded']);
	}

	/**
	 * Статический метод для быстрого доступа к интерфейсу плагина.
	 *
	 * Позволяет разработчику глобально получить доступ к экземпляру класса плагина в любом месте
	 * плагина, но при этом разработчик не может вносить изменения в основной класс плагина.
	 *
	 * Используется для получения настроек плагина, информации о плагине, для доступа к вспомогательным
	 * классам.
	 *
	 * @return \Wbcr_Factory000_Plugin|\WCL_Plugin
	 */
	public static function app()
	{
		return self::$app;
	}

	/**
	 * Метод проверяет активацию премиум плагина и наличие действующего лицензионнного ключа
	 *
	 * @return bool
	 */
	public function is_premium()
	{
		if( $this->premium->is_active() && $this->premium->is_activate() && $this->premium->is_install_package() ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Выполняет php сценарии, когда все Wordpress плагины будут загружены
	 *
	 * @throws \Exception
	 * @since  1.0.0
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	public function plugins_loaded()
	{
		if( is_admin() ) {
			$this->register_pages();
		}

		require_once(WCL_PLUGIN_DIR . '/includes/classes/class.configurate-advanced.php');
		new WCL_ConfigAdvanced($this);
	}

	/**
	 * Исключаем загрузку отключенных компонентов плагина
	 *
	 * @return array
	 * @since  1.6.0
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	public function get_load_plugin_components()
	{
		$load_components = parent::get_load_plugin_components();

		$deactivate_components = $this->getPopulateOption('deactive_preinstall_components', []);

		if( !empty($deactivate_components) ) {
			foreach((array)$load_components as $component_ID => $component) {
				if( in_array($component_ID, $deactivate_components) ) {
					unset($load_components[$component_ID]);
				}
			}
		}

		if( is_plugin_active('gonzales/gonzales.php') ) {
			unset($load_components['assets_manager']);
		}

		if( $this->premium->is_install_package() ) {
			$package = $this->premium->get_package_data();
			if( version_compare($package['version'], "", "<") ) {
				//...
			}
		}

		// Выполнить код до загрузки и инициализации компонентов
		// ----------------------------------------------------------
		if( is_plugin_active('wp-rocket/wp-rocket.php') ) {
			$this->deactivateComponent('cache');

			require_once(WCL_PLUGIN_DIR . '/includes/classes/3rd-party/class-base.php');
			require_once(WCL_PLUGIN_DIR . '/includes/classes/3rd-party/plugins/class-wp-rocket.php');

			$wp_rocket_no_conflict = new \Clearfy\ThirdParty\Wp_Rocket();
			$wp_rocket_no_conflict->disable_clearfy_options();
		}

		//-----------------------------------------------------------

		return $load_components;
	}

	/**
	 * Регистрируем активатор плагина
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.0
	 */
	protected function register_activator()
	{
		include_once(WCL_PLUGIN_DIR . '/admin/activation.php');
		$this->registerActivation('WCL_Activation');
	}

	/**
	 * Регистрирует классы страниц в плагине
	 *
	 * Мы указываем плагину, где найти файлы страниц и какое имя у их класса. Чтобы плагин
	 * выполнил подключение классов страниц. После регистрации, страницы будут доступные по url
	 * и в меню боковой панели администратора. Регистрируемые страницы будут связаны с текущим плагином
	 * все операции выполняемые внутри классов страниц, имеют отношение только текущему плагину.
	 *
	 * @throws \Exception
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	private function register_pages()
	{
		require_once(WCL_PLUGIN_DIR . '/admin/pages/class-page.php');

		try {
			$this->registerPage('WCL_Setup', WCL_PLUGIN_DIR . '/admin/pages/setup/class-pages-setup.php');
			$this->registerPage('WCL_QuickStartPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-quick-start.php');
			$this->registerPage('WCL_AdvancedPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-advanced.php');
			$this->registerPage('WCL_PerformancePage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-performance.php');

			//$this->registerPage('WCL_PerformanceGooglePage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-performance-google.php');
			$this->registerPage('WCL_ComponentsPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-components.php');
			$this->registerPage('WCL_SeoPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-seo.php');
			$this->registerPage('WCL_DoublePagesPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-seo-double-pages.php');
			$this->registerPage('WCL_DefencePage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-defence.php');

			if( !defined('WTITAN_PLUGIN_ACTIVE') ) {
				$this->registerPage('WCL_TitanSecurityPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-defence-titan.php');
			}

			if( defined('WRIO_PLUGIN_ACTIVE') && !wrio_is_clearfy_license_activate() ) {
				$this->registerPage('WCL_ComponentsLicensePage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-components-license.php');
			}

			if( !WCL_Plugin::app()->getPopulateOption('whitelabel_hide_license_page') ) {
				$this->registerPage('WCL_LicensePage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-license.php');
			}

			if( $this->isActivateComponent('widget_tools') ) {
				$this->registerPage('WCL_WidgetsPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-widgets.php');
			}

			$this->registerPage('WCL_ClearfySettingsPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-clearfy-settings.php');

			if( !defined('WIO_PLUGIN_ACTIVE') ) {
				$this->registerPage('WCL_ImageOptimizationPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-image-optimization.php');
			}

			if( !defined('WHLP_PLUGIN_ACTIVE') ) {
				$this->registerPage('WCL_HideLoginPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-hide-login-page.php');
			}
		} catch( Exception $e ) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Выполняет глобальные php сценарии
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.0
	 */
	private function global_scripts()
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

	/**
	 * @return bool
	 */
	public function currentUserCan()
	{
		$permission = $this->isNetworkActive() ? 'manage_network' : 'manage_options';

		return current_user_can($permission);
	}

	/**
	 * @param string $component_name
	 *
	 * @return bool
	 */
	public function isActivateComponent($component_name)
	{
		return $this->is_activate_component($component_name);
	}

	/**
	 * @param string $component_name
	 *
	 * @return bool
	 */
	public function deactivateComponent($component_name)
	{
		if( !$this->is_activate_component($component_name) ) {
			return true;
		}

		do_action('wbcr_clearfy_pre_deactivate_component', $component_name);

		$this->deactivate_component($component_name);

		do_action('wbcr_clearfy_deactivated_component', $component_name);

		return true;
	}

	/**
	 * @param string $component_name
	 *
	 * @return bool
	 */
	public function activateComponent($component_name)
	{
		if( $this->is_activate_component($component_name) ) {
			return true;
		}

		do_action('wbcr_clearfy_pre_activate_component', $component_name);

		return $this->activate_component($component_name);
	}

	/**
	 * Allows you to get a button to install the plugin component
	 *
	 * @param $component_type
	 * @param $slug
	 * param $premium
	 *
	 * @return \WBCR\Factory_000\Components\Install_Button
	 */
	public function getInstallComponentsButton($component_type, $slug)
	{
		return $this->get_install_component_button($component_type, $slug);
	}

	/**
	 * Allows you to get a button to delete the plugin component
	 *
	 * @param $component_type
	 * @param $slug
	 *
	 * @return \WBCR\Factory_000\Components\Delete_Button
	 */
	public function getDeleteComponentsButton($component_type, $slug)
	{
		return $this->get_delete_component_button($component_type, $slug);
	}
}
