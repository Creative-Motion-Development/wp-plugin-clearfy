<?php
/**
 * Clearfy core class
 *
 * @author        Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 19.02.2018, Webcraftic
 * @version       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
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
	public function __construct( $plugin_path, $data ) {
		self::$app = $this;
		parent::__construct( $plugin_path, $data );

		// Freemius
		require_once( WCL_PLUGIN_DIR . '/includes/freemius/class.storage.php' );

		require_once( WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-entity.php' );
		require_once( WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-scope-entity.php' );
		require_once( WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-user.php' );
		require_once( WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-site.php' );
		require_once( WCL_PLUGIN_DIR . '/includes/freemius/entities/class.wcl-fs-plugin-license.php' );

		require_once( WCL_PLUGIN_DIR . '/includes/freemius/sdk/FreemiusWordPress.php' );

		require_once( WCL_PLUGIN_DIR . '/includes/classes/exceptions/class.license-exception.php' );
		require_once( WCL_PLUGIN_DIR . '/includes/classes/class.licensing.php' );
		require_once( WCL_PLUGIN_DIR . '/includes/classes/class.package.php' );

		if ( is_admin() ) {
			require_once( WCL_PLUGIN_DIR . '/admin/includes/classes/class.option.php' );
			require_once( WCL_PLUGIN_DIR . '/admin/includes/classes/class.group.php' );

			require_once( WCL_PLUGIN_DIR . '/admin/activation.php' );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				require( WCL_PLUGIN_DIR . '/admin/ajax/configurate.php' );
				require( WCL_PLUGIN_DIR . '/admin/ajax/import-settings.php' );
				require( WCL_PLUGIN_DIR . '/admin/ajax/install-addons.php' );
				require( WCL_PLUGIN_DIR . '/admin/ajax/update-package.php' );
				require( WCL_PLUGIN_DIR . '/admin/ajax/check-license.php' );
			}

			require_once( WCL_PLUGIN_DIR . '/admin/includes/compatibility.php' );
			require_once( WCL_PLUGIN_DIR . '/admin/boot.php' );

			$this->register_activator();
		}

		$this->global_scripts();

		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );
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
	public static function app() {
		return self::$app;
	}


	/**
	 * Выполняет php сценарии, когда все Wordpress плагины будут загружены
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.0
	 * @throws \Exception
	 */
	public function plugins_loaded() {
		if ( is_admin() ) {
			$this->register_pages();
		}

		require_once( WCL_PLUGIN_DIR . '/includes/classes/class.configurate-advanced.php' );
		new WCL_ConfigAdvanced( $this );
	}

	/**
	 * Исключаем загрузку отключенных компонентов плагина
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 * @return array
	 */
	public function get_load_plugin_components() {
		$load_components = parent::get_load_plugin_components();

		$deactivate_components = $this->getPopulateOption( 'deactive_preinstall_components', [] );

		if ( ! empty( $deactivate_components ) ) {
			foreach ( (array) $load_components as $component_ID => $component ) {
				if ( in_array( $component_ID, $deactivate_components ) ) {
					unset( $load_components[ $component_ID ] );
				}
			}
		}

		return $load_components;
	}

	/**
	 * Регистрируем активатор плагина
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.0
	 */
	protected function register_activator() {
		include_once( WCL_PLUGIN_DIR . '/admin/activation.php' );
		$this->registerActivation( 'WCL_Activation' );
	}

	//public function setAddons() {
	//$addons = [];

	/**
	 * Include plugin components
	 */

	/*require_once( WCL_PLUGIN_DIR . '/includes/classes/class.package.php' );

	if ( ! defined( 'WCL_PLUGIN_DEBUG' ) || ! WCL_PLUGIN_DEBUG ) {

		$package        = WCL_Package::instance();
		$package_addons = $package->getActivedAddons();

		if ( ! empty( $package_addons ) ) {
			$incompatible_addons = [];

			foreach ( $package_addons as $addon_slug => $addon ) {
				$base_dir = $addon[1];

				if ( ! empty( $base_dir ) && file_exists( $base_dir ) ) {
					$addon_info = get_file_data( $base_dir, [
						'Name'             => 'Plugin Name',
						//'Version' => 'Version',
						'FrameworkVersion' => 'Framework Version',
					], 'plugin' );

					if ( ! isset( $addon_info['FrameworkVersion'] ) || ( rtrim( trim( $addon_info['FrameworkVersion'] ) ) != 'FACTORY_000_VERSION' ) ) {
						$incompatible_addons[ $addon_slug ] = [
							'title' => $addon_info['Name']
						];
					} else {
						$addons[ $addon_slug ] = $addon;
					}
				}
			}
			if ( ! empty( $incompatible_addons ) ) {
				add_filter( 'wbcr/factory/admin_notices', function ( $notices, $plugin_name ) use ( $incompatible_addons ) {
					if ( $plugin_name != WCL_Plugin::app()->getPluginName() ) {
						return $notices;
					}

					$notice_text = '<p>' . __( 'Some components of Clearfy were suspended', 'clearfy' ) . ':</p><ul style="padding-left:30px; list-style: circle">';
					foreach ( $incompatible_addons as $addon ) {
						$notice_text .= '<li>' . sprintf( __( 'Component %s is not compatible with the current version of the plugin Clearfy, you must update the component to the latest version.', 'clearfy' ), $addon['title'] ) . '</li>';
					}
					$update_components_url = wp_nonce_url( $this->getPluginPageUrl( 'components', [ 'action' => 'force-update-components' ] ), 'force_update_componetns' );
					$notice_text           .= '</ul><p><a href="' . $update_components_url . '" class="button">' . __( 'Click here to update the components', 'clearfy' ) . '</a></p>';

					$notices[] = [
						'id'              => 'clearfy_component_is_not_compatibility',
						'type'            => 'error',
						'dismissible'     => false,
						'dismiss_expires' => 0,
						'text'            => $notice_text
					];

					return apply_filters( 'wbcr_clearfy_admin_notices', $notices );
				}, 10, 2 );
			}
		}
		//$addons = array_merge($addons, $package_addons);*/
	//}
	//$this->loadAddons( $addons );
	//}

	/**
	 * Регистрирует классы страниц в плагине
	 *
	 * Мы указываем плагину, где найти файлы страниц и какое имя у их класса. Чтобы плагин
	 * выполнил подключение классов страниц. После регистрации, страницы будут доступные по url
	 * и в меню боковой панели администратора. Регистрируемые страницы будут связаны с текущим плагином
	 * все операции выполняемые внутри классов страниц, имеют отношение только текущему плагину.
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @throws \Exception
	 */
	private function register_pages() {
		require_once( WCL_PLUGIN_DIR . '/admin/pages/class-page.php' );

		try {
			$this->registerPage( 'WCL_QuickStartPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-quick-start.php' );
			$this->registerPage( 'WCL_AdvancedPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-advanced.php' );
			$this->registerPage( 'WCL_PerformancePage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-performance.php' );
			$this->registerPage( 'WCL_PerformanceGooglePage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-performance-google.php' );
			$this->registerPage( 'WCL_ComponentsPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-components.php' );
			$this->registerPage( 'WCL_SeoPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-seo.php' );
			$this->registerPage( 'WCL_DoublePagesPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-seo-double-pages.php' );
			$this->registerPage( 'WCL_DefencePage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-defence.php' );
			$this->registerPage( 'WCL_LicensePage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-license.php' );

			if ( $this->isActivateComponent( 'widget_tools' ) ) {
				$this->registerPage( 'WCL_WidgetsPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-widgets.php' );
			}

			$this->registerPage( 'WCL_ClearfySettingsPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-clearfy-settings.php' );

			if ( ! defined( 'WIO_PLUGIN_ACTIVE' ) ) {
				$this->registerPage( 'WCL_ImageOptimizationPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-image-optimization.php' );
			}

			if ( ! defined( 'WHLP_PLUGIN_ACTIVE' ) ) {
				$this->registerPage( 'WCL_HideLoginPage', WCL_PLUGIN_DIR . '/admin/pages/class-pages-hide-login-page.php' );
			}
		} catch( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}
	}

	/**
	 * Выполняет глобальные php сценарии
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.0
	 */
	private function global_scripts() {

		require_once( WCL_PLUGIN_DIR . '/includes/boot.php' );

		require_once( WCL_PLUGIN_DIR . '/includes/classes/class.configurate-performance.php' );
		require_once( WCL_PLUGIN_DIR . '/includes/classes/class.configurate-google-performance.php' );
		require_once( WCL_PLUGIN_DIR . '/includes/classes/class.configurate-privacy.php' );
		require_once( WCL_PLUGIN_DIR . '/includes/classes/class.configurate-security.php' );
		require_once( WCL_PLUGIN_DIR . '/includes/classes/class.configurate-seo.php' );

		new WCL_ConfigPerformance( $this );
		new WCL_ConfigGooglePerformance( $this );
		new WCL_ConfigPrivacy( $this );
		new WCL_ConfigSecurity( $this );
		new WCL_ConfigSeo( $this );
	}

	/**
	 * @return bool
	 */
	public function currentUserCan() {
		$permission = $this->isNetworkActive() ? 'manage_network' : 'manage_options';

		return current_user_can( $permission );
	}

	/**
	 * @param string $component_name
	 *
	 * @return bool
	 */
	public function isActivateComponent( $component_name ) {
		if ( ! is_string( $component_name ) ) {
			return false;
		}

		$deactivate_components = $this->getPopulateOption( 'deactive_preinstall_components', [] );

		if ( ! is_array( $deactivate_components ) ) {
			$deactivate_components = [];
		}

		if ( $deactivate_components && in_array( $component_name, $deactivate_components ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $component_name
	 *
	 * @return bool
	 */
	public function deactivateComponent( $component_name ) {
		if ( ! $this->isActivateComponent( $component_name ) ) {
			return true;
		}

		do_action( 'wbcr_clearfy_pre_deactivate_component', $component_name );

		$deactivate_components = $this->getPopulateOption( 'deactive_preinstall_components', [] );

		if ( ! empty( $deactivate_components ) && is_array( $deactivate_components ) ) {
			$deactivate_components[] = $component_name;
		} else {
			$deactivate_components   = [];
			$deactivate_components[] = $component_name;
		}

		$this->updatePopulateOption( 'deactive_preinstall_components', $deactivate_components );

		do_action( 'wbcr_clearfy_deactivated_component', $component_name );

		return true;
	}

	/**
	 * @param string $component_name
	 *
	 * @return bool
	 */
	public function activateComponent( $component_name ) {
		if ( $this->isActivateComponent( $component_name ) ) {
			return true;
		}

		do_action( 'wbcr_clearfy_pre_activate_component', $component_name );

		$deactivate_components = $this->getPopulateOption( 'deactive_preinstall_components', [] );

		if ( ! empty( $deactivate_components ) && is_array( $deactivate_components ) ) {
			$index = array_search( $component_name, $deactivate_components );
			unset( $deactivate_components[ $index ] );
		}

		if ( empty( $deactivate_components ) ) {
			$this->deletePopulateOption( 'deactive_preinstall_components' );
		} else {
			$this->updatePopulateOption( 'deactive_preinstall_components', $deactivate_components );
		}

		//do_action('wbcr/clearfy/activated_component', $component_name);

		return true;
	}

	/**
	 * Allows you to get a button to install the plugin component
	 *
	 * @param $component_type
	 * @param $slug
	 *
	 * @return WCL_InstallPluginsButton
	 */
	public function getInstallComponentsButton( $component_type, $slug ) {
		require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';

		return new WCL_InstallPluginsButton( $component_type, $slug );
	}

	/**
	 * Allows you to get a button to delete the plugin component
	 *
	 * @param $component_type
	 * @param $slug
	 *
	 * @return WCL_InstallPluginsButton
	 */
	public function getDeleteComponentsButton( $component_type, $slug ) {
		require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.install-plugins-button.php';
		require_once WCL_PLUGIN_DIR . '/admin/includes/classes/class.delete-plugins-button.php';

		return new WCL_DeletePluginsButton( $component_type, $slug );
	}

	/**
	 * Возвращает класс для работы с лицензией
	 *
	 * @return WCL_Licensing
	 */
	public function getLicense() {
		return WCL_Licensing::instance();
	}
}
